<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantedResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'wanted_post_id',
        'responder_id',
        'offered_quantity',
        'price_per_unit',
        'note',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────
    public function wantedPost()
    {
        return $this->belongsTo(WantedPost::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // ── Convenience ───────────────────────────
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function totalValue(): int
    {
        return $this->offered_quantity * $this->price_per_unit;
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
}