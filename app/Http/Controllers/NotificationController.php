<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // JSON: unread count + latest 5
    public function index()
    {
        $userId = auth()->id();

        $unreadCount = AppNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $latest = AppNotification::where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'title'      => $n->title,
                    'body'       => $n->body,
                    'icon'       => $n->icon,
                    'url'        => $n->url,
                    'unread'     => $n->isUnread(),
                    'time_ago'   => $n->timeAgo(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latest,
        ]);
    }

    // Mark ONE as read
    public function markRead($id)
    {
        $n = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $n->markAsRead();

        return response()->json(['ok' => true]);
    }

    // Mark ALL as read
    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // Optional: clear all
    public function clearAll()
    {
        AppNotification::where('user_id', auth()->id())->delete();
        return response()->json(['ok' => true]);
    }
}