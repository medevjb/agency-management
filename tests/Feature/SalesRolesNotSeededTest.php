<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesRolesNotSeededTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Spatie's `role` scope throws RoleDoesNotExist when a role is missing, so
     * anything listing sales people must not use it — an install that has not
     * run the role seeder yet should still render.
     */
    public function test_pages_render_before_the_sales_roles_are_seeded(): void
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('users.create'))->assertOk();
        $this->actingAs($admin)->get(route('sales.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('orders.index'))->assertOk();
    }
}
