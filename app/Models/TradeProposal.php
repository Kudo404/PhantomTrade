<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'buyer_id',
        'offered_gear_id',
        'offered_quantity',
        'note',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function offeredGear()
    {
        return $this->belongsTo(Gear::class, 'offered_gear_id');
    }

    // ── Convenience ───────────────────────────
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'accepted' => '#86efac',
            'rejected' => '#fca5a5',
            default    => '#fbbf24',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'accepted' => '✅ Accepted',
            'rejected' => '❌ Rejected',
            default    => '⏳ Pending',
        };
    }

    // Total estimated value of the offer (offered gear's ecto cost × quantity)
    public function offeredValue(): int
    {
        return ($this->offeredGear->ecto_cost ?? 0) * $this->offered_quantity;
    }

    // Estimated value of what they're asking for (one unit of the listing)
    public function listingValue(): int
    {
        return $this->listing->price_per_unit ?? 0;
    }
}