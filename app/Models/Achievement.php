<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'type',
        'threshold',
        'target_role',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    // ── Scopes ────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Unlock check ──────────────────────────
    public function isUnlockedBy(User $user): bool
    {
        return $this->userAchievements()
            ->where('user_id', $user->id)
            ->exists();
    }

    // ── Progress ──────────────────────────────
    public function progressFor(User $user): array
    {
        $current = 0;
        $target  = $this->threshold;

        switch ($this->type) {
            case 'trades':
                $current = $user->completedTradesCount();
                break;

            case 'purchases':
                $current = $user->completedPurchasesCount();
                break;

            case 'role':
                $current = $user->role === $this->target_role ? 1 : 0;
                $target  = 1;
                break;
        }

        $percent = $target > 0 ? min(100, (int) round(($current / $target) * 100)) : 0;

        return [
            'current' => $current,
            'target'  => $target,
            'percent' => $percent,
        ];
    }

    // ── Visibility ────────────────────────────
    /**
     * Should this achievement be visible to the given user?
     * Hides "become X" candidacy achievements from non-civilians.
     */
    public function isVisibleTo(User $user): bool
    {
        if (in_array($this->key, ['merchant_candidate', 'hunter_candidate'])) {
            return $user->isCivilian();
        }

        return true;
    }
}