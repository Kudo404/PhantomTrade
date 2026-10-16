<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Inbox — list all my conversations, most recent first.
     */
    public function inbox()
    {
        $me = auth()->user();

        $conversations = Conversation::with(['userOne', 'userTwo', 'latestMessage'])
            ->where(function ($q) use ($me) {
                $q->where('user_one_id', $me->id)
                  ->orWhere('user_two_id', $me->id);
            })
            ->orderByDesc('last_message_at')
            ->get();

        return view('messages.inbox', compact('conversations'));
    }

    /**
     * Show a single conversation thread.
     */
    public function show($id)
    {
        $me = auth()->user();

        $conversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($id);

        if (! $conversation->hasUser($me)) {
            abort(403, 'You are not part of this conversation.');
        }

        // Mark all incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()->with('sender')->get();
        $other    = $conversation->otherUser($me);

        return view('messages.show', compact('conversation', 'messages', 'other'));
    }

    /**
     * Send a message to a conversation.
     */
    public function send(Request $request, $id)
    {
        $me = auth()->user();

        $conversation = Conversation::findOrFail($id);

        if (! $conversation->hasUser($me)) {
            abort(403, 'You are not part of this conversation.');
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $me->id,
            'body'            => $validated['body'],
        ]);

        // Bump conversation recency
        $conversation->update(['last_message_at' => now()]);

        // Notify the other user
        $other = $conversation->otherUser($me);
        if ($other) {
            notify(
                $other->id,
                'New Message from ' . $me->name,
                \Illuminate\Support\Str::limit($validated['body'], 60),
                '/messages/' . $conversation->id,
                'message-circle'
            );
        }

        // If AJAX request → return JSON (used by polling / live-append)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => [
                    'id'         => $message->id,
                    'body'       => $message->body,
                    'sender_id'  => $message->sender_id,
                    'sender_name'=> $me->name,
                    'time'       => $message->created_at->format('g:i A'),
                    'is_mine'    => true,
                ],
            ]);
        }

        return redirect('/messages/' . $conversation->id)
            ->with('success', 'Message sent.');
    }

    /**
     * Start (or resume) a conversation with a specific user.
     * Usage: /messages/start/5  → opens chat with user id 5
     */
    public function startWith($userId)
    {
        $me = auth()->user();

        if ((int) $userId === $me->id) {
            return redirect('/messages')
                ->with('error', 'You cannot message yourself.');
        }

        $other = User::findOrFail($userId);

        $conversation = Conversation::findOrCreateBetween($me, $other);

        return redirect('/messages/' . $conversation->id);
    }

    /**
     * Start (or resume) a conversation with the Customer Service Crew.
     * Picks the first available admin.
     */
    public function contactSupport()
    {
        $me = auth()->user();

        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            return redirect('/messages')
                ->with('error', 'No customer service crew is available right now.');
        }

        if ($admin->id === $me->id) {
            return redirect('/messages')
                ->with('error', 'You ARE the customer service crew.');
        }

        $conversation = Conversation::findOrCreateBetween($me, $admin);

        return redirect('/messages/' . $conversation->id);
    }

    /**
     * Poll endpoint — returns all messages newer than a given message id.
     * Used by the chat page to append new messages without reloading.
     */
    public function poll(Request $request, $id)
    {
        $me = auth()->user();

        $conversation = Conversation::findOrFail($id);

        if (! $conversation->hasUser($me)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $afterId = (int) $request->get('after_id', 0);

        $new = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(function ($m) use ($me) {
                return [
                    'id'          => $m->id,
                    'body'        => $m->body,
                    'sender_id'   => $m->sender_id,
                    'sender_name' => $m->sender->name,
                    'time'        => $m->created_at->format('g:i A'),
                    'is_mine'     => $m->sender_id === $me->id,
                ];
            });

        // Mark incoming as read (we're on the chat page)
        $conversation->messages()
            ->where('sender_id', '!=', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $new,
        ]);
    }
}