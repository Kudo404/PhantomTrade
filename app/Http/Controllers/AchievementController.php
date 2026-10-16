<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Services\AchievementService;

class AchievementController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admins don't have a personal achievements page
        if ($user->isAdmin()) {
            return redirect('/admin')
                ->with('success', 'Achievements are managed from Admin Settings.');
        }

        // Auto-check + unlock any achievements the user now qualifies for
        AchievementService::checkAndUnlock($user);

        // Load all active achievements in order
        $achievements = Achievement::active()->ordered()->get()
            ->filter(fn ($a) => $a->isVisibleTo($user));

        // Build progress data for each
        $items = $achievements->map(function ($a) use ($user) {
            $progress = $a->progressFor($user);
            $unlocked = $a->isUnlockedBy($user);

            // Cap current at target (no more "3 / 1")
            $current = min($progress['current'], $progress['target']);
            $target  = $progress['target'];

            // Get unlock date if unlocked
            $unlockedAt = null;
            if ($unlocked) {
                $ua = $a->userAchievements()->where('user_id', $user->id)->first();
                $unlockedAt = $ua?->unlocked_at;
            }

            return [
                'achievement' => $a,
                'current'     => $current,
                'target'      => $target,
                'percent'     => $progress['percent'],
                'unlocked'    => $unlocked,
                'unlocked_at' => $unlockedAt,
            ];
        });

        // Summary counts
        $totalUnlocked = $items->where('unlocked', true)->count();
        $totalActive   = $items->count();
        $inProgress    = $items->filter(fn ($i) => ! $i['unlocked'] && $i['current'] > 0)->count();
        $locked        = $items->filter(fn ($i) => ! $i['unlocked'] && $i['current'] === 0)->count();

        return view('achievements.index', compact(
            'items',
            'totalUnlocked',
            'totalActive',
            'inProgress',
            'locked'
        ));
    }
}