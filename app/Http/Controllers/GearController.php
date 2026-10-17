<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GearController extends Controller
{
    public function index(Request $request)
    {
        $query = Gear::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $gears = $query->latest()->get();
        $types = ['trap', 'ward', 'anchor', 'scanner', 'battery'];

        return view('gears.index', compact('gears', 'types'));
    }

    public function show($id)
    {
        $gear = Gear::findOrFail($id);

        return view('gears.show', compact('gear'));
    }

    public function create()
    {
        $types = ['trap', 'ward', 'anchor', 'scanner'];

        return view('gears.create', compact('types'));
    }

    public function edit($id)
    {
        $gear = Gear::findOrFail($id);
        $types = ['trap', 'ward', 'anchor', 'scanner'];

        return view('gears.edit', compact('gear', 'types'));
    }

    public function update(Request $request, $id)
    {
        $gear = Gear::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:trap,ward,anchor,scanner,battery',
            'description' => 'required|string|max:1000',
            'ecto_cost' => 'required|integer|min:0',
            'energy_cost' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('gears', 'public');
        }

        unset($validated['image']);

        $gear->update($validated);

        return redirect('/gears')
            ->with('success', 'Gear updated successfully.');
    }

    public function checkout($id)
    {
        $gear = Gear::findOrFail($id);
        $user = auth()->user();

        return view('gears.checkout', compact('gear', 'user'));
    }

    public function buy($id)
    {
        $userId = auth()->id();

        $purchaseDetails = DB::transaction(function () use ($id, $userId) {
            $gear = Gear::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $user = User::whereKey($userId)
                ->lockForUpdate()
                ->firstOrFail();

            $price = (int) $gear->ecto_cost;

            if (! $user->canAfford($price)) {
                throw ValidationException::withMessages([
                    'ecto' => "You need ₱ {$price} ecto to buy this item. Your balance is {$user->ecto}.",
                ]);
            }

            $user->spendEcto($price);

            $inventory = Inventory::where('user_id', $user->id)
                ->where('gear_id', $gear->id)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $inventory->increment('quantity');
            } else {
                Inventory::create([
                    'user_id' => $user->id,
                    'gear_id' => $gear->id,
                    'quantity' => 1,
                ]);
            }

            Purchase::create([
                'buyer_id' => $user->id,
                'seller_id' => null,
                'gear_id' => $gear->id,
                'quantity' => 1,
                'price_per_unit' => $price,
                'source' => 'shop',
            ]);

            return [
                'user_id' => $user->id,
                'gear_name' => $gear->name,
                'price' => $price,
            ];
        });

        notify(
            $purchaseDetails['user_id'],
            'Purchase Confirmed',
            "You purchased 1 x {$purchaseDetails['gear_name']} for ₱ {$purchaseDetails['price']}.",
            '/purchases',
            'shopping-cart'
        );

        return redirect('/gears')
            ->with(
                'success',
                "Purchase successful: 1 x {$purchaseDetails['gear_name']} for ₱ {$purchaseDetails['price']}. Check your inventory."
            );
    }

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

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('gears', 'public');
        }

        unset($validated['image']);

        Gear::create($validated);

        return redirect('/gears')
            ->with('success', 'Gear added to the shop.');
    }
}
