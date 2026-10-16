<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        \App\Models\Gear::create([
            'name' => 'Basic Ghost Trap',
            'type' => 'trap',
            'description' => 'A little box that goes SLURP and catches a ghost.',
            'ecto_cost' => 20,
            'energy_cost' => 10,
        ]);

        \App\Models\Gear::create([
            'name' => 'Sticky Spirit Ward',
            'type' => 'ward',
            'description' => 'A charm that says NO GHOSTS ALLOWED.',
            'ecto_cost' => 5,
            'energy_cost' => 2,
        ]);

        \App\Models\Gear::create([
            'name' => 'Soul Anchor Rope',
            'type' => 'anchor',
            'description' => 'Keeps YOU from floating away.',
            'ecto_cost' => 15,
            'energy_cost' => 8,
        ]);

        \App\Models\Gear::create([
            'name' => 'Ecto-Scanner 3000',
            'type' => 'scanner',
            'description' => 'A flashlight that beeps when a ghost is near.',
            'ecto_cost' => 10,
            'energy_cost' => 5,
        ]);
    }
}
