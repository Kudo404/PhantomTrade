<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'gear_id',
        'quantity',
        'price_per_unit',
        'source',
    ];

    // ── Relationships ─────────────────────────
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    // ── Convenience ───────────────────────────
    public function totalPrice(): int
    {
        return $this->quantity * $this->price_per_unit;
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            'shop'        => '🛒 Gear Shop',
            'marketplace' => '🏪 Marketplace',
            default       => $this->source,
        };
    }
}