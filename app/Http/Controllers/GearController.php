<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    // BUY ONE UNIT OF THIS GEAR
    public function buy($id)
    {
        $gear = Gear::findOrFail($id);
        $user = auth()->user();
        $userId = $user->id;

        // 1. Check affordability
        if (! $user->canAfford($gear->ecto_cost)) {
            return redirect('/gears')
                ->withErrors([
                    'ecto' => "You need ₱ {$gear->ecto_cost} ecto to buy that. You have {$user->ecto}.",
                ]);
        }

        // 2. Charge the buyer
        $user->spendEcto($gear->ecto_cost);

        // 3. Add one unit to inventory
        $inventory = Inventory::firstOrCreate(
            [
                'user_id' => $userId,
                'gear_id' => $gear->id,
            ],
            [
                'quantity' => 0,
            ]
        );

        $inventory->increment('quantity');

        // 4. Notify the buyer
        notify(
            $userId,
            'Purchase Confirmed',
            "You bought 1× {$gear->name} for ₱ {$gear->ecto_cost}.",
            '/purchases',
            'shopping-cart'
        );

        // 5. Log the purchase
        Purchase::create([
            'buyer_id' => $userId,
            'seller_id' => null,
            'gear_id' => $gear->id,
            'quantity' => 1,
            'price_per_unit' => $gear->ecto_cost,
            'source' => 'shop',
        ]);

        return redirect('/gears')
            ->with(
                'success',
                "Bought 1× {$gear->name} for ₱ {$gear->ecto_cost}! Check your 📦 Inventory."
            );
    }

    // SAVE NEW GEAR AND OPTIONAL IMAGE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:trap,ward,anchor,scanner',
            'description' => 'required|string|max:1000',
            'ecto_cost' => 'required|integer|min:0',
            'energy_cost' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Save the uploaded photo if one was provided.
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('gears', 'public');
        }

        // Remove the upload object from the model data.
        unset($validated['image']);

        Gear::create($validated);

        return redirect('/gears')
            ->with('success', 'Gear added to the shop!');
    }
}
