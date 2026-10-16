<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\Loadout;
use Illuminate\Http\Request;

class LoadoutController extends Controller
{
    // Show my inventory + my current loadout
    public function index()
    {
        $userId = auth()->id();

        // What I own
        $myItems = Inventory::with('gear')
            ->where('user_id', $userId)
            ->where('quantity', '>', 0)
            ->get();

        // What I've equipped
        $equippedIds = Loadout::where('user_id', $userId)
            ->pluck('gear_id')
            ->toArray();

        $equipped = Loadout::with('gear')
            ->where('user_id', $userId)
            ->get();

        // Totals
        $totalEcto   = $equipped->sum(fn ($l) => $l->gear->ecto_cost);
        $totalEnergy = $equipped->sum(fn ($l) => $l->gear->energy_cost);

        return view('loadout.index', compact(
            'myItems',
            'equippedIds',
            'equipped',
            'totalEcto',
            'totalEnergy'
        ));
    }

    // Equip a gear
    public function equip($gearId)
    {
        $userId = auth()->id();

        // Make sure they own it
        $owns = Inventory::where('user_id', $userId)
            ->where('gear_id', $gearId)
            ->where('quantity', '>', 0)
            ->exists();

        if (! $owns) {
            return back()->withErrors(['loadout' => 'You do not own that gear.']);
        }

        Loadout::firstOrCreate([
            'user_id' => $userId,
            'gear_id' => $gearId,
        ]);

        return redirect('/loadout')->with('success', '✅ Equipped!');
    }

    // Unequip a gear
    public function unequip($gearId)
    {
        Loadout::where('user_id', auth()->id())
            ->where('gear_id', $gearId)
            ->delete();

        return redirect('/loadout')->with('success', 'Unequipped.');
    }
}