<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Loadout;
use App\Models\Purchase;
use App\Models\TradeProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Inventory stats
        $totalItems     = Inventory::where('user_id', $user->id)->sum('quantity');
        $uniqueGear     = Inventory::where('user_id', $user->id)->count();
        $inventoryValue = Inventory::with('gear')
            ->where('user_id', $user->id)
            ->get()
            ->sum(fn ($i) => ($i->gear->ecto_cost ?? 0) * $i->quantity);

        // Loadout
        $equippedCount = Loadout::where('user_id', $user->id)->count();

        // Purchases
        $purchases   = Purchase::where('buyer_id', $user->id)->get();
        $totalSpent  = $purchases->sum(fn ($p) => $p->quantity * $p->price_per_unit);
        $itemsBought = $purchases->sum('quantity');

        // Trade proposals sent
        $sentProposals     = TradeProposal::where('buyer_id', $user->id);
        $proposalsSent     = (clone $sentProposals)->count();
        $proposalsAccepted = (clone $sentProposals)->where('status', 'accepted')->count();
        $proposalsPending  = (clone $sentProposals)->where('status', 'pending')->count();

        // Merchant stats
        $activeListings         = 0;
        $totalListings          = 0;
        $proposalsReceived      = 0;
        $proposalsPendingInbox  = 0;

        if ($user->isMerchant()) {
            $totalListings  = Listing::where('merchant_id', $user->id)->count();
            $activeListings = Listing::where('merchant_id', $user->id)
                ->where('status', 'active')
                ->count();

            $proposalsReceived = TradeProposal::whereHas('listing', function ($q) use ($user) {
                $q->where('merchant_id', $user->id);
            })->count();

            $proposalsPendingInbox = TradeProposal::whereHas('listing', function ($q) use ($user) {
                $q->where('merchant_id', $user->id);
            })->where('status', 'pending')->count();
        }

        // Admin overview
        $adminOverview = null;
        if ($user->isAdmin()) {
            $adminOverview = [
                'total_users'        => \App\Models\User::count(),
                'total_ecto'         => \App\Models\User::sum('ecto'),
                'active_listings'    => Listing::where('status', 'active')->count(),
                'total_transactions' => Purchase::count(),
            ];
        }

        return view('profile.index', compact(
            'user',
            'totalItems',
            'uniqueGear',
            'inventoryValue',
            'equippedCount',
            'totalSpent',
            'itemsBought',
            'proposalsSent',
            'proposalsAccepted',
            'proposalsPending',
            'activeListings',
            'totalListings',
            'proposalsReceived',
            'proposalsPendingInbox',
            'adminOverview'
        ));
    }

    // ── Show the "set my location" form ─────────
    public function locationForm()
    {
        $user = auth()->user();
        return view('profile.location', compact('user'));
    }

    // ── Save the location ───────────────────────
    public function saveLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:120',
        ]);

        $user = auth()->user();

        // Always reverse-geocode on save — the browser value is just a preview
        $locationName = $this->reverseGeocode(
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        // Fallback to what the user/client provided if Nominatim fails
        if (empty($locationName)) {
            $locationName = $validated['location_name'] ?? null;
        }

        $user->latitude      = $validated['latitude'];
        $user->longitude     = $validated['longitude'];
        $user->location_name = $locationName;
        $user->save();

        // Refresh auth session so the next page load sees new values
        auth()->setUser($user->fresh());

        return redirect('/profile')->with('success', 'Location saved!');
    }

    /**
     * Reverse-geocode lat/lng → friendly place name via Nominatim (OSM).
     * No API key, respects fair-use policy.
     */
    private function reverseGeocode(float $lat, float $lng): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'PhantomTrade/1.0 (Laravel; contact: phantomtrade@example.test)',
                'Accept'     => 'application/json',
            ])
                ->timeout(6)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'         => 'json',
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'zoom'           => 18,
                    'addressdetails' => 1,
                ]);

            if (! $response->ok()) {
                return null;
            }

            $data = $response->json();
            if (empty($data['address'])) {
                return null;
            }

            $addr = $data['address'];

            // Most specific → least specific
            $parts = array_filter([
                $addr['neighbourhood'] ?? null,
                $addr['suburb']        ?? null,
                $addr['village']       ?? null,
                $addr['town']          ?? null,
                $addr['city']          ?? null,
                $addr['municipality']  ?? null,
                $addr['county']        ?? null,
                $addr['state']         ?? null,
                $addr['country']       ?? null,
            ]);

            // De-duplicate, keep first 3
            $parts = array_values(array_unique($parts));

            return implode(', ', array_slice($parts, 0, 3)) ?: null;

        } catch (\Throwable $e) {
            Log::warning('Reverse geocode failed: ' . $e->getMessage());
            return null;
        }
    }
}