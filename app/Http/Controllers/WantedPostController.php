<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\User;
use App\Models\WantedPost;
use App\Models\WantedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WantedPostController extends Controller
{
    public function create()
    {
        $allGear = Gear::orderBy('name')->get();

        return view('wanted.create', compact('allGear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gear_id' => ['required', 'exists:gears,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'price_per_unit' => ['required', 'integer', 'min:1', 'max:99999'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $post = WantedPost::create([
            'user_id' => auth()->id(),
            'gear_id' => $validated['gear_id'],
            'quantity' => $validated['quantity'],
            'price_per_unit' => $validated['price_per_unit'],
            'note' => $validated['note'] ?? null,
            'status' => 'open',
        ]);

        $post->load('gear');

        $ownerIds = Inventory::where('gear_id', $post->gear_id)
            ->where('quantity', '>', 0)
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id')
            ->unique();

        foreach ($ownerIds as $userId) {
            notify(
                $userId,
                'Someone Wants ' . $post->gear->name,
                auth()->user()->name . " is looking for {$post->quantity}x at {$post->price_per_unit} ecto each.",
                '/marketplace',
                'tag'
            );
        }

        notify(
            auth()->id(),
            'Wanted Item Posted',
            "Your wanted item for {$post->quantity}x {$post->gear->name} is now live.",
            '/marketplace',
            'check-circle-2'
        );

        return redirect('/marketplace')
            ->with('success', 'Wanted item posted. Owners of that gear have been notified.');
    }

    public function respond($wantedPostId)
    {
        $post = WantedPost::with('gear', 'user')->findOrFail($wantedPostId);

        if (! $post->isOpen()) {
            return redirect('/marketplace')
                ->withErrors(['wanted' => 'This wanted item is no longer open.']);
        }

        if ((int) $post->user_id === (int) auth()->id()) {
            return redirect('/marketplace')
                ->withErrors(['wanted' => 'You cannot respond to your own ad.']);
        }

        $myInventory = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->where('gear_id', $post->gear_id)
            ->where('quantity', '>', 0)
            ->first();

        if (! $myInventory) {
            return redirect('/marketplace')->withErrors([
                'wanted' => "You do not own any {$post->gear->name} to offer.",
            ]);
        }

        return view('wanted.respond', compact('post', 'myInventory'));
    }

    public function storeResponse(Request $request, $wantedPostId)
    {
        $validated = $request->validate([
            'offered_quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'price_per_unit' => ['required', 'integer', 'min:1', 'max:99999'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $response = DB::transaction(function () use ($wantedPostId, $validated) {
            $post = WantedPost::whereKey($wantedPostId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $post->isOpen()) {
                throw ValidationException::withMessages([
                    'wanted' => 'This wanted ad is no longer open.',
                ]);
            }

            if ((int) $post->user_id === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'wanted' => 'You cannot respond to your own ad.',
                ]);
            }

            $myInventory = Inventory::where('user_id', auth()->id())
                ->where('gear_id', $post->gear_id)
                ->lockForUpdate()
                ->first();

            if (
                ! $myInventory ||
                $myInventory->quantity < $validated['offered_quantity']
            ) {
                throw ValidationException::withMessages([
                    'offered_quantity' => 'You do not own enough of that gear.',
                ]);
            }

            $existing = WantedResponse::where('wanted_post_id', $post->id)
                ->where('responder_id', auth()->id())
                ->where('status', 'pending')
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'wanted' => 'You already have a pending response to this ad.',
                ]);
            }

            return WantedResponse::create([
                'wanted_post_id' => $post->id,
                'responder_id' => auth()->id(),
                'offered_quantity' => $validated['offered_quantity'],
                'price_per_unit' => $validated['price_per_unit'],
                'note' => $validated['note'] ?? null,
                'status' => 'pending',
            ]);
        });

        $response->load('wantedPost.gear', 'wantedPost.user');

        notify(
            $response->wantedPost->user_id,
            'New Response to Your Wanted Ad',
            auth()->user()->name . " has {$response->offered_quantity}x {$response->wantedPost->gear->name} at {$response->price_per_unit} ecto each.",
            '/marketplace/wanted/mine',
            'hand'
        );

        return redirect('/marketplace')
            ->with('success', 'Response sent. ' . $response->wantedPost->user->name . ' has been notified.');
    }

    public function mine()
    {
        $myPosts = WantedPost::with('gear', 'responses.responder')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $totalPosts = $myPosts->count();
        $openPosts = $myPosts->where('status', 'open')->count();
        $pendingCount = $myPosts->sum(
            fn ($post) => $post->responses->where('status', 'pending')->count()
        );

        return view('wanted.mine', compact(
            'myPosts',
            'totalPosts',
            'openPosts',
            'pendingCount'
        ));
    }

    public function acceptResponse($responseId)
    {
        $accepted = DB::transaction(function () use ($responseId) {
            $response = WantedResponse::whereKey($responseId)
                ->lockForUpdate()
                ->firstOrFail();

            $post = WantedPost::whereKey($response->wanted_post_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $post->user_id !== (int) auth()->id()) {
                abort(403, 'This is not your wanted ad.');
            }

            if (! $response->isPending()) {
                throw ValidationException::withMessages([
                    'wanted' => 'This response is no longer pending.',
                ]);
            }

            if (! $post->isOpen()) {
                throw ValidationException::withMessages([
                    'wanted' => 'This wanted ad is already closed.',
                ]);
            }

            $qty = (int) $response->offered_quantity;

            if ($qty < 1 || $qty > $post->quantity) {
                throw ValidationException::withMessages([
                    'wanted' => 'The offered quantity exceeds the remaining quantity requested.',
                ]);
            }

            $buyer = User::whereKey($post->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            $seller = User::whereKey($response->responder_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($buyer->id === $seller->id) {
                throw ValidationException::withMessages([
                    'wanted' => 'You cannot accept a response to your own wanted ad.',
                ]);
            }

            $total = $qty * (int) $response->price_per_unit;

            if (! $buyer->canAfford($total)) {
                throw ValidationException::withMessages([
                    'wanted' => "You need {$total} ecto. You have {$buyer->ecto}.",
                ]);
            }

            $sellerInventory = Inventory::where('user_id', $seller->id)
                ->where('gear_id', $post->gear_id)
                ->lockForUpdate()
                ->first();

            if (! $sellerInventory || $sellerInventory->quantity < $qty) {
                throw ValidationException::withMessages([
                    'wanted' => 'The responder no longer owns enough of that gear.',
                ]);
            }

            $buyer->spendEcto($total);
            $seller->addEcto($total);

            $sellerInventory->decrement('quantity', $qty);

            if ($sellerInventory->fresh()->quantity <= 0) {
                $sellerInventory->delete();
            }

            $buyerInventory = Inventory::where('user_id', $buyer->id)
                ->where('gear_id', $post->gear_id)
                ->lockForUpdate()
                ->first();

            if ($buyerInventory) {
                $buyerInventory->increment('quantity', $qty);
            } else {
                Inventory::create([
                    'user_id' => $buyer->id,
                    'gear_id' => $post->gear_id,
                    'quantity' => $qty,
                ]);
            }

            $response->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            $remaining = (int) $post->quantity - $qty;

            if ($remaining === 0) {
                $post->quantity = 0;
                $post->status = 'fulfilled';
            } else {
                $post->quantity = $remaining;
            }

            $post->save();

            // Reject pending responses that cannot fit the remaining quantity.
            $pendingResponses = WantedResponse::where('wanted_post_id', $post->id)
                ->where('id', '!=', $response->id)
                ->where('status', 'pending');

            if ($remaining === 0) {
                $pendingResponses->update([
                    'status' => 'rejected',
                    'responded_at' => now(),
                ]);
            } else {
                $pendingResponses
                    ->where('offered_quantity', '>', $remaining)
                    ->update([
                        'status' => 'rejected',
                        'responded_at' => now(),
                    ]);
            }

            $post->load('gear');

            return [
                'seller_id' => $seller->id,
                'buyer_name' => $buyer->name,
                'quantity' => $qty,
                'total' => $total,
                'gear_name' => $post->gear->name,
            ];
        });

        notify(
            $accepted['seller_id'],
            'Response Accepted',
            "{$accepted['buyer_name']} accepted your offer: {$accepted['quantity']}x {$accepted['gear_name']} for {$accepted['total']} ecto.",
            '/marketplace',
            'check-circle-2'
        );

        return redirect('/marketplace/wanted/mine')
            ->with(
                'success',
                "Accepted. {$accepted['quantity']}x {$accepted['gear_name']} moved to your inventory."
            );
    }

    public function rejectResponse($responseId)
    {
        $result = DB::transaction(function () use ($responseId) {
            $response = WantedResponse::whereKey($responseId)
                ->lockForUpdate()
                ->firstOrFail();

            $post = WantedPost::whereKey($response->wanted_post_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $post->user_id !== (int) auth()->id()) {
                abort(403, 'This is not your wanted ad.');
            }

            if (! $response->isPending()) {
                throw ValidationException::withMessages([
                    'wanted' => 'This response is no longer pending.',
                ]);
            }

            $response->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);

            $post->load('gear');

            return [
                'responder_id' => $response->responder_id,
                'gear_name' => $post->gear->name,
                'poster_name' => auth()->user()->name,
            ];
        });

        notify(
            $result['responder_id'],
            'Response Rejected',
            $result['poster_name'] . " declined your offer for {$result['gear_name']}.",
            '/marketplace',
            'x-circle'
        );

        return redirect('/marketplace/wanted/mine')
            ->with('success', 'Response rejected.');
    }
}
