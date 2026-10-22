<?php

namespace Tests\Feature;

use App\Models\TopUpRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_demo_top_up_without_immediate_credit(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN, 'ecto' => 100]);

        $response = $this->actingAs($user)->post('/wallet', ['amount' => 250]);

        $response->assertRedirect('/wallet');
        $this->assertDatabaseHas('top_up_requests', [
            'user_id' => $user->id, 'amount' => 250, 'method' => 'demo', 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 100]);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_admin_approval_credits_ecto_once_and_creates_ledger_entry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN, 'ecto' => 100]);
        $request = TopUpRequest::create([
            'user_id' => $user->id, 'amount' => 250, 'method' => 'demo', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/top-ups/{$request->id}/approve")->assertRedirect('/admin/top-ups');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 350]);
        $this->assertDatabaseHas('top_up_requests', [
            'id' => $request->id, 'status' => 'approved', 'reviewed_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id, 'top_up_request_id' => $request->id,
            'type' => 'demo_top_up', 'direction' => 'credit', 'amount' => 250,
            'balance_before' => 100, 'balance_after' => 350,
        ]);
        $this->assertDatabaseCount('wallet_transactions', 1);

        $this->actingAs($admin)->post("/admin/top-ups/{$request->id}/approve")->assertSessionHasErrors('top_up');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 350]);
        $this->assertDatabaseCount('wallet_transactions', 1);
    }

    public function test_rejected_request_does_not_credit_ecto(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['ecto' => 100]);
        $request = TopUpRequest::create([
            'user_id' => $user->id, 'amount' => 250, 'method' => 'demo', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/top-ups/{$request->id}/reject", ['reason' => 'Demo rejection'])->assertRedirect('/admin/top-ups');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 100]);
        $this->assertDatabaseHas('top_up_requests', [
            'id' => $request->id, 'status' => 'rejected', 'rejection_reason' => 'Demo rejection',
        ]);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_non_admin_cannot_review_top_up_requests(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CIVILIAN]);
        $request = TopUpRequest::create([
            'user_id' => $user->id, 'amount' => 250, 'method' => 'demo', 'status' => 'pending',
        ]);

        $this->actingAs($user)->post("/admin/top-ups/{$request->id}/approve")->assertForbidden();
        $this->assertDatabaseHas('top_up_requests', ['id' => $request->id, 'status' => 'pending']);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_top_up_amount_must_be_within_demo_limits(): void
    {
        $user = User::factory()->create(['ecto' => 100]);

        $this->actingAs($user)->post('/wallet', ['amount' => 0])->assertSessionHasErrors('amount');
        $this->actingAs($user)->post('/wallet', ['amount' => 10001])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('top_up_requests', 0);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 100]);
    }


    public function test_gameplay_ecto_credits_and_debits_are_recorded_in_ledger(): void
    {
        $user = User::factory()->create(['ecto' => 100]);

        $user->spendEcto(25);
        $user->refresh()->addEcto(10);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'ecto' => 85]);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id, 'type' => 'gameplay_debit', 'direction' => 'debit',
            'amount' => 25, 'balance_before' => 100, 'balance_after' => 75,
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id, 'type' => 'gameplay_credit', 'direction' => 'credit',
            'amount' => 10, 'balance_before' => 75, 'balance_after' => 85,
        ]);
    }

}
