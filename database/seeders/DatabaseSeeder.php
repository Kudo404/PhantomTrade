<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's default data.
     */
    public function run(): void
    {
        $this->call([
            AchievementsAndSettingsSeeder::class,
            GearSeeder::class,
        ]);
    }
}
