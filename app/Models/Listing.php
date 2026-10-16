<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'gear_id',
        'quantity',
        'price_per_unit',
        'status',
        'stock_reserved',
    ];

    protected $casts = [
        'stock_reserved' => 'boolean',
    ];

    // Relationships
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    public function tradeProposals()
    {
        return $this->hasMany(TradeProposal::class);
    }

    // Convenience methods
    public function totalPrice(): int
    {
        return $this->quantity * $this->price_per_unit;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->quantity > 0;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active' => '#86efac',
            'sold_out' => '#fbbf24',
            'cancelled' => '#fca5a5',
            default => '#888',
        };
    }
}
