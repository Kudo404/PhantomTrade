<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtectionDevice extends Model
{
    protected $fillable = [
        'user_id', 'gear_id', 'label', 'latitude', 'longitude',
        'radius_meters', 'battery_minutes', 'activated_at', 'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'battery_minutes' => 'integer',
        'activated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    public function batteryPercent(): int
    {
        if (! $this->is_active || ! $this->activated_at) {
            return 0;
        }

        $elapsed = max(0, $this->activated_at->diffInMinutes(now(), false));
        return max(0, min(100, (int) floor(100 * (1 - ($elapsed / max(1, $this->battery_minutes))))));
    }

    public function isPowered(): bool
    {
        return $this->is_active && $this->batteryPercent() > 0;
    }

    public function remainingMinutes(): int
    {
        if (! $this->isPowered()) {
            return 0;
        }

        $elapsed = max(0, $this->activated_at->diffInMinutes(now(), false));
        return max(0, $this->battery_minutes - $elapsed);
    }
}
