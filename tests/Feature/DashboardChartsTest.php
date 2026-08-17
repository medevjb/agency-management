<?php

namespace Tests\Feature;

use App\Models\BugReport;
use App\Models\Order;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardChartsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_main_dashboard_renders_task_and_team_revenue_charts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $team = Team::factory()->create(['name' => 'Alpha']);
        Order::factory()->create([
            'team_id' => $team->id,
            'status' => 'delivered',
            'start_date' => now()->startOfMonth(),
            'actual_revenue' => 5000,
        ]);
        Task::factory()->create(['title' => 'Task A', 'status' => 'pending', 'priority' => 'high', 'created_by' => $admin->id]);
        Task::factory()->create(['title' => 'Task B', 'status' => 'completed', 'priority' => 'low', 'created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('taskStats')
            ->assertViewHas('revenueByTeam')
            ->assertSee('teamRevenueChart', false)
            ->assertSee('taskStatusChart', false);
    }

    public function test_order_dashboard_renders_priority_and_trend_charts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Order::factory()->create(['status' => 'in-progress', 'priority' => 'high']);
        Order::factory()->create(['status' => 'completed', 'priority' => 'low']);

        $response = $this->actingAs($admin)->get(route('orders.dashboard'));

        $response->assertOk()
            ->assertViewHas('ordersByPriority')
            ->assertSee('priorityChart', false)
            ->assertSee('trendChart', false);
    }

    public function test_admin_dashboard_renders_task_bug_and_user_charts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Task::factory()->create(['title' => 'Task X', 'status' => 'in-progress', 'created_by' => $admin->id]);
        BugReport::create(['user_id' => $admin->id, 'title' => 'Bug A', 'description' => 'Description A', 'status' => 'pending']);
        BugReport::create(['user_id' => $admin->id, 'title' => 'Bug B', 'description' => 'Description B', 'status' => 'resolved']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('monthlyTasksCreated')
            ->assertViewHas('monthlyTasksCompleted')
            ->assertViewHas('bugStatusStats')
            ->assertViewHas('monthlyNewUsers')
            ->assertViewHas('ordersByPriority')
            ->assertSee('tasksChart', false)
            ->assertSee('bugsChart', false)
            ->assertSee('usersChart', false);
    }
}
