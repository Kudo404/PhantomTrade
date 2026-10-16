<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'ecto_cost',
        'energy_cost',
    ];

        // ── Relationship: which users own this gear ──
    public function inventories()
    {
        return $this->hasMany(Inventory::class);    
    }

    public function loadouts()
    {
        return $this->hasMany(Loadout::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function tradeProposals()
    {
        return $this->hasMany(TradeProposal::class, 'offered_gear_id');
    }
}