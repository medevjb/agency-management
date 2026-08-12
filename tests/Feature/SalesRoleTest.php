<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $salesLeader;

    protected User $salesMember;

    protected User $otherSalesMember;

    protected User $operationManager;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->salesLeader = User::factory()->create(['name' => 'Sales Leader']);
        $this->salesLeader->assignRole('sales-leader');

        $this->salesMember = User::factory()->create([
            'name' => 'Sales Member',
            'sales_leader_id' => $this->salesLeader->id,
        ]);
        $this->salesMember->assignRole('sales-member');

        $this->otherSalesMember = User::factory()->create(['name' => 'Other Seller']);
        $this->otherSalesMember->assignRole('sales-member');

        $this->operationManager = User::factory()->create(['name' => 'Ops Manager']);
        $this->operationManager->assignRole('operation-manager');
    }

    public function test_sales_person_creates_an_order_from_the_deal_facts_alone(): void
    {
        $response = $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Northwind Trading',
            'value' => 1000,
            'notes' => 'Client wants a Shopify store.',
        ]);

        $response->assertRedirect(route('orders.index'));

        $order = Order::first();
        $this->assertSame($this->salesMember->id, $order->sales_user_id);
        $this->assertSame('Northwind Trading', $order->name);
        $this->assertSame(1000.0, (float) $order->value);
        $this->assertSame(800.0, (float) $order->actual_revenue);
        $this->assertSame('pending', $order->status);
        $this->assertSame(0, $order->progress);
        $this->assertNull($order->team_id);
    }

    public function test_operational_fields_sent_by_sales_are_ignored_on_create(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Contoso',
            'value' => 500,
            'status' => 'delivered',
            'progress' => 90,
            'team_id' => $team->id,
            'end_date' => now()->addDays(5)->toDateString(),
        ]);

        $order = Order::first();
        $this->assertSame('pending', $order->status);
        $this->assertSame(0, $order->progress);
        $this->assertNull($order->team_id);
        $this->assertNull($order->end_date);
    }

    public function test_sales_person_edits_deal_facts_but_never_delivery_fields(): void
    {
        $team = Team::factory()->create();
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
            'status' => 'in-progress',
            'progress' => 40,
        ]);

        $this->actingAs($this->salesMember)->patch(route('orders.update', $order), [
            'name' => 'Renamed Client',
            'value' => 2000,
            'notes' => 'Budget increased.',
            'status' => 'delivered',
            'progress' => 100,
            'team_id' => $team->id,
        ])->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertSame('Renamed Client', $order->name);
        $this->assertSame(2000.0, (float) $order->value);
        $this->assertSame('Budget increased.', $order->notes);
        $this->assertSame('in-progress', $order->status);
        $this->assertSame(40, $order->progress);
        $this->assertNull($order->team_id);
    }

    public function test_sales_member_only_sees_their_own_orders(): void
    {
        $mine = Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id]);
        $theirs = Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id]);

        $data = $this->actingAs($this->salesMember)->getJson(route('orders.data'))->assertOk()->json('data');

        $this->assertSame([$mine->id], array_column($data, 'id'));

        $this->actingAs($this->salesMember)->get(route('orders.show', $theirs))->assertForbidden();
        $this->actingAs($this->salesMember)->get(route('orders.show', $mine))->assertOk();
    }

    public function test_sales_leader_sees_every_order_sold_by_their_group(): void
    {
        $leaderOrder = Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id]);
        $memberOrder = Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id]);
        $outsideOrder = Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id]);

        $data = $this->actingAs($this->salesLeader)->getJson(route('orders.data'))->assertOk()->json('data');

        $this->assertEqualsCanonicalizing([$leaderOrder->id, $memberOrder->id], array_column($data, 'id'));

        $this->actingAs($this->salesLeader)->get(route('orders.show', $memberOrder))->assertOk();
        $this->actingAs($this->salesLeader)->get(route('orders.show', $outsideOrder))->assertForbidden();
    }

    public function test_sales_cannot_touch_operational_endpoints(): void
    {
        $order = Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id]);
        $worker = User::factory()->create();

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.quick-update', $order), ['status' => 'delivered'])
            ->assertForbidden();

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.assign-users', $order), ['users' => [$worker->id]])
            ->assertForbidden();

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.bulk-action'), ['ids' => [$order->id], 'action' => 'mark-completed'])
            ->assertForbidden();

        $this->actingAs($this->salesMember)
            ->deleteJson(route('orders.destroy', $order))
            ->assertForbidden();

        $this->assertSame('in-progress', $order->fresh()->status);
    }

    public function test_the_sales_order_form_hides_delivery_fields(): void
    {
        $response = $this->actingAs($this->salesMember)->get(route('orders.create'))->assertOk();

        $response->assertSee('Client Name', false)
            ->assertSee('Order Amount', false)
            ->assertSee('Initial Client Documents', false);

        $this->assertStringNotContainsString('name="status"', $response->getContent());
        $this->assertStringNotContainsString('name="team_id"', $response->getContent());
        $this->assertStringNotContainsString('name="progress"', $response->getContent());
        $this->assertStringNotContainsString('name="end_date"', $response->getContent());
        $this->assertStringNotContainsString('Assign Users', $response->getContent());
    }

    public function test_operations_never_sees_the_sales_owner_on_an_order(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
        ]);

        $this->actingAs($this->operationManager)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertDontSee('Sold by')
            ->assertDontSee('Sales Member');

        $this->actingAs($this->salesMember)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Sold by');
    }

    public function test_sales_people_land_on_the_sales_dashboard(): void
    {
        $this->actingAs($this->salesMember)->get(route('dashboard'))->assertRedirect(route('sales.dashboard'));
        $this->actingAs($this->salesMember)->get(route('orders.dashboard'))->assertRedirect(route('sales.dashboard'));
        $this->actingAs($this->operationManager)->get(route('sales.dashboard'))->assertForbidden();
    }

    public function test_a_member_sees_their_achievement_against_the_month_target(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->startOfMonth(), 5000);
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 2000]);

        $this->actingAs($this->salesMember)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Your target')
            ->assertSee('40%')
            ->assertSee('$2,000.00 of $5,000.00 minimum', false)
            ->assertSee('$3,000.00 to go', false);
    }

    public function test_a_leader_sees_each_member_against_their_target(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->startOfMonth(), 4000);
        SalesTarget::setFor($this->salesLeader->id, now()->startOfMonth(), 6000);

        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 4000]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 3000]);

        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Team target')
            ->assertSee('100% of $4,000.00', false)   // the member met theirs
            ->assertSee('50% of $6,000.00', false)    // the leader is halfway
            ->assertSee('$3,000.00 to go', false);
    }

    public function test_a_target_carries_forward_until_a_newer_one_is_set(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->subMonths(3)->startOfMonth(), 1000);
        SalesTarget::setFor($this->salesMember->id, now()->subMonth()->startOfMonth(), 2500);

        $this->assertSame(1000.0, SalesTarget::amountFor($this->salesMember->id, now()->subMonths(2)));
        $this->assertSame(2500.0, SalesTarget::amountFor($this->salesMember->id, now()));
        $this->assertNull(SalesTarget::amountFor($this->salesMember->id, now()->subMonths(6)));
    }

    public function test_an_admin_sets_a_members_target_from_the_user_form(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->patchJson(route('users.update', $this->salesMember), [
            'name' => $this->salesMember->name,
            'email' => $this->salesMember->email,
            'sales_target' => 7500,
            'sales_target_month' => now()->format('Y-m'),
        ])->assertOk();

        $this->assertSame(7500.0, $this->salesMember->fresh()->salesTargetFor());
        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $this->salesMember->id,
            'amount' => 7500,
            'set_by' => $admin->id,
        ]);
    }

    public function test_an_admin_can_put_a_sales_member_under_a_leader(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->patchJson(route('users.update', $this->otherSalesMember), [
            'name' => $this->otherSalesMember->name,
            'email' => $this->otherSalesMember->email,
            'sales_leader_id' => $this->salesLeader->id,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame($this->salesLeader->id, $this->otherSalesMember->fresh()->sales_leader_id);
        $this->assertEqualsCanonicalizing(
            [$this->salesLeader->id, $this->salesMember->id, $this->otherSalesMember->id],
            $this->salesLeader->fresh()->salesVisibleUserIds()
        );
    }

    public function test_sales_dashboard_totals_the_sales_amount_for_the_group(): void
    {
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 2500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 9999]);

        // The leader leads with the group total and sees who sold what.
        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Your team sold in')
            ->assertSee('$4,000.00')
            ->assertSee('Sales Member')
            ->assertDontSee('Other Seller');
    }

    public function test_a_member_sees_their_own_number_and_the_team_total(): void
    {
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 2500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 9999]);

        $this->actingAs($this->salesMember)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('You sold in')
            ->assertSee('$1,500.00')      // their own
            ->assertSee('Your team sold')
            ->assertSee('$4,000.00')      // the group they belong to
            ->assertSee('38%')            // their share of it
            ->assertDontSee('Other Seller');
    }

    public function test_an_admin_sees_every_sales_person(): void
    {
        $admin = User::factory()->create(['name' => 'The Admin']);
        $admin->assignRole('admin');

        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 500]);

        $this->actingAs($admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('All sales')
            ->assertSee('$2,000.00')
            ->assertSee('Sales Member')
            ->assertSee('Other Seller');
    }
}
