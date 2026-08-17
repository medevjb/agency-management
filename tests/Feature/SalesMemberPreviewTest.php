<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesMemberPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'sales-leader', 'sales-member', 'team-leader'] as $role) {
            Role::create(['name' => $role]);
        }
    }

    public function test_admin_om_and_pm_can_see_the_sample_member_dashboard(): void
    {
        foreach (['admin', 'operation-manager', 'project-manager'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route('sales.dashboard.preview'))
                ->assertOk()
                ->assertSee('Preview')
                ->assertSee('sample data', false)
                ->assertSee('Q3 bonus so far')
                ->assertSee('$128,500'); // the fabricated "you sold" figure
        }
    }

    public function test_sales_people_cannot_reach_the_admin_preview(): void
    {
        $member = User::factory()->create();
        $member->assignRole('sales-member');

        $leader = User::factory()->create();
        $leader->assignRole('sales-leader');

        $this->actingAs($member)->get(route('sales.dashboard.preview'))->assertForbidden();
        $this->actingAs($leader)->get(route('sales.dashboard.preview'))->assertForbidden();
    }

    public function test_the_preview_never_links_to_a_real_order(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('sales.dashboard.preview'))->assertOk();

        // The redesigned dashboard is mock-driven — it never links to a real
        // order detail page (only generic list/create routes in the sidebar).
        $this->assertDoesNotMatchRegularExpression('#orders/\d+#', $response->getContent());
    }

    public function test_the_preview_route_is_listed_in_the_sidebar_for_admin_only(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $salesMember = User::factory()->create();
        $salesMember->assignRole('sales-member');

        $this->actingAs($admin)->get(route('orders.index'))->assertOk()->assertSee('Member Dashboard Preview');
        $this->actingAs($salesMember)->get(route('orders.index'))->assertOk()->assertDontSee('Member Dashboard Preview');
    }
}
