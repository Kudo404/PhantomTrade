<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gear_id',
        'quantity',
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

    // Convenience: "3× Basic Ghost Trap"
    public function label(): string
    {
        return $this->quantity . '× ' . $this->gear->name;
    }
}