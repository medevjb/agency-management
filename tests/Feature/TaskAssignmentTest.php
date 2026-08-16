<?php

namespace Tests\Feature;

use App\Models\Task;
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
}
