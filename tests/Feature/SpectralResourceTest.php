<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Loadout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpectralResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_encounter_persistently_spends_energy_and_life_force(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => 100,
            'energy' => 100,
            'life_force' => 100,
        ]);

        $response = $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'poltergeist',
        ]);

        $response->assertRedirect('/drain');
        $response->assertSessionHas('simulation_result');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 78,
            'life_force' => 63,
            'ecto' => 100,
        ]);
    }

    public function test_successful_containment_keeps_ecto_rewards_and_persists_resource_costs(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => 100,
            'energy' => 100,
            'life_force' => 100,
        ]);

        $trap = Gear::create([
            'name' => 'Test Containment Trap',
            'type' => 'trap',
            'description' => 'Feature-test containment unit.',
            'ecto_cost' => 10,
            'energy_cost' => 5,
        ]);
        $scanner = Gear::create([
            'name' => 'Test Spectral Scanner',
            'type' => 'scanner',
            'description' => 'Feature-test resonance scanner.',
            'ecto_cost' => 10,
            'energy_cost' => 5,
        ]);

        Loadout::create([
            'user_id' => $user->id,
            'gear_id' => $trap->id,
            'slot' => 'trap',
            'tuning_mode' => 'balanced',
            'frequency_khz' => 440,
            'ward_reinforcement' => 0,
        ]);
        Loadout::create([
            'user_id' => $user->id,
            'gear_id' => $scanner->id,
            'slot' => 'scanner',
            'tuning_mode' => 'balanced',
            'frequency_khz' => 280,
            'ward_reinforcement' => 0,
        ]);

        $response = $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'poltergeist',
        ]);

        $response->assertRedirect('/drain');
        $response->assertSessionHas('simulation_result', function ($result) {
            return $result['captured'] === true && $result['ecto_earned'] === 20;
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 68,
            'life_force' => 63,
            'ecto' => 120,
        ]);
    }

    public function test_encounter_is_blocked_without_enough_energy_and_does_not_change_resources(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => 100,
            'energy' => 10,
            'life_force' => 100,
        ]);

        $response = $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'poltergeist',
        ]);

        $response->assertRedirect('/drain');
        $response->assertSessionHasErrors('encounter');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 10,
            'life_force' => 100,
            'ecto' => 100,
        ]);
    }

    public function test_encounter_is_blocked_when_life_force_cannot_cover_the_exposure(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => 100,
            'energy' => 100,
            'life_force' => 10,
        ]);

        $response = $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'poltergeist',
        ]);

        $response->assertRedirect('/drain');
        $response->assertSessionHasErrors('encounter');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 100,
            'life_force' => 10,
            'ecto' => 100,
        ]);
    }

    public function test_recovery_spends_ecto_and_restores_both_reserves(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => User::RECOVERY_ECTO_COST,
            'energy' => 0,
            'life_force' => 25,
        ]);

        $response = $this->actingAs($user)->post('/drain/recover');

        $response->assertRedirect('/drain');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => User::MAX_ENERGY,
            'life_force' => User::MAX_LIFE_FORCE,
            'ecto' => 0,
        ]);
    }

    public function test_recovery_requires_ecto_and_does_not_restore_resources_for_free(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'ecto' => User::RECOVERY_ECTO_COST - 1,
            'energy' => 0,
            'life_force' => 25,
        ]);

        $response = $this->actingAs($user)->post('/drain/recover');

        $response->assertRedirect('/drain');
        $response->assertSessionHasErrors('recovery');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 0,
            'life_force' => 25,
            'ecto' => User::RECOVERY_ECTO_COST - 1,
        ]);
    }

    public function test_invalid_entity_ids_are_rejected(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_GHOST_HUNTER,
            'energy' => 100,
            'life_force' => 100,
        ]);

        $response = $this->actingAs($user)->post('/drain/execute', [
            'entity_id' => 'unlisted_entity',
        ]);

        $response->assertSessionHasErrors('entity_id');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'energy' => 100,
            'life_force' => 100,
        ]);
    }
}
