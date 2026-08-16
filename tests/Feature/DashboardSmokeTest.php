<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * One pass over every dashboard route this session touched, as the admin
 * role (which can reach all of them) and again after switching the global
 * month — the cheapest way to catch a page that only 500s once real data
 * and a non-default month are both in play.
 */
class DashboardSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_dashboard_renders_for_admin_in_the_default_and_a_past_month(): void
    {
        foreach (['admin', 'sales-leader', 'sales-member', 'team-leader'] as $role) {
            Role::create(['name' => $role]);
        }

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');

        $order = Order::factory()->create(['sales_user_id' => $leader->id, 'created_by' => $leader->id, 'value' => 500]);
        $order->update(['status' => 'completed']);
        $order->valueDistributions()->create(['user_id' => $leader->id, 'amount' => 400, 'distributed_by' => $admin->id]);

        $routes = [
            'dashboard',
            'admin.dashboard',
            'orders.dashboard',
            'sales.dashboard',
            'sales.leadership',
            'value-distribution.index',
        ];

        foreach ($routes as $name) {
            $this->actingAs($admin)->get(route($name))->assertOk();
        }

        // Switch the global month and confirm every page still holds up —
        // this is the case each controller's own tests exercise individually,
        // but not all six in the same session together.
        $this->actingAs($admin)->post(route('selected-month.update'), [
            'month' => now()->subMonths(2)->format('Y-m'),
        ])->assertRedirect();

        foreach ($routes as $name) {
            $this->actingAs($admin)->get(route($name))->assertOk();
        }
    }
}
