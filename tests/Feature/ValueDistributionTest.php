<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Profile;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ValueDistributionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $teamLeaderA;

    protected User $teamLeaderB;

    protected Team $teamA;

    protected Team $teamB;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'team-leader', 'team-member', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->teamA = Team::factory()->create();
        $this->teamB = Team::factory()->create();

        $this->teamLeaderA = User::factory()->create(['team_id' => $this->teamA->id]);
        $this->teamLeaderA->assignRole('team-leader');

        $this->teamLeaderB = User::factory()->create(['team_id' => $this->teamB->id]);
        $this->teamLeaderB->assignRole('team-leader');
    }

    /**
     * completed_at is only stamped by the model's `updating` hook (mirroring
     * delivered_at), so — like real usage — the order must be created first
     * and then transitioned to 'completed', not created with that status directly.
     */
    protected function completedOrder(array $overrides = []): Order
    {
        $status = $overrides['status'] ?? 'completed';
        unset($overrides['status']);

        $order = Order::factory()->create(array_merge(['value' => 1000], $overrides));
        $order->update(['status' => $status]);

        return $order->fresh();
    }

    public function test_completing_an_order_stamps_completed_at_and_puts_it_on_the_pending_list(): void
    {
        $order = Order::factory()->create(['team_id' => $this->teamA->id, 'value' => 1000]);
        $order->update(['status' => 'completed']);

        $this->assertNotNull($order->fresh()->completed_at);

        $this->actingAs($this->admin)
            ->get(route('value-distribution.index'))
            ->assertOk()
            ->assertSee($order->name)
            ->assertSee('id="vdPendingCount">1', false);
    }

    public function test_the_net_value_is_calculated_from_the_freelance_profiles_platform(): void
    {
        $fiverrProfile = Profile::factory()->create(['platform' => 'fiverr']);
        $upworkProfile = Profile::factory()->create(['platform' => 'upwork']);

        $fiverrOrder = $this->completedOrder(['value' => 1000, 'freelance_profile_id' => $fiverrProfile->id]);
        $upworkOrder = $this->completedOrder(['value' => 1000, 'freelance_profile_id' => $upworkProfile->id]);
        $noProfileOrder = $this->completedOrder(['value' => 1000, 'freelance_profile_id' => null]);

        $this->assertSame(800.0, $fiverrOrder->netValue());
        $this->assertSame(900.0, $upworkOrder->netValue());
        $this->assertSame(800.0, $noProfileOrder->netValue());
    }

    public function test_admin_distributes_the_net_value_across_two_people(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]);
        $a = User::factory()->create();
        $b = User::factory()->create();

        // Net value is $800 (1000 minus the default 20% fee); this leaves $100 short.
        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [
                ['user_id' => $a->id, 'amount' => 500],
                ['user_id' => $b->id, 'amount' => 200],
            ],
        ])->assertOk()->assertJson(['is_complete' => false, 'undistributed' => 100.0]);

        $order->refresh();
        $this->assertSame('500.00', $order->valueDistributions->firstWhere('user_id', $a->id)->amount);
        $this->assertFalse($order->isValueFullyDistributed());
        $this->assertEqualsWithDelta(100.0, $order->undistributedValue(), 0.01);
    }

    public function test_distribution_cannot_exceed_the_net_value(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]);
        $a = User::factory()->create();

        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [
                ['user_id' => $a->id, 'amount' => 900], // net is 800
            ],
        ])->assertStatus(422);

        $this->assertCount(0, $order->fresh()->valueDistributions);
    }

    public function test_reaching_full_allocation_marks_the_order_distributed(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]); // net 800
        $a = User::factory()->create();

        $response = $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $a->id, 'amount' => 800]],
        ])->assertOk();

        $response->assertJson(['is_complete' => true]);
        $this->assertTrue($order->fresh()->isValueFullyDistributed());
        $this->assertNotNull($order->fresh()->value_distributed_at);

        $this->actingAs($this->admin)
            ->get(route('value-distribution.index'))
            ->assertOk()
            ->assertSee('id="vdDistributedCount">1', false)
            ->assertSee('id="vdPendingCount">0', false);
    }

    public function test_a_team_leader_only_sees_and_distributes_their_own_teams_orders(): void
    {
        $ownOrder = $this->completedOrder(['team_id' => $this->teamA->id, 'name' => 'Team A Deal', 'value' => 500]);
        $otherOrder = $this->completedOrder(['team_id' => $this->teamB->id, 'name' => 'Team B Deal', 'value' => 500]);
        $worker = User::factory()->create(['team_id' => $this->teamA->id]);

        $this->actingAs($this->teamLeaderA)
            ->get(route('value-distribution.index'))
            ->assertOk()
            ->assertSee('Team A Deal')
            ->assertDontSee('Team B Deal');

        $this->actingAs($this->teamLeaderA)->postJson(route('value-distribution.store', $ownOrder), [
            'shares' => [['user_id' => $worker->id, 'amount' => 400]],
        ])->assertOk();

        $this->actingAs($this->teamLeaderA)->postJson(route('value-distribution.store', $otherOrder), [
            'shares' => [['user_id' => $worker->id, 'amount' => 100]],
        ])->assertForbidden();
    }

    public function test_sales_leader_and_sales_member_cannot_access_the_distribution_page(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole('sales-leader');

        $salesPerson = User::factory()->create();
        $salesPerson->assignRole('sales-member');

        $teamMember = User::factory()->create(['team_id' => $this->teamA->id]);
        $teamMember->assignRole('team-member');

        $this->actingAs($salesLeader)->get(route('value-distribution.index'))->assertForbidden();
        $this->actingAs($salesPerson)->get(route('value-distribution.index'))->assertForbidden();
        $this->actingAs($teamMember)->get(route('value-distribution.index'))->assertForbidden();
    }

    public function test_a_sales_leader_who_also_holds_an_operations_role_is_still_denied(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole(['sales-leader', 'team-leader']);

        $this->actingAs($salesLeader)->get(route('value-distribution.index'))->assertForbidden();

        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]);
        $this->actingAs($salesLeader)
            ->postJson(route('value-distribution.store', $order), [
                'shares' => [['user_id' => $salesLeader->id, 'amount' => 800]],
            ])->assertForbidden();
    }

    public function test_only_completed_orders_can_be_distributed(): void
    {
        $order = Order::factory()->create(['status' => 'in-progress', 'value' => 500]);
        $worker = User::factory()->create();

        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $worker->id, 'amount' => 100]],
        ])->assertStatus(422);
    }

    public function test_assigned_users_are_pre_listed_and_highlighted_on_the_pending_order(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 500]);
        $assignee = User::factory()->create(['name' => 'Delivery Person']);
        $order->users()->attach($assignee->id);

        $this->actingAs($this->admin)
            ->get(route('value-distribution.index'))
            ->assertOk()
            ->assertSee('Delivery Person')
            ->assertSee('Assigned');
    }

    public function test_the_achievement_board_compares_credited_amount_to_the_operations_target(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]); // net 800
        $worker = User::factory()->create(['name' => 'Net Earner']);
        $worker->assignRole('team-member');
        SalesTarget::setFor($worker->id, now()->startOfMonth(), 1000);

        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $worker->id, 'amount' => 800]],
        ])->assertOk();

        $this->actingAs($this->admin)
            ->get(route('value-distribution.index'))
            ->assertOk()
            ->assertSee('Net Earner')
            ->assertSee('80% of $1,000.00 target');
    }

    public function test_the_add_person_search_excludes_sales_and_admin_accounts(): void
    {
        $worker = User::factory()->create(['name' => 'Delivery Worker']);
        $worker->assignRole('team-member');

        $salesLeader = User::factory()->create(['name' => 'Sales Leader Person']);
        $salesLeader->assignRole('sales-leader');

        $salesMember = User::factory()->create(['name' => 'Sales Member Person']);
        $salesMember->assignRole('sales-member');

        $response = $this->actingAs($this->admin)->getJson(
            route('orders.search-users', ['exclude_roles' => 'sales-leader,sales-member,admin'])
        )->assertOk();

        $names = collect($response->json('users'))->pluck('name');

        $this->assertContains('Delivery Worker', $names);
        $this->assertNotContains('Sales Leader Person', $names);
        $this->assertNotContains('Sales Member Person', $names);
        $this->assertNotContains($this->admin->name, $names);
    }

    public function test_changing_a_distribution_replaces_the_previous_shares(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]);
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $a->id, 'amount' => 800]],
        ])->assertOk();

        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $b->id, 'amount' => 800]],
        ])->assertOk();

        $order->refresh();
        $this->assertCount(1, $order->valueDistributions);
        $this->assertSame($b->id, $order->valueDistributions->first()->user_id);
    }

    public function test_the_lists_endpoint_searches_pending_orders(): void
    {
        $this->completedOrder(['team_id' => $this->teamA->id, 'name' => 'Zeta Landing Page', 'value' => 500]);
        $this->completedOrder(['team_id' => $this->teamA->id, 'name' => 'Acme Rebrand', 'value' => 600]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('value-distribution.lists', ['search' => 'zeta']))
            ->assertOk();

        $this->assertStringContainsString('Zeta Landing Page', $response->json('pending_html'));
        $this->assertStringNotContainsString('Acme Rebrand', $response->json('pending_html'));
        $this->assertSame(1, $response->json('pending_count'));
    }

    public function test_the_lists_endpoint_filters_pending_orders_by_team(): void
    {
        $this->completedOrder(['team_id' => $this->teamA->id, 'name' => 'Team A Order', 'value' => 500]);
        $this->completedOrder(['team_id' => $this->teamB->id, 'name' => 'Team B Order', 'value' => 500]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('value-distribution.lists', ['team' => $this->teamB->id]))
            ->assertOk();

        $this->assertStringContainsString('Team B Order', $response->json('pending_html'));
        $this->assertStringNotContainsString('Team A Order', $response->json('pending_html'));
        $this->assertSame(1, $response->json('pending_count'));
    }

    public function test_the_lists_endpoint_paginates_pending_orders(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->completedOrder(['team_id' => $this->teamA->id, 'name' => "Order {$i}", 'value' => 100]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson(route('value-distribution.lists', ['pending_page' => 2]))
            ->assertOk();

        $this->assertSame(11, $response->json('pending_count'));
        $this->assertStringContainsString('2 / 2', $response->json('pending_pager_html'));
    }

    public function test_the_lists_endpoint_opens_the_settled_panel_on_request(): void
    {
        $order = $this->completedOrder(['team_id' => $this->teamA->id, 'value' => 1000]);
        $worker = User::factory()->create();
        $this->actingAs($this->admin)->postJson(route('value-distribution.store', $order), [
            'shares' => [['user_id' => $worker->id, 'amount' => 800]],
        ])->assertOk();

        $response = $this->actingAs($this->admin)
            ->getJson(route('value-distribution.lists', ['settled_open' => 1]))
            ->assertOk();

        $this->assertStringContainsString($order->name, $response->json('settled_panel_html'));
        $this->assertStringNotContainsString('<div id="vdSettledBody" class="vd-settled-body" hidden', $response->json('settled_panel_html'));

        $closed = $this->actingAs($this->admin)
            ->getJson(route('value-distribution.lists'))
            ->assertOk();
        $this->assertStringContainsString('<div id="vdSettledBody" class="vd-settled-body" hidden', $closed->json('settled_panel_html'));
    }

    public function test_sales_roles_cannot_use_the_lists_endpoint(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole('sales-leader');

        $salesPerson = User::factory()->create();
        $salesPerson->assignRole('sales-member');

        $this->actingAs($salesLeader)->getJson(route('value-distribution.lists'))->assertForbidden();
        $this->actingAs($salesPerson)->getJson(route('value-distribution.lists'))->assertForbidden();
    }
}
