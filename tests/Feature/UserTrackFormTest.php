<?php

namespace Tests\Feature;

use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTrackFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'team-member', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_the_create_page_offers_an_operations_and_a_sales_track(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.create'))->assertOk();

        $response->assertSee('value="operation"', false)
            ->assertSee('value="sales"', false)
            ->assertSee('data-track-panel="operation"', false)
            ->assertSee('data-track-panel="sales"', false)
            ->assertSee('Sales Leader')
            ->assertSee('Monthly Sales Target');
    }

    public function test_choosing_the_operations_track_stores_team_and_ignores_sales_fields(): void
    {
        $team = Team::factory()->create();
        $stack = TechStack::factory()->create();
        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');

        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Ops Hire',
            'email' => 'ops-hire@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'track' => 'operation',
            'role' => 'team-member',
            'team_id' => $team->id,
            'tech_stack_id' => $stack->id,
            // Sent anyway, to prove the server ignores it rather than trusting the client.
            'sales_leader_id' => $leader->id,
            'sales_target' => 5000,
        ])->assertRedirect();

        $user = User::where('email', 'ops-hire@example.com')->firstOrFail();

        $this->assertSame($team->id, $user->team_id);
        $this->assertSame($stack->id, $user->tech_stack_id);
        $this->assertNull($user->sales_leader_id);
        $this->assertNull($user->salesTargetFor());
    }

    public function test_choosing_the_sales_track_stores_leader_and_target_and_ignores_team(): void
    {
        $team = Team::factory()->create();
        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');

        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Sales Hire',
            'email' => 'sales-hire@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'track' => 'sales',
            'role' => 'sales-member',
            'sales_leader_id' => $leader->id,
            'sales_target' => 4500,
            'sales_target_month' => now()->format('Y-m'),
            // Sent anyway, to prove the server ignores it.
            'team_id' => $team->id,
        ])->assertRedirect();

        $user = User::where('email', 'sales-hire@example.com')->firstOrFail();

        $this->assertNull($user->team_id);
        $this->assertSame($leader->id, $user->sales_leader_id);
        $this->assertSame(4500.0, $user->salesTargetFor());
    }

    public function test_a_sales_role_cannot_be_paired_with_the_operations_track(): void
    {
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Mismatch',
            'email' => 'mismatch@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'track' => 'operation',
            'role' => 'sales-member',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_editing_an_existing_sales_person_keeps_the_sales_track_selected(): void
    {
        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');
        $member = User::factory()->create(['sales_leader_id' => $leader->id]);
        $member->assignRole('sales-member');
        SalesTarget::setFor($member->id, now()->startOfMonth(), 3000);

        $this->actingAs($this->admin)
            ->get(route('users.edit', $member))
            ->assertOk()
            ->assertSee('value="sales" checked', false);
    }

    public function test_switching_an_existing_sales_person_to_operations_clears_their_sales_leader(): void
    {
        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');
        $member = User::factory()->create(['sales_leader_id' => $leader->id, 'name' => 'Switching Person']);
        $member->assignRole('sales-member');
        $team = Team::factory()->create();

        $this->actingAs($this->admin)->patchJson(route('users.update', $member), [
            'name' => 'Switching Person',
            'email' => $member->email,
            'track' => 'operation',
            'team_id' => $team->id,
        ])->assertOk();

        $member->refresh();
        $this->assertSame($team->id, $member->team_id);
        $this->assertNull($member->sales_leader_id);
    }
}
