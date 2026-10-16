<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\WantedPost;
use App\Models\WantedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WantedPostController extends Controller
{
    // ── Show the "post a wanted ad" form ─────────
    public function create()
    {
        $allGear = Gear::orderBy('name')->get();

        return view('wanted.create', compact('allGear'));
    }

    // ── Save a new wanted post ───────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gear_id'        => 'required|exists:gears,id',
            'quantity'       => 'required|integer|min:1|max:999',
            'price_per_unit' => 'required|integer|min:1|max:99999',
            'note'           => 'nullable|string|max:500',
        ]);

        $post = WantedPost::create([
            'user_id'        => auth()->id(),
            'gear_id'        => $validated['gear_id'],
            'quantity'       => $validated['quantity'],
            'price_per_unit' => $validated['price_per_unit'],
            'note'           => $validated['note'] ?? null,
            'status'         => 'open',
        ]);

        // 🔔 Notify everyone who owns at least 1 of this gear (except the poster)
        $ownerIds = Inventory::where('gear_id', $post->gear_id)
            ->where('quantity', '>', 0)
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id')
            ->unique();

        foreach ($ownerIds as $uid) {
            notify(
                $uid,
                'Someone Wants ' . $post->gear->name,
                auth()->user()->name . " is looking for {$post->quantity}× at ₱ {$post->price_per_unit} each.",
                '/marketplace',
                'tag'
            );
        }

        // Confirm to the poster too
        notify(
            auth()->id(),
            'Wanted Item Posted',
            "Your wanted item for {$post->quantity}× {$post->gear->name} is now live.",
            '/marketplace',
            'check-circle-2'
        );

        return redirect('/marketplace')
            ->with('success', '🔍 Wanted item posted! Owners of that gear have been notified.');
    }

        // ── Show the "I Have This" response form ─────
    public function respond($wantedPostId)
    {
        $post = WantedPost::with('gear', 'user')->findOrFail($wantedPostId);

        if (! $post->isOpen()) {
            return redirect('/marketplace')->withErrors(['wanted' => 'This wanted item is no longer open.']);
        }

        if ($post->user_id === auth()->id()) {
            return redirect('/marketplace')->withErrors(['wanted' => 'You cannot respond to your own ad.']);
        }

        // Check they own at least 1 of the gear
        $myInventory = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->where('gear_id', $post->gear_id)
            ->where('quantity', '>', 0)
            ->first();

        if (! $myInventory) {
            return redirect('/marketplace')->withErrors([
                'wanted' => "You don't own any {$post->gear->name} to offer."
            ]);
        }

        return view('wanted.respond', compact('post', 'myInventory'));
    }

    // ── Save the response ────────────────────────
    public function storeResponse(Request $request, $wantedPostId)
    {
        $post = WantedPost::with('gear')->findOrFail($wantedPostId);

        if (! $post->isOpen()) {
            return back()->withErrors(['wanted' => 'This wanted ad is no longer open.']);
        }
        if ($post->user_id === auth()->id()) {
            return back()->withErrors(['wanted' => 'You cannot respond to your own ad.']);
        }

        $validated = $request->validate([
            'offered_quantity' => 'required|integer|min:1|max:999',
            'price_per_unit'   => 'required|integer|min:1|max:99999',
            'note'             => 'nullable|string|max:500',
        ]);

        // Must own enough
        $myInv = Inventory::where('user_id', auth()->id())
            ->where('gear_id', $post->gear_id)
            ->first();

        if (! $myInv || $myInv->quantity < $validated['offered_quantity']) {
            return back()->withErrors(['offered_quantity' => 'You don\'t own that many.'])->withInput();
        }

        // Only one pending response per post per user
        $existing = WantedResponse::where('wanted_post_id', $post->id)
            ->where('responder_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['wanted' => 'You already have a pending response to this ad.']);
        }

        $response = WantedResponse::create([
            'wanted_post_id'   => $post->id,
            'responder_id'     => auth()->id(),
            'offered_quantity' => $validated['offered_quantity'],
            'price_per_unit'   => $validated['price_per_unit'],
            'note'             => $validated['note'] ?? null,
            'status'           => 'pending',
        ]);

        // 🔔 Notify the poster
        notify(
            $post->user_id,
            'New Response to Your Wanted Ad',
            auth()->user()->name . " has {$response->offered_quantity}× {$post->gear->name} at ₱ {$response->price_per_unit} each.",
            '/marketplace/wanted/mine',
            'hand'
        );

        return redirect('/marketplace')
            ->with('success', '✋ Response sent! ' . $post->user->name . ' has been notified.');
    }

    // ── Poster's own wanted ads + responses ─────
    public function mine()
    {
        $myPosts = WantedPost::with('gear', 'responses.responder')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Summary counts
        $totalPosts    = $myPosts->count();
        $openPosts     = $myPosts->where('status', 'open')->count();
        $pendingCount  = $myPosts->sum(fn ($p) => $p->responses->where('status', 'pending')->count());

        return view('wanted.mine', compact('myPosts', 'totalPosts', 'openPosts', 'pendingCount'));
    }

    // ── Accept a response ────────────────────────
    public function acceptResponse($responseId)
    {
        $response = WantedResponse::with('wantedPost.gear', 'wantedPost.user', 'responder')->findOrFail($responseId);
        $post     = $response->wantedPost;

        if ($post->user_id !== auth()->id()) {
            abort(403, 'This is not your wanted ad.');
        }
        if (! $response->isPending()) {
            return back()->withErrors(['wanted' => 'This response is no longer pending.']);
        }
        if (! $post->isOpen()) {
            return back()->withErrors(['wanted' => 'This wanted ad is already closed.']);
        }

        $buyer   = auth()->user();            // the poster
        $seller  = $response->responder;      // the responder
        $qty     = $response->offered_quantity;
        $total   = $qty * $response->price_per_unit;

        if (! $buyer->canAfford($total)) {
            return back()->withErrors([
                'wanted' => "You need ₱ {$total} ecto. You have {$buyer->ecto}."
            ]);
        }

        // Sanity: responder still owns enough
        $sellerInv = Inventory::where('user_id', $seller->id)
            ->where('gear_id', $post->gear_id)
            ->first();

        if (! $sellerInv || $sellerInv->quantity < $qty) {
            return back()->withErrors(['wanted' => 'The responder no longer owns enough of that gear.']);
        }

        DB::transaction(function () use ($buyer, $seller, $post, $response, $sellerInv, $qty, $total) {
            // 1) Ecto: buyer → seller
            $buyer->spendEcto($total);
            $seller->addEcto($total);

            // 2) Inventory: seller → buyer
            $sellerInv->decrement('quantity', $qty);
            if ($sellerInv->fresh()->quantity <= 0) {
                $sellerInv->delete();
            }

            $buyerInv = Inventory::firstOrCreate(
                ['user_id' => $buyer->id, 'gear_id' => $post->gear_id],
                ['quantity' => 0]
            );
            $buyerInv->increment('quantity', $qty);

            // 3) Mark response accepted
            $response->update([
                'status'       => 'accepted',
                'responded_at' => now(),
            ]);

            // 4) Reduce wanted post quantity; if fully met, close it
            $remaining = $post->quantity - $qty;

            if ($remaining <= 0) {
                $post->update(['quantity' => 0, 'status' => 'fulfilled']);

                // Auto-reject other pending responses
                WantedResponse::where('wanted_post_id', $post->id)
                    ->where('id', '!=', $response->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected', 'responded_at' => now()]);
            } else {
                $post->update(['quantity' => $remaining]);
            }

            // 🔔 Notify the responder
            notify(
                $seller->id,
                'Response Accepted',
                "{$buyer->name} accepted your offer: {$qty}× {$post->gear->name} for ₱ {$total}.",
                '/marketplace',
                'check-circle-2'
            );
        });

        return redirect('/marketplace/wanted/mine')
            ->with('success', "✅ Accepted! {$qty}× {$post->gear->name} moved to your inventory.");
    }

    // ── Reject a response ────────────────────────
    public function rejectResponse($responseId)
    {
        $response = WantedResponse::with('wantedPost.gear', 'responder')->findOrFail($responseId);
        $post     = $response->wantedPost;

        if ($post->user_id !== auth()->id()) {
            abort(403, 'This is not your wanted ad.');
        }
        if (! $response->isPending()) {
            return back()->withErrors(['wanted' => 'This response is no longer pending.']);
        }

        $response->update([
            'status'       => 'rejected',
            'responded_at' => now(),
        ]);

        // 🔔 Notify the responder
        notify(
            $response->responder_id,
            'Response Rejected',
            auth()->user()->name . " declined your offer for {$post->gear->name}.",
            '/marketplace',
            'x-circle'
        );

        return redirect('/marketplace/wanted/mine')->with('success', '❌ Response rejected.');
    }
}