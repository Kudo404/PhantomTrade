<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\RoleApplication;
use App\Models\Setting;
use App\Models\UserAchievement;
use Illuminate\Http\Request;

class AdminController extends Controller
{


        /**
     * Admin home — overview of the entire system.
     */
    public function home()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        // User stats
        $totalUsers       = \App\Models\User::count();
        $totalCivilians   = \App\Models\User::where('role', 'civilian')->count();
        $totalHunters     = \App\Models\User::where('role', 'ghost_hunter')->count();
        $totalMerchants   = \App\Models\User::where('role', 'merchant')->count();
        $totalAdmins      = \App\Models\User::where('role', 'admin')->count();

        // Economy
        $totalEcto        = \App\Models\User::sum('ecto');
        $avgEcto          = round(\App\Models\User::avg('ecto') ?? 0, 1);
        $totalSpent       = \App\Models\Purchase::get()->sum(fn ($p) => $p->quantity * $p->price_per_unit);

        // Inventory
        $totalItems       = \App\Models\Inventory::sum('quantity');

        // Marketplace
        $activeListings    = \App\Models\Listing::where('status', 'active')->count();
        $soldOutListings   = \App\Models\Listing::where('status', 'sold_out')->count();
        $cancelledListings = \App\Models\Listing::where('status', 'cancelled')->count();

        // Applications
        $pendingApps      = \App\Models\RoleApplication::where('status', 'pending')->count();
        $approvedApps     = \App\Models\RoleApplication::where('status', 'approved')->count();
        $rejectedApps     = \App\Models\RoleApplication::where('status', 'rejected')->count();

        // Transactions
        $totalPurchases   = \App\Models\Purchase::count();
        $shopPurchases    = \App\Models\Purchase::where('source', 'shop')->count();
        $marketPurchases  = \App\Models\Purchase::where('source', 'marketplace')->count();
        $totalTrades      = \App\Models\TradeProposal::where('status', 'accepted')->count();
        
        // Recent activity
        $recentApplications = \App\Models\RoleApplication::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = \App\Models\User::latest()
            ->take(5)
            ->get();

