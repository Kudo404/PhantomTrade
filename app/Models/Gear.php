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
        'image_path',
    ];

    // Users who own this gear
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    // Loadouts using this gear
    public function loadouts()
    {
        return $this->hasMany(Loadout::class);
    }

    // Marketplace listings for this gear
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    // Trade proposals involving this gear
    public function tradeProposals()
    {
        return $this->hasMany(TradeProposal::class, 'offered_gear_id');
    }
}
