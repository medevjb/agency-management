<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_authenticated_users_can_access_task_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_assign_multiple_users_to_task()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'created_by' => $admin->id,
        ]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('tasks.assign-users', $task), [
            'users' => [$user1->id, $user2->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $user1->id,
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $user2->id,
        ]);
    }

    public function test_task_has_many_users_relationship()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'created_by' => $admin->id,
        ]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task->users()->attach($user1->id, [
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $task->users()->attach($user2->id, [
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $this->assertCount(2, $task->users);
        $this->assertTrue($task->users->contains($user1));
        $this->assertTrue($task->users->contains($user2));
    }

    public function test_sales_cannot_manage_task_assignments()
    {
        $salesUser = User::factory()->create();
        $salesUser->assignRole('sales-member');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'created_by' => $salesUser->id,
        ]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($salesUser)->post(route('tasks.assign-users', $task), [
            'users' => [$otherUser->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_assignee_search_filters_users_by_name_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        User::factory()->create(['name' => 'Alice Smith']);
        User::factory()->create(['name' => 'Bob Jones']);

        $response = $this->actingAs($admin)->getJson(route('tasks.search-users').'?search=Ali');

        $response->assertOk()
            ->assertJsonCount(1, 'users')
            ->assertJsonPath('users.0.name', 'Alice Smith');
    }

    public function test_assignee_search_lists_all_users_for_admin_when_no_term()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $userA = User::factory()->create(['name' => 'Alice Smith']);
        $userB = User::factory()->create(['name' => 'Bob Jones']);

        $response = $this->actingAs($admin)->getJson(route('tasks.search-users'));

        $response->assertOk();
        $ids = collect($response->json('users'))->pluck('id');
        $this->assertTrue($ids->contains($userA->id));
        $this->assertTrue($ids->contains($userB->id));
    }

    public function test_assignee_search_is_scoped_to_team_for_team_leader()
    {
        $leader = User::factory()->create();
        $leader->assignRole('team-leader');
        $team = Team::factory()->create(['team_leader_id' => $leader->id]);
        $leader->update(['team_id' => $team->id]);

        $teammate = User::factory()->create(['name' => 'Alice Smith', 'team_id' => $team->id]);
        $outsider = User::factory()->create(['name' => 'Bob Jones', 'team_id' => Team::factory()->create()->id]);

        $response = $this->actingAs($leader)->getJson(route('tasks.search-users').'?search=Alice');

        $response->assertOk();
        $ids = collect($response->json('users'))->pluck('id');
        $this->assertTrue($ids->contains($teammate->id));
        $this->assertFalse($ids->contains($outsider->id));
    }
}
