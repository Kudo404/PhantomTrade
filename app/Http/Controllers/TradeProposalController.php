<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Listing;
use App\Models\TradeProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TradeProposalController extends Controller
{
    public function create($listingId)
    {
        $listing = Listing::with('gear', 'merchant')->findOrFail($listingId);

        if (! $listing->isActive()) {
            return redirect('/marketplace')
                ->withErrors(['trade' => 'This listing is no longer available.']);
        }

        if ((int) $listing->merchant_id === (int) auth()->id()) {
            return redirect('/marketplace')
                ->withErrors(['trade' => 'You cannot trade with your own listing.']);
        }

        $myItems = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->where('quantity', '>', 0)
            ->get();

        return view('trade-proposals.create', compact('listing', 'myItems'));
    }

    public function store(Request $request, $listingId)
    {
        $validated = $request->validate([
            'offered_gear_id' => ['required', 'exists:gears,id'],
            'offered_quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $proposal = DB::transaction(function () use ($validated, $listingId) {
            $listing = Listing::whereKey($listingId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $listing->isActive()) {
                throw ValidationException::withMessages([
                    'trade' => 'This listing is no longer available.',
                ]);
            }

            if ((int) $listing->merchant_id === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'trade' => 'You cannot trade with your own listing.',
                ]);
            }

            $offeredInventory = Inventory::where('user_id', auth()->id())
                ->where('gear_id', $validated['offered_gear_id'])
                ->lockForUpdate()
                ->first();

            if (
                ! $offeredInventory ||
                $offeredInventory->quantity < $validated['offered_quantity']
            ) {
                throw ValidationException::withMessages([
                    'offered_quantity' => 'You do not own enough of the offered item.',
                ]);
            }

            $existing = TradeProposal::where('listing_id', $listing->id)
                ->where('buyer_id', auth()->id())
                ->where('status', 'pending')
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'trade' => 'You already have a pending proposal on this listing.',
                ]);
            }

            return TradeProposal::create([
                'listing_id' => $listing->id,
                'buyer_id' => auth()->id(),
                'offered_gear_id' => $validated['offered_gear_id'],
                'offered_quantity' => $validated['offered_quantity'],
                'note' => $validated['note'] ?? null,
                'status' => 'pending',
            ]);
        });

        $proposal->load('offeredGear', 'listing.gear', 'listing.merchant');

        notify(
            $proposal->listing->merchant_id,
            'New Trade Proposal',
            auth()->user()->name . " wants to trade {$proposal->offered_quantity}x {$proposal->offeredGear->name} for your {$proposal->listing->gear->name}.",
            '/marketplace/proposals',
            'arrow-right-right'
        );

        notify(
            auth()->id(),
            'Proposal Sent',
            "Your trade proposal for {$proposal->listing->gear->name} was sent to {$proposal->listing->merchant->name}. You will be notified when they respond.",
            '/marketplace/my-proposals',
            'send'
        );

        return redirect('/marketplace')
            ->with('success', 'Trade proposal sent to ' . $proposal->listing->merchant->name . '.');
    }

    public function inbox()
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can view this page.');
        }

        $pending = TradeProposal::with('listing.gear', 'buyer', 'offeredGear')
            ->whereHas('listing', function ($query) {
                $query->where('merchant_id', auth()->id());
            })
            ->where('status', 'pending')
            ->latest()
            ->get();

        $recent = TradeProposal::with('listing.gear', 'buyer', 'offeredGear')
            ->whereHas('listing', function ($query) {
                $query->where('merchant_id', auth()->id());
            })
            ->whereIn('status', ['accepted', 'rejected'])
            ->latest()
            ->take(10)
            ->get();

        return view('trade-proposals.inbox', compact('pending', 'recent'));
    }

    public function mine()
    {
        $proposals = TradeProposal::with(
            'listing.gear',
            'listing.merchant',
            'offeredGear'
        )
            ->where('buyer_id', auth()->id())
            ->latest()
            ->get();

        $total = $proposals->count();
        $pending = $proposals->where('status', 'pending')->count();
        $accepted = $proposals->where('status', 'accepted')->count();
        $rejected = $proposals->where('status', 'rejected')->count();

        return view('trade-proposals.mine', compact(
            'proposals',
            'total',
            'pending',
            'accepted',
            'rejected'
        ));
    }

    public function accept($id)
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can accept trade proposals.');
        }

        $result = DB::transaction(function () use ($id) {
            // Lock the proposal first, then its listing.
            $proposal = TradeProposal::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $listing = Listing::whereKey($proposal->listing_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $listing->merchant_id !== (int) auth()->id()) {
                abort(403, 'That proposal is not for your listing.');
            }

            if (! $proposal->isPending()) {
                throw ValidationException::withMessages([
                    'trade' => 'This proposal is no longer pending.',
                ]);
            }

            if (! $listing->isActive() || $listing->quantity < 1) {
                throw ValidationException::withMessages([
                    'trade' => 'The listing is no longer available.',
                ]);
            }

            $buyerInventory = Inventory::where('user_id', $proposal->buyer_id)
                ->where('gear_id', $proposal->offered_gear_id)
                ->lockForUpdate()
                ->first();

            if (
                ! $buyerInventory ||
                $buyerInventory->quantity < $proposal->offered_quantity
            ) {
                throw ValidationException::withMessages([
                    'trade' => 'The buyer no longer owns enough of the offered item.',
                ]);
            }

            /*
             * New listings reserve their stock when created.
             * Only older, unreserved listings need a merchant inventory deduction.
             */
            if (! $listing->stock_reserved) {
                $merchantInventory = Inventory::where('user_id', $listing->merchant_id)
                    ->where('gear_id', $listing->gear_id)
                    ->lockForUpdate()
                    ->first();

                if (! $merchantInventory || $merchantInventory->quantity < 1) {
                    throw ValidationException::withMessages([
                        'trade' => 'The merchant no longer has the item required by this listing.',
                    ]);
                }

                $merchantInventory->decrement('quantity', 1);

                if ($merchantInventory->fresh()->quantity <= 0) {
                    $merchantInventory->delete();
                }
            }

            // Transfer the offered gear from the buyer to the merchant.
            $buyerInventory->decrement(
                'quantity',
                $proposal->offered_quantity
            );

            if ($buyerInventory->fresh()->quantity <= 0) {
                $buyerInventory->delete();
            }

            $merchantOfferedInventory = Inventory::where('user_id', $listing->merchant_id)
                ->where('gear_id', $proposal->offered_gear_id)
                ->lockForUpdate()
                ->first();

            if ($merchantOfferedInventory) {
                $merchantOfferedInventory->increment(
                    'quantity',
                    $proposal->offered_quantity
                );
            } else {
                Inventory::create([
                    'user_id' => $listing->merchant_id,
                    'gear_id' => $proposal->offered_gear_id,
                    'quantity' => $proposal->offered_quantity,
                ]);
            }

            // Transfer one reserved listing item to the buyer.
            $buyerListedInventory = Inventory::where('user_id', $proposal->buyer_id)
                ->where('gear_id', $listing->gear_id)
                ->lockForUpdate()
                ->first();

            if ($buyerListedInventory) {
                $buyerListedInventory->increment('quantity', 1);
            } else {
                Inventory::create([
                    'user_id' => $proposal->buyer_id,
                    'gear_id' => $listing->gear_id,
                    'quantity' => 1,
                ]);
            }

            // Update the remaining listing quantity.
            $listing->quantity--;

            if ($listing->quantity <= 0) {
                $listing->quantity = 0;
                $listing->status = 'sold_out';
            }

            $listing->save();

            $proposal->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Reject competing pending proposals for this listing.
            TradeProposal::where('listing_id', $listing->id)
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'responded_at' => now(),
                ]);

            $proposal->load('offeredGear', 'listing.gear');

            return [
                'proposal' => $proposal,
                'merchant_name' => auth()->user()->name,
            ];
        });

        // Send notifications after the transaction has committed.
        $proposal = $result['proposal'];

        notify(
            $proposal->buyer_id,
            'Trade Accepted',
            $result['merchant_name'] . " accepted your trade: {$proposal->offered_quantity}x {$proposal->offeredGear->name} for 1x {$proposal->listing->gear->name}.",
            '/marketplace/my-proposals',
            'check-circle-2'
        );

        return redirect('/marketplace/proposals')
            ->with('success', 'Trade accepted. Items have been exchanged.');
    }

    public function reject($id)
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can reject trade proposals.');
        }

        $result = DB::transaction(function () use ($id) {
            // Use the same lock order as accept() to reduce race conditions.
            $proposal = TradeProposal::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $listing = Listing::whereKey($proposal->listing_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $listing->merchant_id !== (int) auth()->id()) {
                abort(403, 'That proposal is not for your listing.');
            }

            if (! $proposal->isPending()) {
                throw ValidationException::withMessages([
                    'trade' => 'This proposal is no longer pending.',
                ]);
            }

            $proposal->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);

            $proposal->load('listing.gear', 'buyer');

            return [
                'proposal' => $proposal,
                'merchant_name' => auth()->user()->name,
            ];
        });

        $proposal = $result['proposal'];

        notify(
            $proposal->buyer_id,
            'Trade Rejected',
            $result['merchant_name'] . " declined your trade for {$proposal->listing->gear->name}.",
            '/marketplace/my-proposals',
            'x-circle'
        );

        return redirect('/marketplace/proposals')
            ->with('success', 'Trade proposal rejected.');
    }
}
