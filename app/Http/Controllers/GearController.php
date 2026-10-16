<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Purchase;

class GearController extends Controller
{
    // LIST — with optional ?type=trap filter
    public function index(Request $request)
    {
        $query = Gear::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $gears = $query->latest()->get();

        // For the filter buttons
        $types = ['trap', 'ward', 'anchor', 'scanner'];

        return view('gears.index', compact('gears', 'types'));
    }

    // SHOW ONE
    public function show($id)
    {
        $gear = Gear::findOrFail($id);
        return view('gears.show', compact('gear'));
    }

    // SHOW THE ADD FORM
    public function create()
    {
        $types = ['trap', 'ward', 'anchor', 'scanner'];
        return view('gears.create', compact('types'));
    }

        // Buy one unit of this gear → add to my inventory
        public function buy($id)
    {
        $gear   = Gear::findOrFail($id);
        $user   = auth()->user();
        $userId = $user->id;

        // 1) Check they can afford it
        if (! $user->canAfford($gear->ecto_cost)) {
            return redirect('/gears')
                ->withErrors(['ecto' => "You need ₱ {$gear->ecto_cost} ecto to buy that. You have {$user->ecto}."]);
        }

        // 2) Charge them
        $user->spendEcto($gear->ecto_cost);

        // 3) Add to inventory
        $inventory = Inventory::firstOrCreate(
            ['user_id' => $userId, 'gear_id' => $gear->id],
            ['quantity' => 0]
        );
        $inventory->increment('quantity');

                // 🔔 Notify the buyer
        notify(
            $userId,
            'Purchase Confirmed',
            "You bought 1× {$gear->name} for ₱ {$gear->ecto_cost}.",
            '/purchases',
            'shopping-cart'
        );

                // Log the purchase
        Purchase::create([
            'buyer_id'       => $userId,
            'seller_id'      => null,           // shop has no real seller
            'gear_id'        => $gear->id,
            'quantity'       => 1,
            'price_per_unit' => $gear->ecto_cost,
            'source'         => 'shop',
        ]);

        return redirect('/gears')
            ->with('success', "Bought 1× {$gear->name} for ₱ {$gear->ecto_cost}! Check your 📦 Inventory.");
    }



    // SAVE THE NEW GEAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:trap,ward,anchor,scanner',
            'description' => 'required|string',
            'ecto_cost'   => 'required|integer|min:0',
            'energy_cost' => 'required|integer|min:0',
        ]);

        Gear::create($validated);

        return redirect('/gears')->with('success', 'Gear added to the shop! ');
    }
}