<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WantedPost;

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
            abort(403);
        }

        $validated = $request->validate([
            'gear_id'        => 'required|exists:gears,id',
            'quantity'       => 'required|integer|min:1',
            'price_per_unit' => 'required|integer|min:1',
        ]);

        $inventory = Inventory::where('user_id', auth()->id())
            ->where('gear_id', $validated['gear_id'])
            ->first();

        if (! $inventory || $inventory->quantity < $validated['quantity']) {
            return back()
                ->withErrors(['quantity' => 'You don\'t own that many.'])
                ->withInput();
        }

        Listing::create([
            'merchant_id'    => auth()->id(),
            'gear_id'        => $validated['gear_id'],
            'quantity'       => $validated['quantity'],
            'price_per_unit' => $validated['price_per_unit'],
            'status'         => 'active',
        ]);

        return redirect('/marketplace')
            ->with('success', '🏪 Listing created! It\'s live on the marketplace.');
    }

    public function buy(Request $request, $id)
    {
        $listing = Listing::with('gear', 'merchant')->findOrFail($id);
        $buyer   = auth()->user();

        if (! $listing->isActive()) {
            return back()->withErrors(['buy' => 'This listing is no longer available.']);
        }

        if ($listing->merchant_id === $buyer->id) {
            return back()->withErrors(['buy' => 'You cannot buy your own listing.']);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $listing->quantity,
        ]);

        $qty   = $validated['quantity'];
        $total = $qty * $listing->price_per_unit;

        if (! $buyer->canAfford($total)) {
            return back()->withErrors([
                'buy' => "You need ₱ {$total} ecto. You have {$buyer->ecto}."
            ]);
        }

        $merchantInventory = Inventory::where('user_id', $listing->merchant_id)
            ->where('gear_id', $listing->gear_id)
            ->first();

        if (! $merchantInventory || $merchantInventory->quantity < $qty) {
            return back()->withErrors([
                'buy' => 'The merchant no longer has enough stock. Listing may be stale.'
            ]);
        }

        DB::transaction(function () use ($buyer, $listing, $qty, $total, $merchantInventory) {
            $buyer->spendEcto($total);
            $listing->merchant->addEcto($total);

            $merchantInventory->decrement('quantity', $qty);
            if ($merchantInventory->fresh()->quantity <= 0) {
                $merchantInventory->delete();
            }

            $buyerInventory = Inventory::firstOrCreate(
                ['user_id' => $buyer->id, 'gear_id' => $listing->gear_id],
                ['quantity' => 0]
            );
            $buyerInventory->increment('quantity', $qty);

            $listing->decrement('quantity', $qty);
            if ($listing->fresh()->quantity <= 0) {
                $listing->update(['status' => 'sold_out']);
            }

            Purchase::create([
                'buyer_id'       => $buyer->id,
                'seller_id'      => $listing->merchant_id,
                'gear_id'        => $listing->gear_id,
                'quantity'       => $qty,
                'price_per_unit' => $listing->price_per_unit,
                'source'         => 'marketplace',
            ]);

            // 🔔 Notify buyer + merchant
            notify($buyer->id, 'Marketplace Purchase',
                "You bought {$qty}× {$listing->gear->name} for ₱ {$total}.",
                '/purchases', 'shopping-cart');

            notify($listing->merchant_id, 'You Made a Sale!',
                "{$buyer->name} bought {$qty}× {$listing->gear->name} for ₱ {$total}.",
                '/marketplace/mine', 'wallet');
        });

        return redirect('/marketplace')->with(
            'success',
            "🛒 Bought {$qty}× {$listing->gear->name} for ₱ {$total}!"
        );
    }

    public function mine()
    {
        if (! auth()->user()->isMerchant()) {
            abort(403);
        }

        $listings = Listing::with('gear')
            ->where('merchant_id', auth()->id())
            ->latest()
            ->get();

        return view('marketplace.mine', compact('listings'));
    }

    public function cancel($id)
    {
        $listing = Listing::findOrFail($id);

        if (! auth()->user()->isMerchant()) {
            abort(403);
        }

        if ($listing->merchant_id !== auth()->id()) {
            abort(403, 'That is not your listing.');
        }

        if ($listing->status === 'active') {
            $listing->update(['status' => 'cancelled']);
        }

        return redirect('/marketplace/mine')
            ->with('success', '🗑️ Listing cancelled.');
    }
}