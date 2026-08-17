<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAssignmentProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function order(): Order
    {
        return Order::factory()->create(['team_id' => Team::factory()->create()->id]);
    }

    public function test_sales_member_cannot_assign_users_to_order(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $order = $this->order();

        $response = $this->actingAs($sales)->post(route('orders.assign-users', $order), ['users' => [$sales->id]]);

        $response->assertForbidden();
        $this->assertEmpty($order->fresh()->users);
    }

    public function test_sales_leader_cannot_assign_users_to_order(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole('sales-leader');

        $order = $this->order();

        $response = $this->actingAs($salesLeader)->post(route('orders.assign-users', $order), ['users' => [$salesLeader->id]]);

        $response->assertForbidden();
        $this->assertEmpty($order->fresh()->users);
    }

    public function test_sales_member_cannot_update_progress(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $order = $this->order();

        $response = $this->actingAs($sales)->post(route('orders.quick-update', $order), ['progress' => 50]);

        $response->assertForbidden();
        $this->assertEquals(0, $order->fresh()->progress);
    }

    public function test_sales_leader_cannot_update_progress(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole('sales-leader');

        $order = $this->order();

        $response = $this->actingAs($salesLeader)->post(route('orders.quick-update', $order), ['progress' => 50]);

        $response->assertForbidden();
        $this->assertEquals(0, $order->fresh()->progress);
    }

    public function test_assigned_team_member_can_update_progress(): void
    {
        $order = $this->order();
        $member = User::factory()->create(['team_id' => $order->team_id]);
        $member->assignRole('team-member');
        $order->users()->attach($member);

        $response = $this->actingAs($member)->post(route('orders.quick-update', $order), ['progress' => 60]);

        $response->assertOk();
        $this->assertEquals(60, $order->fresh()->progress);
    }

    public function test_sales_user_order_index_hides_assign_and_progress_controls(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $response = $this->actingAs($sales)->get(route('orders.index'));

        $response->assertOk()
            ->assertSee('const canManageAssignment = false', false)
            ->assertSee('const canUpdateProgress = false', false);
    }

    public function test_admin_order_index_shows_assign_and_progress_controls(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('orders.index'));

        $response->assertOk()
            ->assertSee('const canManageAssignment = true', false)
            ->assertSee('const canUpdateProgress = true', false);
    }

    public function test_sales_user_show_page_hides_progress_editor(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $order = $this->order();

        $response = $this->actingAs($sales)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertDontSee('Update progress')
            ->assertDontSee('Save progress');
    }
}
