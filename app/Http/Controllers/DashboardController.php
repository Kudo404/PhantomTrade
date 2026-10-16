<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TradeProposal;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Date range filter
        $range      = $request->get('range', '7');
        $rangeLabel = match ($range) {
            '30'    => 'Last 30 Days',
            '90'    => 'Last 90 Days',
            'all'   => 'All Time',
            default => 'Last 7 Days',
        };
        $days = in_array($range, ['7', '30', '90']) ? (int) $range : null;

        // Common stats
        $totalGear     = Gear::count();
        $totalEctoCost = Gear::sum('ecto_cost');
        $totalEnergy   = Gear::sum('energy_cost');
        $recentGear    = Gear::latest()->take(3)->get();

        $hunter = [
            'name'       => $user->name,
            'role'       => $user->roleLabel(),
            'energy'     => 100,
            'energy_max' => 100,
            'ecto'       => $user->ecto,
            'ecto_max'   => 100,
        ];

        $admin = null;

        if ($user->isAdmin()) {
            $admin = [
                'total_users'       => User::count(),
                'civilians'         => User::where('role', 'civilian')->count(),
                'hunters'           => User::where('role', 'ghost_hunter')->count(),
                'merchants'         => User::where('role', 'merchant')->count(),
                'admins'            => User::where('role', 'admin')->count(),

                'total_ecto'        => User::sum('ecto'),
                'avg_ecto'          => round(User::avg('ecto') ?? 0, 1),

                'total_inventory'   => Inventory::sum('quantity'),
                'unique_inv_rows'   => Inventory::count(),

                'active_listings'   => Listing::where('status', 'active')->count(),
                'sold_out_listings' => Listing::where('status', 'sold_out')->count(),
                'cancelled_listings'=> Listing::where('status', 'cancelled')->count(),

                'pending_proposals'  => TradeProposal::where('status', 'pending')->count(),
                'accepted_proposals' => TradeProposal::where('status', 'accepted')->count(),
                'rejected_proposals' => TradeProposal::where('status', 'rejected')->count(),

                'total_purchases'   => Purchase::count(),
                'shop_purchases'    => Purchase::where('source', 'shop')->count(),
                'market_purchases'  => Purchase::where('source', 'marketplace')->count(),
                'total_ecto_spent'  => Purchase::get()->sum(fn ($p) => $p->quantity * $p->price_per_unit),

                'recent_users'      => User::latest()->take(5)->get(),
                'recent_purchases'  => Purchase::with('buyer', 'gear')->latest()->take(5)->get(),
            ];

            // Range-filtered stats
            $purchasesQuery = Purchase::query();
            if ($days) {
                $purchasesQuery->where('created_at', '>=', Carbon::today()->subDays($days - 1));
            }
            $rangePurchases = $purchasesQuery->get();

            $admin['range_purchases']  = $rangePurchases->count();
            $admin['range_ecto_spent'] = $rangePurchases->sum(fn ($p) => $p->quantity * $p->price_per_unit);
            $admin['range_items']      = $rangePurchases->sum('quantity');
            $admin['range']            = $range;
            $admin['rangeLabel']       = $rangeLabel;

            // Chart (7 days for readability)
            $admin['purchases_chart'] = $this->purchasesPerDay(7);
        }

        return view('dashboard', compact(
            'totalGear',
            'totalEctoCost',
            'totalEnergy',
            'recentGear',
            'hunter',
            'admin',
            'rangeLabel'
        ));
    }

    private function purchasesPerDay(int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $rows = Purchase::query()
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $result[$date] = $rows[$date] ?? 0;
        }

        return $result;
    }
}