<?php

namespace App\Http\Controllers;

use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('gear', 'seller')
            ->where('buyer_id', auth()->id())
            ->latest()
            ->get();

        $totalSpent    = $purchases->sum(fn ($p) => $p->totalPrice());
        $totalItems    = $purchases->sum('quantity');
        $uniqueCount   = $purchases->unique('gear_id')->count();

        return view('purchases.index', compact(
            'purchases',
            'totalSpent',
            'totalItems',
            'uniqueCount'
        ));
    }
}