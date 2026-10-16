<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class AchievementsAndSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Settings ─────────────────────────
        $settings = [
            'merchant_trade_threshold'   => '3',
            'hunter_purchase_threshold'  => '3',
            'application_cooldown_hours' => '24',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        // ── Achievements ─────────────────────
        $achievements = [
            [
                'key'         => 'first_trade',
                'name'        => 'First Trade',
                'description' => 'Complete your first trade on the marketplace.',
                'icon'        => 'arrow-left-right',
                'type'        => 'trades',
                'threshold'   => 1,
                'sort_order'  => 1,
            ],
            [
                'key'         => 'merchant_candidate',
                'name'        => 'Merchant Candidate',
                'description' => 'Complete 3 trades — you can now apply to become a Merchant.',
                'icon'        => 'store',
                'type'        => 'trades',
                'threshold'   => 3,
                'sort_order'  => 2,
            ],
            [
                'key'         => 'certified_merchant',
                'name'        => 'Certified Merchant',
                'description' => 'Approved by an admin to become a Certified Merchant.',
                'icon'        => 'badge-check',
                'type'        => 'role',
                'threshold'   => 1,
                'target_role' => 'merchant',
                'sort_order'  => 3,
            ],
            [
                'key'         => 'first_purchase',
                'name'        => 'First Purchase',
                'description' => 'Buy your first item from the shop or marketplace.',
                'icon'        => 'shopping-cart',
                'type'        => 'purchases',
                'threshold'   => 1,
                'sort_order'  => 4,
            ],
            [
                'key'         => 'hunter_candidate',
                'name'        => 'Hunter Candidate',
                'description' => 'Complete 3 purchases — you can now apply to become a Ghost Hunter.',
                'icon'        => 'target',
                'type'        => 'purchases',
                'threshold'   => 3,
                'sort_order'  => 5,
            ],
            [
                'key'         => 'licensed_hunter',
                'name'        => 'Licensed Hunter',
                'description' => 'Approved by an admin to become a licensed Ghost Hunter.',
                'icon'        => 'shield-check',
                'type'        => 'role',
                'threshold'   => 1,
                'target_role' => 'ghost_hunter',
                'sort_order'  => 6,
            ],
        ];

        foreach ($achievements as $a) {
            Achievement::updateOrCreate(
                ['key' => $a['key']],
                $a
            );
        }
    }
}