<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesMyOrdersFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_sales_member_can_filter_orders_to_only_their_own_sales(): void
    {
        $salesA = User::factory()->create();
        $salesA->assignRole('sales-member');
        $salesB = User::factory()->create();
        $salesB->assignRole('sales-member');

        $myOrderOne = Order::factory()->create(['sales_user_id' => $salesA->id, 'created_by' => $salesA->id]);
        $myOrderTwo = Order::factory()->create(['sales_user_id' => $salesA->id, 'created_by' => $salesA->id]);
        $theirOrder = Order::factory()->create(['sales_user_id' => $salesB->id, 'created_by' => $salesB->id]);

        $response = $this->actingAs($salesA)->get(route('orders.data', ['my_sales' => '1', 'length' => '100']));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertCount(2, $names);
        $this->assertContains($myOrderOne->name, $names);
        $this->assertContains($myOrderTwo->name, $names);
        $this->assertNotContains($theirOrder->name, $names);
    }

    public function test_sales_leader_can_filter_orders_to_only_their_own_sales(): void
    {
        $salesLeader = User::factory()->create();
        $salesLeader->assignRole('sales-leader');
        $otherSeller = User::factory()->create();
        $otherSeller->assignRole('sales-member');

        $myOrder = Order::factory()->create(['sales_user_id' => $salesLeader->id, 'created_by' => $salesLeader->id]);
        $otherOrder = Order::factory()->create(['sales_user_id' => $otherSeller->id, 'created_by' => $otherSeller->id]);

        $response = $this->actingAs($salesLeader)->get(route('orders.data', ['my_sales' => '1', 'length' => '100']));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertCount(1, $names);
        $this->assertContains($myOrder->name, $names);
        $this->assertNotContains($otherOrder->name, $names);
    }

    public function test_without_the_flag_a_sales_user_sees_all_orders(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');
        $otherSeller = User::factory()->create();
        $otherSeller->assignRole('sales-member');

        Order::factory()->create(['sales_user_id' => $sales->id, 'created_by' => $sales->id]);
        Order::factory()->create(['sales_user_id' => $otherSeller->id, 'created_by' => $otherSeller->id]);

        $response = $this->actingAs($sales)->get(route('orders.data', ['length' => '100']));

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_my_sales_filter_is_ignored_for_non_sales_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $soldOrder = Order::factory()->create(['sales_user_id' => $sales->id, 'created_by' => $sales->id]);
        $adminOrder = Order::factory()->create(['sales_user_id' => null, 'created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('orders.data', ['my_sales' => '1', 'length' => '100']));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertCount(2, $names);
        $this->assertContains($soldOrder->name, $names);
        $this->assertContains($adminOrder->name, $names);
    }

    public function test_sales_user_order_index_renders_my_sales_checkbox(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $response = $this->actingAs($sales)->get(route('orders.index'));

        $response->assertOk()
            ->assertSee('filterMySales', false)
            ->assertSee('My sales only', false);
    }

    public function test_admin_order_index_omits_my_sales_checkbox(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('orders.index'));

        $response->assertOk()
            ->assertDontSee('My sales only', false)
            ->assertDontSee('id="filterMySales"', false);
    }
}
