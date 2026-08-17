<?php

namespace Tests\Feature;

use App\Models\TechStack;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechStackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_access_tech_stack_create_page_with_searchable_leader_picker()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        User::factory()->create(['name' => 'Alice Smith', 'employee_id' => 'EMP-001']);
        User::factory()->create(['name' => 'Bob Jones', 'employee_id' => 'EMP-002']);

        $response = $this->actingAs($admin)->get(route('tech-stacks.create'));

        $response->assertOk()
            ->assertViewHas('usersJson')
            ->assertSee('stackLeaderPickerTrigger', false)
            ->assertSee('name="stack_leader_id"', false)
            ->assertSee('Search by name or employee ID', false);
    }

    public function test_admin_can_access_tech_stack_edit_page_with_selected_leader_picker()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $leader = User::factory()->create(['name' => 'Alice Smith', 'employee_id' => 'EMP-001']);
        $techStack = TechStack::factory()->create(['stack_leader_id' => $leader->id]);

        $response = $this->actingAs($admin)->get(route('tech-stacks.edit', $techStack));

        $response->assertOk()
            ->assertViewHas('usersJson')
            ->assertSee('stackLeaderPickerTrigger', false)
            ->assertSee('id="stackLeaderPicker" value="'.$leader->id.'"', false);
    }
}
