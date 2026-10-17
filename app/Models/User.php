<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ── Roles ──────────────────────────────────
    const ROLE_CIVILIAN     = 'civilian';
    const ROLE_GHOST_HUNTER = 'ghost_hunter';
    const ROLE_MERCHANT     = 'merchant';
    const ROLE_ADMIN        = 'admin';

    public const MAX_ENERGY = 100;
    public const MAX_LIFE_FORCE = 100;
    public const RECOVERY_ECTO_COST = 10;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ecto',
        'energy',
        'life_force',
        'latitude',
        'longitude',
        'location_name',
        'hunter_name',
        'hunter_tier',
        'license_number',
        'license_issued_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Laravel 9 requires the classic $casts property for reliability.
     * Method-style casts() works but has quirks in some setups.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'license_issued_at' => 'datetime',
        'energy' => 'integer',
        'life_force' => 'integer',
    ];

    // ── Role helpers ───────────────────────────
    public function isAdmin(): bool       { return $this->role === self::ROLE_ADMIN; }
    public function isMerchant(): bool    { return $this->role === self::ROLE_MERCHANT; }
    public function isGhostHunter(): bool { return $this->role === self::ROLE_GHOST_HUNTER; }
    public function isCivilian(): bool    { return $this->role === self::ROLE_CIVILIAN; }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN        => 'Admin',
            self::ROLE_MERCHANT     => 'Merchant',
            self::ROLE_GHOST_HUNTER => 'Ghost Hunter',
            default                 => 'Civilian',
        };
    }

    // ── Ecto helpers ───────────────────────────
    public function canAfford(int $amount): bool { return $this->ecto >= $amount; }
    public function addEcto(int $amount): void   { $this->increment('ecto', $amount); }
    public function spendEcto(int $amount): void { $this->decrement('ecto', $amount); }

    // ── Location helpers ───────────────────────
    public function hasLocation(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    public function locationLabel(): string
    {
        return $this->location_name ?: 'Unknown location';
    }

    // ── License helpers ────────────────────────
    public function hasLicense(): bool
    {
        return $this->isGhostHunter() && $this->license_number;
    }

    public function hunterTierLabel(): string
    {
        return match ($this->hunter_tier) {
            'S' => 'Class S',
            'A' => 'Class A',
            'B' => 'Class B',
            'C' => 'Class C',
            default => 'Class D',
        };
    }

    // ── Progress helpers ───────────────────────
    public function completedTradesCount(): int
    {
        $asBuyer = \App\Models\TradeProposal::where('buyer_id', $this->id)
            ->where('status', 'accepted')
            ->count();

        $asSeller = \App\Models\TradeProposal::whereHas('listing', function ($q) {
                $q->where('merchant_id', $this->id);
            })
            ->where('status', 'accepted')
            ->count();

        return $asBuyer + $asSeller;
    }

    public function completedPurchasesCount(): int
    {
        return \App\Models\Purchase::where('buyer_id', $this->id)->count();
    }

    // ── Relationships ──────────────────────────
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function tradeProposals()
    {
        return $this->hasMany(TradeProposal::class, 'buyer_id');
    }

    public function loadouts()
    {
        return $this->hasMany(Loadout::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'merchant_id');
    }

    public function wantedPosts()
    {
        return $this->hasMany(WantedPost::class);
    }

    public function wantedResponses()
    {
        return $this->hasMany(WantedResponse::class, 'responder_id');
    }

    public function achievements()
    {
        return $this->belongsToMany(
            \App\Models\Achievement::class,
            'user_achievements',
            'user_id',
            'achievement_id'
        )->withPivot('unlocked_at');
    }

    public function roleApplications()
    {
        return $this->hasMany(\App\Models\RoleApplication::class);
    }

        // ── Messaging relationships ────────────────
    public function conversationsAsOne()
    {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    public function conversationsAsTwo()
    {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    /**
     * All conversations this user is part of.
     */
    public function conversations()
    {
        return Conversation::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Count total unread messages across all conversations.
     */
    public function unreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function ($q) {
                $q->where('user_one_id', $this->id)
                  ->orWhere('user_two_id', $this->id);
            })
            ->where('sender_id', '!=', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Is this user a customer service crew member (admin)?
     */
    public function isCustomerService(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Spectral Rig Resource Tracker & Telemetry for Scenario 3
     */
    public function spectralRigSummary(): array
    {
        $equipped = $this->loadouts()->with('gear')->get();

        $totalDrain = $equipped->sum(fn ($l) => $l->effective_energy_drain);
        $hasWard = $equipped->contains(fn ($l) => ($l->gear && $l->gear->type === 'ward') || $l->slot === 'ward');
        $hasAnchor = $equipped->contains(fn ($l) => ($l->gear && $l->gear->type === 'anchor') || $l->slot === 'anchor');
        $hasTrap = $equipped->contains(fn ($l) => ($l->gear && $l->gear->type === 'trap') || $l->slot === 'trap');
        $hasScanner = $equipped->contains(fn ($l) => ($l->gear && $l->gear->type === 'scanner') || $l->slot === 'scanner');

        // Soul Tether Stability Calculation (Scenario 3 Life-Force Drain mechanics):
        $stability = 100;
        if (! $hasAnchor && $totalDrain > 15) {
            $stability -= min(60, ($totalDrain - 15) * 4);
        } elseif (! $hasAnchor) {
            $stability -= 15;
        }

        // Ward absorption rating (0 - 100%)
        $wardAbsorption = 0;
        $wardItem = $equipped->first(fn ($l) => ($l->gear && $l->gear->type === 'ward') || $l->slot === 'ward');
        if ($wardItem) {
            $baseAbsorption = match ($wardItem->tuning_mode) {
                'shielded'    => 55,
                'overclocked' => 25,
                'stealth'     => 40,
                default       => 35,
            };
            $wardAbsorption = min(95, $baseAbsorption + ($wardItem->ward_reinforcement ?? 0));
        }

        // Overload status:
        $isOverloaded = $totalDrain > 50 || ($totalDrain > 25 && ! $hasWard);

        return [
            'equipped_count'  => $equipped->count(),
            'total_drain'     => $totalDrain,
            'stability'       => (int) max(10, min(100, $stability)),
            'ward_absorption' => (int) $wardAbsorption,
            'has_ward'        => (bool) $hasWard,
            'has_anchor'      => (bool) $hasAnchor,
            'has_trap'        => (bool) $hasTrap,
            'has_scanner'     => (bool) $hasScanner,
            'is_overloaded'   => (bool) $isOverloaded,
        ];
    }
}