<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\ProtectionDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProtectionDeviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_civilian_can_deploy_an_owned_ward_at_a_chosen_location(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);
        $ward = Gear::create([
            'name' => 'Home Spirit Ward',
            'type' => 'ward',
            'description' => 'Protects a location.',
            'ecto_cost' => 20,
            'energy_cost' => 3,
        ]);
        Inventory::create(['user_id' => $user->id, 'gear_id' => $ward->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->post('/protection/deploy', [
            'gear_id' => $ward->id,
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'label' => 'Home',
        ]);

        $response->assertRedirect('/protection');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('protection_devices', [
            'user_id' => $user->id,
            'gear_id' => $ward->id,
            'label' => 'Home',
            'radius_meters' => 100,
            'battery_minutes' => 1440,
            'is_active' => 1,
        ]);
        $this->assertDatabaseMissing('inventories', [
            'user_id' => $user->id,
            'gear_id' => $ward->id,
        ]);
    }

    public function test_civilian_cannot_access_hunter_loadout_or_drain_chamber(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);

        $this->actingAs($user)->get('/loadout')->assertForbidden();
        $this->actingAs($user)->get('/drain')->assertForbidden();
        $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'poltergeist',
        ])->assertForbidden();
    }

    public function test_civilian_cannot_deploy_hunting_equipment(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);
        $trap = Gear::create([
            'name' => 'Containment Trap',
            'type' => 'trap',
            'description' => 'Hunter equipment.',
            'ecto_cost' => 20,
            'energy_cost' => 3,
        ]);
        Inventory::create(['user_id' => $user->id, 'gear_id' => $trap->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->from('/protection')->post('/protection/deploy', [
            'gear_id' => $trap->id,
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ]);

        $response->assertRedirect('/protection');
        $response->assertSessionHasErrors('gear_id');
        $this->assertDatabaseMissing('protection_devices', ['user_id' => $user->id]);
    }

    public function test_user_cannot_deploy_gear_they_do_not_own(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);
        $ward = Gear::create([
            'name' => 'Unowned Ward',
            'type' => 'ward',
            'description' => 'Not in inventory.',
            'ecto_cost' => 20,
            'energy_cost' => 3,
        ]);

        $response = $this->actingAs($user)->from('/protection')->post('/protection/deploy', [
            'gear_id' => $ward->id,
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ]);

        $response->assertRedirect('/protection');
        $response->assertSessionHasErrors('gear_id');
        $this->assertDatabaseMissing('protection_devices', ['user_id' => $user->id]);
    }

    public function test_user_can_recharge_a_device_with_ecto(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN, 'ecto' => 20]);
        $ward = Gear::create([
            'name' => 'Rechargeable Ward',
            'type' => 'ward',
            'description' => 'Ward that can be recharged.',
            'ecto_cost' => 20,
            'energy_cost' => 3,
        ]);
        $device = ProtectionDevice::create([
            'user_id' => $user->id,
            'gear_id' => $ward->id,
            'label' => 'Home',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'radius_meters' => 100,
            'battery_minutes' => 1440,
            'activated_at' => now()->subHours(30),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/protection/' . $device->id . '/recharge');

        $response->assertRedirect('/protection');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 10]);
        $this->assertDatabaseHas('protection_devices', [
            'id' => $device->id,
            'battery_minutes' => 1440,
            'is_active' => 1,
        ]);
    }

    public function test_owned_battery_recharges_only_the_users_device(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-17 09:00:00'));
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);
        $ward = Gear::create([
            'name' => 'Depleted Ward',
            'type' => 'ward',
            'description' => 'Ward with a dead battery.',
            'ecto_cost' => 20,
            'energy_cost' => 3,
        ]);
        $battery = Gear::create([
            'name' => 'Test Battery',
            'type' => 'battery',
            'description' => 'Replacement battery.',
            'ecto_cost' => 15,
            'energy_cost' => 0,
        ]);
        $device = ProtectionDevice::create([
            'user_id' => $user->id,
            'gear_id' => $ward->id,
            'label' => 'Home',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'radius_meters' => 100,
            'battery_minutes' => 1440,
            'activated_at' => now()->subHours(30),
            'is_active' => true,
        ]);
        Inventory::create(['user_id' => $user->id, 'gear_id' => $battery->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->post('/protection/' . $device->id . '/battery');

        $response->assertRedirect('/protection');
        $this->assertDatabaseHas('protection_devices', [
            'id' => $device->id,
            'battery_minutes' => 1440,
            'is_active' => 1,
        ]);
        $this->assertDatabaseMissing('inventories', [
            'user_id' => $user->id,
            'gear_id' => $battery->id,
        ]);
        Carbon::setTestNow();
    }
}
