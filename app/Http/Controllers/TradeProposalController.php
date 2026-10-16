<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Listing;
use App\Models\TradeProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeProposalController extends Controller
{
    // ── Show the "propose a trade" form ─────────
    public function create($listingId)
    {
        $listing = Listing::with('gear', 'merchant')->findOrFail($listingId);

        if (! $listing->isActive()) {
            return redirect('/marketplace')->withErrors(['trade' => 'This listing is no longer available.']);
        }

        if ($listing->merchant_id === auth()->id()) {
            return redirect('/marketplace')->withErrors(['trade' => 'You cannot trade with your own listing.']);
        }

        // What the buyer owns (their offer pool)
        $myItems = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->where('quantity', '>', 0)
            ->get();

        return view('trade-proposals.create', compact('listing', 'myItems'));
    }

    // ── Save a new proposal ─────────────────────
    public function store(Request $request, $listingId)
    {
        $listing = Listing::with('gear', 'merchant')->findOrFail($listingId);

        if (! $listing->isActive()) {
            return back()->withErrors(['trade' => 'This listing is no longer available.']);
        }
        if ($listing->merchant_id === auth()->id()) {
            return back()->withErrors(['trade' => 'You cannot trade with your own listing.']);
        }

        $validated = $request->validate([
            'offered_gear_id'  => 'required|exists:gears,id',
            'offered_quantity' => 'required|integer|min:1',
            'note'             => 'nullable|string|max:500',
        ]);

        // Buyer must own enough of the offered item
        $offeredInv = Inventory::where('user_id', auth()->id())
            ->where('gear_id', $validated['offered_gear_id'])
            ->first();

        if (! $offeredInv || $offeredInv->quantity < $validated['offered_quantity']) {
            return back()
                ->withErrors(['offered_quantity' => 'You don\'t own that many.'])
                ->withInput();
        }

        // Don't allow duplicate pending proposals from the same buyer on the same listing
        $existing = TradeProposal::where('listing_id', $listing->id)
            ->where('buyer_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['trade' => 'You already have a pending proposal on this listing.']);
        }

        $proposal = TradeProposal::create([
            'listing_id'       => $listing->id,
            'buyer_id'         => auth()->id(),
            'offered_gear_id'  => $validated['offered_gear_id'],
            'offered_quantity' => $validated['offered_quantity'],
            'note'             => $validated['note'] ?? null,
            'status'           => 'pending',
        ]);

        // 🔔 Notify the merchant
        notify(
            $listing->merchant_id,
            'New Trade Proposal',
            auth()->user()->name . " wants to trade {$proposal->offered_quantity}× {$proposal->offeredGear->name} for your {$listing->gear->name}.",
            '/marketplace/proposals',
            'arrow-right-right'
        );

        // 🔔 Confirm to the buyer that their proposal was sent
        notify(
            auth()->id(),
            'Proposal Sent',
            "Your trade proposal for {$listing->gear->name} was sent to {$listing->merchant->name}. You'll be notified when they respond.",
            '/marketplace/my-proposals',
            'send'
        );

        return redirect('/marketplace')->with('success', '🔄 Trade proposal sent to ' . $listing->merchant->name . '!');
    }

    // ── Merchant's inbox of proposals ───────────
    public function inbox()
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can view this page.');
        }

        $pending = TradeProposal::with('listing.gear', 'buyer', 'offeredGear')
            ->whereHas('listing', fn ($q) => $q->where('merchant_id', auth()->id()))
            ->where('status', 'pending')
            ->latest()
            ->get();

        $recent = TradeProposal::with('listing.gear', 'buyer', 'offeredGear')
            ->whereHas('listing', fn ($q) => $q->where('merchant_id', auth()->id()))
            ->whereIn('status', ['accepted', 'rejected'])
            ->latest()
            ->take(10)
            ->get();

        return view('trade-proposals.inbox', compact('pending', 'recent'));
    }

    // ── Buyer's own sent proposals ───────────────
    public function mine()
    {
        $proposals = TradeProposal::with('listing.gear', 'listing.merchant', 'offeredGear')
            ->where('buyer_id', auth()->id())
            ->latest()
            ->get();

        // Small summary counts
        $total     = $proposals->count();
        $pending   = $proposals->where('status', 'pending')->count();
        $accepted  = $proposals->where('status', 'accepted')->count();
        $rejected  = $proposals->where('status', 'rejected')->count();

        return view('trade-proposals.mine', compact(
            'proposals',
            'total',
            'pending',
            'accepted',
            'rejected'
        ));
    }

    // ── Accept: swap the items atomically ───────
    public function accept($id)
    {
        $proposal = TradeProposal::with('listing.gear', 'buyer', 'offeredGear')->findOrFail($id);

        if (! auth()->user()->isMerchant()) {
            abort(403);
        }
        if ($proposal->listing->merchant_id !== auth()->id()) {
            abort(403, 'That proposal is not for your listing.');
        }
        if (! $proposal->isPending()) {
            return back()->withErrors(['trade' => 'This proposal is no longer pending.']);
        }
        if (! $proposal->listing->isActive()) {
            return back()->withErrors(['trade' => 'The listing is no longer available.']);
        }

        $buyer   = $proposal->buyer;
        $merchant = auth()->user();

        // Sanity checks
        $buyerInv = Inventory::where('user_id', $buyer->id)
            ->where('gear_id', $proposal->offered_gear_id)
            ->first();

        if (! $buyerInv || $buyerInv->quantity < $proposal->offered_quantity) {
            return back()->withErrors(['trade' => 'The buyer no longer owns enough of the offered item.']);
        }

        $merchantInv = Inventory::where('user_id', $merchant->id)
            ->where('gear_id', $proposal->listing->gear_id)
            ->first();

        if (! $merchantInv || $merchantInv->quantity < 1) {
            return back()->withErrors(['trade' => 'You no longer own that listing item.']);
        }

        DB::transaction(function () use ($proposal, $buyer, $merchant, $buyerInv, $merchantInv) {

            // 1) Buyer's offered item → Merchant
            $buyerInv->decrement('quantity', $proposal->offered_quantity);
            if ($buyerInv->fresh()->quantity <= 0) {
                $buyerInv->delete();
            }

            $merchantGet = Inventory::firstOrCreate(
                ['user_id' => $merchant->id, 'gear_id' => $proposal->offered_gear_id],
                ['quantity' => 0]
            );
            $merchantGet->increment('quantity', $proposal->offered_quantity);

            // 2) Merchant's listing item (1 unit) → Buyer
            $merchantInv->decrement('quantity', 1);
            if ($merchantInv->fresh()->quantity <= 0) {
                $merchantInv->delete();
            }

            $buyerGet = Inventory::firstOrCreate(
                ['user_id' => $buyer->id, 'gear_id' => $proposal->listing->gear_id],
                ['quantity' => 0]
            );
            $buyerGet->increment('quantity', 1);

            // 3) Reduce the listing's available stock
            $proposal->listing->decrement('quantity', 1);
            if ($proposal->listing->fresh()->quantity <= 0) {
                $proposal->listing->update(['status' => 'sold_out']);
            }

            // 4) Mark the proposal accepted
            $proposal->update([
                'status'       => 'accepted',
                'responded_at' => now(),
            ]);

            // 5) Auto-reject other pending proposals for this listing
            //    (since the item may now be sold out or the merchant is busy)
            TradeProposal::where('listing_id', $proposal->listing_id)
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected', 'responded_at' => now()]);

            // 🔔 Notify the buyer
            notify(
                $buyer->id,
                'Trade Accepted',
                "{$merchant->name} accepted your trade: {$proposal->offered_quantity}× {$proposal->offeredGear->name} for 1× {$proposal->listing->gear->name}.",
                '/marketplace/my-proposals',
                'check-circle-2'
            );
        });

        return redirect('/marketplace/proposals')->with('success', '✅ Trade accepted! Items swapped.');
    }

    // ── Reject ──────────────────────────────────
    public function reject($id)
    {
        $proposal = TradeProposal::with('listing', 'buyer')->findOrFail($id);

        if (! auth()->user()->isMerchant()) {
            abort(403);
        }
        if ($proposal->listing->merchant_id !== auth()->id()) {
            abort(403, 'That proposal is not for your listing.');
        }
        if (! $proposal->isPending()) {
            return back()->withErrors(['trade' => 'This proposal is no longer pending.']);
        }

        $proposal->update([
            'status'       => 'rejected',
            'responded_at' => now(),
        ]);

        // 🔔 Notify the buyer
        notify(
            $proposal->buyer_id,
            'Trade Rejected',
            auth()->user()->name . " declined your trade for {$proposal->listing->gear->name}.",
            '/marketplace/my-proposals',
            'x-circle'
        );

        return redirect('/marketplace/proposals')->with('success', '❌ Proposal rejected.');
    }
}