        return view('admin.home', compact(
            'totalUsers', 'totalCivilians', 'totalHunters', 'totalMerchants', 'totalAdmins',
            'totalEcto', 'avgEcto', 'totalSpent',
            'totalItems',
            'activeListings', 'soldOutListings', 'cancelledListings',
            'pendingApps', 'approvedApps', 'rejectedApps',
            'totalPurchases', 'shopPurchases', 'marketPurchases', 'totalTrades',
            'recentApplications', 'recentUsers'
        ));
    }

        /**
     * List all users with search + role filter.
     */
    public function users(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $search = $request->get('q');
        $role   = $request->get('role');

        $query = \App\Models\User::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['civilian', 'ghost_hunter', 'merchant', 'admin'])) {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        // Counts for filter chips
        $counts = [
            'all'          => \App\Models\User::count(),
            'civilian'     => \App\Models\User::where('role', 'civilian')->count(),
            'ghost_hunter' => \App\Models\User::where('role', 'ghost_hunter')->count(),
            'merchant'     => \App\Models\User::where('role', 'merchant')->count(),
            'admin'        => \App\Models\User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'search', 'role', 'counts'));
    }

    /**
     * Show a single user's detail page.
     */
    public function showUser($id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);

        // Stats
        $inventoryCount   = \App\Models\Inventory::where('user_id', $user->id)->sum('quantity');
        $purchasesCount   = \App\Models\Purchase::where('buyer_id', $user->id)->count();
        $tradesCount      = $user->completedTradesCount();
        $applications     = \App\Models\RoleApplication::where('user_id', $user->id)->latest()->get();
        $listingsCount    = \App\Models\Listing::where('merchant_id', $user->id)->count();

        return view('admin.user-detail', compact(
            'user',
            'inventoryCount',
            'purchasesCount',
            'tradesCount',
            'applications',
            'listingsCount'
        ));
    }

    /**
     * Update a user's role or hunter tier.
     */
    public function updateUser(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);

        // Don't let admins change their own role (prevents lockout)
        if ($user->id === auth()->id() && $request->has('role') && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role'        => 'sometimes|in:civilian,ghost_hunter,merchant,admin',
            'hunter_tier' => 'sometimes|in:D,C,B,A,S',
        ]);

        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        if (isset($validated['hunter_tier']) && $user->isGhostHunter()) {
            $user->hunter_tier = $validated['hunter_tier'];
        }

        $user->save();

        return redirect("/admin/users/{$user->id}")
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show the settings page.
     */
    public function settings()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $settings = [
            'merchant_trade_threshold'   => Setting::int('merchant_trade_threshold', 3),
            'hunter_purchase_threshold'  => Setting::int('hunter_purchase_threshold', 3),
            'application_cooldown_hours' => Setting::int('application_cooldown_hours', 24),
        ];

        $achievements = Achievement::ordered()->get();

        return view('admin.settings', compact('settings', 'achievements'));
    }

    /**
     * Update the settings.
     */
    public function updateSettings(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'merchant_trade_threshold'   => 'required|integer|min:1|max:100',
            'hunter_purchase_threshold'  => 'required|integer|min:1|max:100',
            'application_cooldown_hours' => 'required|integer|min:0|max:720',
        ]);

        Setting::set('merchant_trade_threshold',   $validated['merchant_trade_threshold']);
        Setting::set('hunter_purchase_threshold',  $validated['hunter_purchase_threshold']);
        Setting::set('application_cooldown_hours', $validated['application_cooldown_hours']);

        return redirect('/admin/settings')->with('success', 'Settings updated.');
    }

    /**
     * Toggle an achievement's active state.
     */
    public function toggleAchievement($id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $achievement = Achievement::findOrFail($id);
        $achievement->update(['is_active' => ! $achievement->is_active]);

        return redirect('/admin/settings')->with('success', 'Achievement updated.');
    }

    /**
     * Update an achievement's threshold.
     */
    public function updateAchievement(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'threshold' => 'required|integer|min:1|max:9999',
        ]);

        $achievement = Achievement::findOrFail($id);
        $achievement->update(['threshold' => $validated['threshold']]);

        return redirect('/admin/settings')->with('success', 'Achievement updated.');
    }

        /**
     * List all applications (with optional status filter).
     */
    public function applications(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $status = $request->get('status', 'pending'); // pending | approved | rejected | all

        $query = RoleApplication::with('user', 'reviewer')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->get();

        // Counts for tabs
        $counts = [
            'pending'  => RoleApplication::where('status', 'pending')->count(),
            'approved' => RoleApplication::where('status', 'approved')->count(),
            'rejected' => RoleApplication::where('status', 'rejected')->count(),
            'all'      => RoleApplication::count(),
        ];

        return view('admin.applications', compact('applications', 'status', 'counts'));
    }

    /**
     * View a single application in detail.
     */
    public function showApplication($id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $application = RoleApplication::with('user', 'reviewer')->findOrFail($id);

        return view('admin.application-detail', compact('application'));
    }

    /**
     * Approve an application → change user's role, grant achievement, notify.
     */
    public function approveApplication(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $application = RoleApplication::with('user')->findOrFail($id);

        if (! $application->isPending()) {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $applicant = $application->user;
        $roleLabel = $application->targetRoleLabel();

        \DB::transaction(function () use ($application, $applicant) {
            // 1. Change the user's role
            $applicant->update(['role' => $application->target_role]);

            // 2. Mark application as approved
            $application->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // 3. Unlock the corresponding achievement
            $achievementKey = $application->target_role === 'merchant'
                ? 'certified_merchant'
                : 'licensed_hunter';

            $achievement = Achievement::where('key', $achievementKey)->first();

            if ($achievement) {
                UserAchievement::firstOrCreate(
                    [
                        'user_id'        => $applicant->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'unlocked_at' => now(),
                    ]
                );
            }
        });

        // Notify the applicant
        notify(
            $applicant->id,
            "Application Approved",
            "Congratulations! You are now a certified {$roleLabel}.",
            $application->target_role === 'ghost_hunter' ? '/profile/license' : '/profile',
            $application->target_role === 'ghost_hunter' ? 'shield-check' : 'badge-check'
        );

        return redirect('/admin/applications')
            ->with('success', "Approved! {$applicant->name} is now a {$roleLabel}.");
    }

    /**
     * Reject an application with a reason.
     */
    public function rejectApplication(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application = RoleApplication::with('user')->findOrFail($id);

        if (! $application->isPending()) {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $applicant = $application->user;
        $roleLabel = $application->targetRoleLabel();

        $application->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify the applicant
        notify(
            $applicant->id,
            "Application Rejected",
            "Your {$roleLabel} application was not approved. Reason: {$validated['rejection_reason']}",
            '/applications/status',
            'x-circle'
        );

        return redirect('/admin/applications')
            ->with('success', "Rejected {$applicant->name}'s application.");
    }

}       