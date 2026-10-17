<?php

namespace Tests\Feature;

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
            'energy' => 63,
            'life_force' => 63,
            'ecto' => 100,
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
