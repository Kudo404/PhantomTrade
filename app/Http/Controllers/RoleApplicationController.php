<?php

namespace App\Http\Controllers;

use App\Models\RoleApplication;
use App\Models\Setting;
use Illuminate\Http\Request;

class RoleApplicationController extends Controller
{
    /**
     * Merchant application form + progress check.
     */
    public function showMerchantForm()
    {
        return $this->showForm('merchant');
    }

    /**
     * Hunter application form + progress check.
     */
    public function showHunterForm()
    {
        return $this->showForm('ghost_hunter');
    }

    /**
     * Shared logic for both forms.
     */
    private function showForm(string $targetRole)
    {
        $user = auth()->user();

        // Only civilians can apply
        if (! $user->isCivilian()) {
            return redirect('/applications/status')
                ->with('error', 'You already have a role. Promotion is only available to civilians.');
        }

        // Check for existing pending application
        $pending = RoleApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect('/applications/status')
                ->with('error', 'You already have a pending application.');
        }

        // Check for recent rejection (cooldown)
        $cooldownHours = Setting::int('application_cooldown_hours', 24);
        $lastRejected = RoleApplication::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->where('reviewed_at', '>', now()->subHours($cooldownHours))
            ->latest('reviewed_at')
            ->first();

        if ($lastRejected) {
            $availableAt = $lastRejected->reviewed_at->addHours($cooldownHours);
            return redirect('/applications/status')
                ->with('error', "You can re-apply after {$availableAt->diffForHumans()}.");
        }

        // Progress thresholds
        if ($targetRole === 'merchant') {
            $threshold = Setting::int('merchant_trade_threshold', 3);
            $progress  = $user->completedTradesCount();
        } else {
            $threshold = Setting::int('hunter_purchase_threshold', 3);
            $progress  = $user->completedPurchasesCount();
        }

        $qualified = $progress >= $threshold;

        // Prefill from user's saved data
        $prefill = [
            'name'          => $user->name,
            'email'         => $user->email,
            'location'      => $user->locationLabel(),
            'latitude'      => $user->latitude,
            'longitude'     => $user->longitude,
        ];

        $view = $targetRole === 'merchant'
            ? 'applications.merchant'
            : 'applications.hunter';

        return view($view, compact(
            'user',
            'targetRole',
            'threshold',
            'progress',
            'qualified',
            'prefill'
        ));
    }

    /**
     * Submit a merchant application.
     */
    public function submitMerchant(Request $request)
    {
        return $this->submit($request, 'merchant');
    }

    /**
     * Submit a hunter application.
     */
    public function submitHunter(Request $request)
    {
        return $this->submit($request, 'ghost_hunter');
    }

    /**
     * Shared submit logic.
     */
    private function submit(Request $request, string $targetRole)
    {
        $user = auth()->user();

        if (! $user->isCivilian()) {
            return redirect('/applications/status')
                ->with('error', 'Only civilians can apply.');
        }

        // Re-check pending / cooldown (in case they resubmit)
        $pending = RoleApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return redirect('/applications/status')
                ->with('error', 'You already have a pending application.');
        }

        // Validate
        if ($targetRole === 'merchant') {
            $validated = $request->validate([
                'name'          => 'required|string|max:120',
                'contact_email' => 'required|email|max:120',
                'contact_phone' => 'required|string|max:40',
                'location_name' => 'required|string|max:120',
                'latitude'      => 'required|numeric|between:-90,90',
                'longitude'     => 'required|numeric|between:-180,180',
                'reason'        => 'nullable|string|max:500',
            ]);
        } else {
            $validated = $request->validate([
                'name'           => 'required|string|max:120',
                'hunter_name'    => 'required|string|max:120',
                'contact_email'  => 'required|email|max:120',
                'contact_phone'  => 'required|string|max:40',
                'location_name'  => 'required|string|max:120',
                'latitude'       => 'required|numeric|between:-90,90',
                'longitude'      => 'required|numeric|between:-180,180',
                'reason'         => 'nullable|string|max:500',
            ]);
        }

        // Create application
        $application = RoleApplication::create([
            'user_id'     => $user->id,
            'target_role' => $targetRole,
            'status'      => 'pending',
            'form_data'   => $validated,
        ]);

        // Notify all admins
        $roleLabel = $targetRole === 'merchant' ? 'Merchant' : 'Ghost Hunter';
        notifyAdmins(
            "New {$roleLabel} Application",
            "{$user->name} has applied to become a {$roleLabel}.",
            '/admin/applications',
            'user-plus'
        );

        // Notify the applicant (confirmation)
        notify(
            $user->id,
            'Application Submitted',
            "Your {$roleLabel} application has been sent to the admin team for review.",
            '/applications/status',
            'send'
        );

        return redirect('/applications/status')
            ->with('success', 'Application submitted! An admin will review it soon.');
    }

    /**
     * Applicant's own status page.
     */
    public function status()
    {
        $user = auth()->user();

        $applications = RoleApplication::where('user_id', $user->id)
            ->latest()
            ->get();

        // Progress toward both paths
        $merchantThreshold = Setting::int('merchant_trade_threshold', 3);
        $hunterThreshold   = Setting::int('hunter_purchase_threshold', 3);

        $merchantProgress = $user->completedTradesCount();
        $hunterProgress   = $user->completedPurchasesCount();

        $cooldownHours = Setting::int('application_cooldown_hours', 24);

        return view('applications.status', compact(
            'user',
            'applications',
            'merchantThreshold',
            'hunterThreshold',
            'merchantProgress',
            'hunterProgress',
            'cooldownHours'
        ));
    }
}