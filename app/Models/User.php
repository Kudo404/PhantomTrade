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

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ecto',
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
}