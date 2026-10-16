<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Purchase;
use App\Models\User;
use App\Models\WantedPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarketplaceController extends Controller
{
    public function index()
    {
        $listings = Listing::with('gear', 'merchant')
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->latest()
            ->get();

        $wantedPosts = WantedPost::with('gear', 'user', 'responses')
            ->where('status', 'open')
            ->latest()
            ->get();

        return view('marketplace.index', compact('listings', 'wantedPosts'));
    }

    public function create()
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can create listings.');
        }

        $myItems = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->where('quantity', '>', 0)
            ->get();

        return view('marketplace.create', compact('myItems'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can create listings.');
        }

        $validated = $request->validate([
            'gear_id' => 'required|exists:gears,id',
            'quantity' => 'required|integer|min:1',
            'price_per_unit' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $merchantId = auth()->id();

            $inventory = Inventory::where('user_id', $merchantId)
                ->where('gear_id', $validated['gear_id'])
                ->lockForUpdate()
                ->first();

            if (
                ! $inventory ||
                $inventory->quantity < $validated['quantity']
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'You do not own enough stock to create this listing.',
                ]);
            }

            // Reserve stock by removing it from available inventory.
            $inventory->decrement('quantity', $validated['quantity']);

            if ($inventory->fresh()->quantity <= 0) {
                $inventory->delete();
            }

            Listing::create([
                'merchant_id' => $merchantId,
                'gear_id' => $validated['gear_id'],
                'quantity' => $validated['quantity'],
                'price_per_unit' => $validated['price_per_unit'],
                'status' => 'active',
                'stock_reserved' => true,
            ]);
        });

        return redirect('/marketplace')
            ->with('success', 'Listing created successfully.');
    }

    public function buy(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $buyerId = auth()->id();

        $purchaseDetails = DB::transaction(function () use (
            $id,
            $buyerId,
            $validated
        ) {
            $listing = Listing::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $listing->isActive()) {
                throw ValidationException::withMessages([
                    'buy' => 'This listing is no longer available.',
                ]);
            }

            if ((int) $listing->merchant_id === (int) $buyerId) {
                throw ValidationException::withMessages([
                    'buy' => 'You cannot buy your own listing.',
                ]);
            }

            $quantity = (int) $validated['quantity'];

            if ($quantity > $listing->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity exceeds the available listing stock.',
                ]);
            }

            $buyer = User::whereKey($buyerId)
                ->lockForUpdate()
                ->firstOrFail();

            $merchant = User::whereKey($listing->merchant_id)
                ->lockForUpdate()
                ->firstOrFail();

            $total = $quantity * $listing->price_per_unit;

            if (! $buyer->canAfford($total)) {
                throw ValidationException::withMessages([
                    'buy' => "You need ₱ {$total} ecto. Your balance is {$buyer->ecto}.",
                ]);
            }

            // Older listings may not have reserved their inventory.
            $merchantInventory = null;

            if (! $listing->stock_reserved) {
                $merchantInventory = Inventory::where(
                    'user_id',
                    $merchant->id
                )
                    ->where('gear_id', $listing->gear_id)
                    ->lockForUpdate()
                    ->first();

                if (
                    ! $merchantInventory ||
                    $merchantInventory->quantity < $quantity
                ) {
                    throw ValidationException::withMessages([
                        'buy' => 'The merchant does not have enough available stock for this listing.',
                    ]);
                }
            }

            $gearName = $listing->gear()->value('name');

            $buyer->spendEcto($total);
            $merchant->addEcto($total);

            // Only legacy, unreserved listings need another stock deduction.
            if ($merchantInventory) {
                $merchantInventory->decrement('quantity', $quantity);

                if ($merchantInventory->fresh()->quantity <= 0) {
                    $merchantInventory->delete();
                }
            }

            // Transfer the purchased items to the buyer.
            $buyerInventory = Inventory::where('user_id', $buyer->id)
                ->where('gear_id', $listing->gear_id)
                ->lockForUpdate()
                ->first();

            if ($buyerInventory) {
                $buyerInventory->increment('quantity', $quantity);
            } else {
                Inventory::create([
                    'user_id' => $buyer->id,
                    'gear_id' => $listing->gear_id,
                    'quantity' => $quantity,
                ]);
            }

            $listing->quantity -= $quantity;

            if ($listing->quantity <= 0) {
                $listing->quantity = 0;
                $listing->status = 'sold_out';
            }

            $listing->save();

            Purchase::create([
                'buyer_id' => $buyer->id,
                'seller_id' => $merchant->id,
                'gear_id' => $listing->gear_id,
                'quantity' => $quantity,
                'price_per_unit' => $listing->price_per_unit,
                'source' => 'marketplace',
            ]);

            return [
                'buyer_id' => $buyer->id,
                'buyer_name' => $buyer->name,
                'merchant_id' => $merchant->id,
                'gear_name' => $gearName,
                'quantity' => $quantity,
                'total' => $total,
            ];
        });

        // Send notifications only after the transaction commits.
        notify(
            $purchaseDetails['buyer_id'],
            'Marketplace Purchase',
            "You bought {$purchaseDetails['quantity']} x {$purchaseDetails['gear_name']} for ₱ {$purchaseDetails['total']}.",
            '/purchases',
            'shopping-cart'
        );

        notify(
            $purchaseDetails['merchant_id'],
            'Sale Completed',
            "{$purchaseDetails['buyer_name']} bought {$purchaseDetails['quantity']} x {$purchaseDetails['gear_name']} for ₱ {$purchaseDetails['total']}.",
            '/marketplace/mine',
            'wallet'
        );

        return redirect('/marketplace')->with(
            'success',
            "Purchase successful: {$purchaseDetails['quantity']} x {$purchaseDetails['gear_name']} for ₱ {$purchaseDetails['total']}."
        );
    }

    public function mine()
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can view their listings.');
        }

        $listings = Listing::with('gear')
            ->where('merchant_id', auth()->id())
            ->latest()
            ->get();

        return view('marketplace.mine', compact('listings'));
    }

    public function cancel($id)
    {
        if (! auth()->user()->isMerchant()) {
            abort(403, 'Only merchants can cancel listings.');
        }

        DB::transaction(function () use ($id) {
            $listing = Listing::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $listing->merchant_id !== (int) auth()->id()) {
                abort(403, 'That is not your listing.');
            }

            if ($listing->status !== 'active') {
                return;
            }

            // Return remaining reserved stock to the merchant.
            if ($listing->stock_reserved && $listing->quantity > 0) {
                $inventory = Inventory::where(
                    'user_id',
                    $listing->merchant_id
                )
                    ->where('gear_id', $listing->gear_id)
                    ->lockForUpdate()
                    ->first();

                if ($inventory) {
                    $inventory->increment('quantity', $listing->quantity);
                } else {
                    Inventory::create([
                        'user_id' => $listing->merchant_id,
                        'gear_id' => $listing->gear_id,
                        'quantity' => $listing->quantity,
                    ]);
                }

                $listing->stock_reserved = false;
            }

            $listing->status = 'cancelled';
            $listing->save();
        });

        return redirect('/marketplace/mine')
            ->with('success', 'Listing cancelled successfully.');
    }
}
