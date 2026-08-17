<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderValueDistribution;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDetailsFieldsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_delivered_date_can_be_edited_via_quick_update(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($admin)->post(route('orders.quick-update', $order), ['delivered_at' => '2026-08-10']);

        $response->assertOk();
        $this->assertEquals('2026-08-10 00:00:00', $order->fresh()->delivered_at->format('Y-m-d H:i:s'));
    }

    public function test_completed_date_can_be_edited_via_quick_update(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($admin)->post(route('orders.quick-update', $order), ['completed_at' => '2026-08-11']);

        $response->assertOk();
        $this->assertEquals('2026-08-11 00:00:00', $order->fresh()->completed_at->format('Y-m-d H:i:s'));
    }

    public function test_delivered_and_completed_dates_can_be_cleared(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create([
            'team_id' => Team::factory()->create()->id,
            'delivered_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('orders.quick-update', $order), ['delivered_at' => null])->assertOk();
        $this->actingAs($admin)->post(route('orders.quick-update', $order), ['completed_at' => null])->assertOk();

        $this->assertNull($order->fresh()->delivered_at);
        $this->assertNull($order->fresh()->completed_at);
    }

    public function test_show_page_renders_editable_delivered_and_completed_dates(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($admin)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('data-field="delivered_at"', false)
            ->assertSee('data-field="completed_at"', false);
    }

    public function test_show_page_shows_status_history(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'in-progress',
            'changed_by' => $admin->id,
            'actual_revenue' => 800,
        ]);

        $response = $this->actingAs($admin)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('Status history', false)
            ->assertSee('In progress', false);
    }

    public function test_show_page_marks_value_as_distributed_and_lists_who_took_it(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $deliverer = User::factory()->create();

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id, 'value' => 1000]);
        OrderValueDistribution::create([
            'order_id' => $order->id,
            'user_id' => $deliverer->id,
            'amount' => 800,
            'distributed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('Value distribution', false)
            ->assertSee('Distributed', false)
            ->assertSee($deliverer->name, false)
            ->assertDontSee('Not distributed', false);
    }

    public function test_show_page_marks_value_as_not_distributed_when_nobody_credited(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($admin)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('Value distribution', false)
            ->assertSee('Not distributed', false)
            ->assertSee('No value has been credited to anyone yet.', false);
    }

    public function test_sales_user_does_not_see_the_value_distribution_panel(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales-member');

        $order = Order::factory()->create(['team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($sales)->get(route('orders.show', $order));

        $response->assertOk()
            ->assertDontSee('Value distribution', false);
    }
}
