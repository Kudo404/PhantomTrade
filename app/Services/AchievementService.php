<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;

class AchievementService
{
    /**
     * Check all active achievements for a user and unlock any that qualify.
     * Fires a notification for each newly unlocked achievement.
     *
     * @return array<int, Achievement>  Newly unlocked achievements
     */
    public static function checkAndUnlock(User $user): array
    {
        $newlyUnlocked = [];

        $achievements = Achievement::active()->ordered()->get();

        foreach ($achievements as $achievement) {
            // Already unlocked? Skip.
            if ($achievement->isUnlockedBy($user)) {
                continue;
            }

            // Check progress
            $progress = $achievement->progressFor($user);

            if ($progress['current'] >= $progress['target']) {
                // Unlock it
                UserAchievement::create([
                    'user_id'        => $user->id,
                    'achievement_id' => $achievement->id,
                    'unlocked_at'    => now(),
                ]);

                // Fire a notification
                notify(
                    $user->id,
                    'Achievement Unlocked',
                    $achievement->name . ' — ' . $achievement->description,
                    '/achievements',
                    'trophy'
                );

                $newlyUnlocked[] = $achievement;
            }
        }

        return $newlyUnlocked;
    }
}