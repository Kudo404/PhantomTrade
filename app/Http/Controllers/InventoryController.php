<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Show the logged-in user's inventory
    public function index()
    {
        $items = Inventory::with('gear')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Calculate totals for the summary cards
        $totalItems      = $items->sum('quantity');
        $totalEctoValue  = $items->sum(fn ($i) => $i->gear->ecto_cost   * $i->quantity);
        $totalEnergyCost = $items->sum(fn ($i) => $i->gear->energy_cost * $i->quantity);

        return view('inventory.index', compact(
            'items',
            'totalItems',
            'totalEctoValue',
            'totalEnergyCost'
        ));
    }
}