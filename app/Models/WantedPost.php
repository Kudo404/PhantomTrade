<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WantedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gear_id',
        'quantity',
        'price_per_unit',
        'note',
        'status',
    ];

    // ── Relationships ─────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    public function responses()
    {
        return $this->hasMany(WantedResponse::class);
    }

    // ── Convenience ───────────────────────────
    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->quantity > 0;
    }

    public function totalOfferedValue(): int
    {
        return $this->quantity * $this->price_per_unit;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'open'      => '#86efac',
            'fulfilled' => '#7dd3fc',
            'cancelled' => '#fca5a5',
            default     => '#888',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open'      => '🔍 Open',
            'fulfilled' => '✅ Fulfilled',
            'cancelled' => '❌ Cancelled',
            default     => $this->status,
        };
    }
}