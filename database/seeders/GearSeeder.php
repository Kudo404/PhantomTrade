<?php

namespace Database\Seeders;

use App\Models\Gear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Seed the default PhantomTrade gear catalog.
     *
     * updateOrCreate keeps this seeder safe to run more than once and avoids
     * creating duplicate catalog entries on later deployments.
     */
    public function run(): void
    {
        $gears = [
            [
                'name' => 'Basic Ghost Trap',
                'type' => 'trap',
                'description' => 'A compact containment trap for capturing low-level spectral entities.',
                'ecto_cost' => 20,
                'energy_cost' => 10,
            ],
            [
                'name' => 'Sticky Spirit Ward',
                'type' => 'ward',
                'description' => 'A portable ward charm that helps repel unwanted spirits from a protected area.',
                'ecto_cost' => 5,
                'energy_cost' => 2,
            ],
            [
                'name' => 'Soul Anchor Rope',
                'type' => 'anchor',
                'description' => 'A tether designed to stabilize a hunter and help reduce life-force loss during encounters.',
                'ecto_cost' => 15,
                'energy_cost' => 8,
            ],
            [
                'name' => 'Ecto-Scanner 3000',
                'type' => 'scanner',
                'description' => 'A handheld scanner that detects nearby ectoplasmic activity.',
                'ecto_cost' => 10,
                'energy_cost' => 5,
            ],
            [
                'name' => 'Spectral Pulse Scanner',
                'type' => 'scanner',
                'description' => 'A long-range scanner tuned to identify stronger ectoplasmic signatures before a hunt.',
                'ecto_cost' => 35,
                'energy_cost' => 12,
            ],
            [
                'name' => 'Resonance Beacon',
                'type' => 'scanner',
                'description' => 'A deployable beacon that helps locate unstable spectral activity in a haunted area.',
                'ecto_cost' => 28,
                'energy_cost' => 9,
            ],
            [
                'name' => 'Reinforced Containment Trap',
                'type' => 'trap',
                'description' => 'A reinforced trap built to hold more aggressive spirits during a field operation.',
                'ecto_cost' => 45,
                'energy_cost' => 18,
            ],
            [
                'name' => 'Phase-Lock Snare',
                'type' => 'trap',
                'description' => 'A quick-deploy snare designed to temporarily restrict a moving spectral target.',
                'ecto_cost' => 32,
                'energy_cost' => 14,
            ],
            [
                'name' => 'Soul Anchor Mk II',
                'type' => 'anchor',
                'description' => 'An upgraded soul anchor designed for more demanding supernatural encounters.',
                'ecto_cost' => 40,
                'energy_cost' => 16,
            ],
            [
                'name' => 'Portable Ward Emitter',
                'type' => 'ward',
                'description' => 'A portable emitter that establishes a small protective field around a deployed position.',
                'ecto_cost' => 30,
                'energy_cost' => 10,
            ],
            [
                'name' => 'Emergency Ward Battery',
                'type' => 'battery',
                'description' => 'A replacement power cell for restoring a deployed Spirit Ward when its battery runs out.',
                'ecto_cost' => 8,
                'energy_cost' => 0,
            ],
            [
                'name' => 'High-Capacity Ward Battery',
                'type' => 'battery',
                'description' => 'A high-capacity replacement power cell for maintaining Spirit Ward protection.',
                'ecto_cost' => 18,
                'energy_cost' => 0,
            ],
        ];

        foreach ($gears as $gear) {
            Gear::updateOrCreate(
                ['name' => $gear['name']],
                $gear
            );
        }
    }
}
