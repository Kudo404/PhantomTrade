<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loadout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gear_id',
        'slot',
        'tuning_mode',
        'frequency_khz',
        'ward_reinforcement',
        'custom_name',
    ];

    protected $casts = [
        'frequency_khz'      => 'integer',
        'ward_reinforcement' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }

    /**
     * Compute effective energy drain based on tuning mode and reinforcement.
     */
    public function getEffectiveEnergyDrainAttribute(): int
    {
        $base = $this->gear ? $this->gear->energy_cost : 0;

        $multiplier = match ($this->tuning_mode) {
            'overclocked' => 1.5,
            'shielded'    => 0.8,
            'stealth'     => 0.7,
            default       => 1.0,
        };

        // Each 10% ward reinforcement adds +1 energy drain
        $extraDrain = (int) round(($this->ward_reinforcement ?? 0) / 10);

        return (int) max(1, round($base * $multiplier) + $extraDrain);
    }

    /**
     * Target supernatural entity category based on tuned frequency.
     */
    public function getTargetEntityAttribute(): string
    {
        $freq = $this->frequency_khz ?? 440;

        if ($freq < 350) {
            return 'Poltergeist (Kinetic)';
        } elseif ($freq < 550) {
            return 'Banshee (Acoustic)';
        } elseif ($freq < 750) {
            return 'Shadow Fiend (Astral)';
        } else {
            return 'Ancient Wraith (Ethereal)';
        }
    }

    /**
     * Human-friendly label for tuning mode.
     */
    public function getTuningModeLabelAttribute(): string
    {
        return match ($this->tuning_mode) {
            'overclocked' => '⚡ Overclocked (+Power / +Drain)',
            'shielded'    => '🛡️ Ward-Hardened (+Shield / -Drain)',
            'stealth'     => '🔇 Spectral-Dampened (Stealth)',
            default       => '⚖️ Balanced Calibration',
        };
    }

    /**
     * Display name (custom nickname or gear name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->custom_name ?: ($this->gear ? $this->gear->name : 'Unknown Gear');
    }
}