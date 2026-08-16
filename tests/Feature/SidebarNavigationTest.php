<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'sales-leader', 'sales-member', 'team-leader'] as $role) {
            Role::create(['name' => $role]);
        }
    }

    public function test_value_distribution_lives_inside_the_orders_section_not_its_own(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('orders.index'))->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Value Distribution', $html);

        // The old standalone section had its own data-section="value-distribution"
        // wrapper; that must be gone now that the link lives under Orders.
        $this->assertStringNotContainsString('data-section="value-distribution"', $html);
    }

    public function test_team_leader_still_sees_value_distribution_under_orders(): void
    {
        $teamLeader = User::factory()->create();
        $teamLeader->assignRole('team-leader');

        $this->actingAs($teamLeader)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Value Distribution');
    }

    public function test_sales_leader_and_sales_member_roles_are_selectable_when_creating_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSee('value="sales-leader"', false)
            ->assertSee('value="sales-member"', false);
    }
}
