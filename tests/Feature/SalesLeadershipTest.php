<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Profile;
use App\Models\SalesTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesLeadershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $leaderA;

    protected User $leaderB;

    protected User $memberA1;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'team-leader', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->leaderA = User::factory()->create(['name' => 'Leader A']);
        $this->leaderA->assignRole('sales-leader');

        $this->leaderB = User::factory()->create(['name' => 'Leader B']);
        $this->leaderB->assignRole('sales-leader');

        $this->memberA1 = User::factory()->create(['name' => 'Member A1', 'sales_leader_id' => $this->leaderA->id]);
        $this->memberA1->assignRole('sales-member');
    }

    protected function order(User $seller, float $value): Order
    {
        return Order::factory()->create([
            'sales_user_id' => $seller->id,
            'created_by' => $seller->id,
            'value' => $value,
        ]);
    }

    public function test_admin_om_pm_and_sales_leaders_can_reach_it_but_sales_members_cannot(): void
    {
        $om = User::factory()->create();
        $om->assignRole('operation-manager');
        $pm = User::factory()->create();
        $pm->assignRole('project-manager');

        foreach ([$this->admin, $om, $pm, $this->leaderA] as $user) {
            $this->actingAs($user)->get(route('sales.leadership'))->assertOk();
        }

        $this->actingAs($this->memberA1)->get(route('sales.leadership'))->assertForbidden();

        $teamLeader = User::factory()->create();
        $teamLeader->assignRole('team-leader');
        $this->actingAs($teamLeader)->get(route('sales.leadership'))->assertForbidden();
    }

    public function test_it_totals_every_leaders_group_company_wide(): void
    {
        $this->order($this->leaderA, 2000);
        $this->order($this->memberA1, 1000);
        $this->order($this->leaderB, 1500);

        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSee('$4,500.00') // company total
            ->assertSee('Leader A')
            ->assertSee('Leader B')
            ->assertSee('Member A1'); // shows in top performers, not just leader rollup
    }

    public function test_leader_comparison_ranks_groups_by_amount(): void
    {
        $this->order($this->leaderA, 5000); // A's group: 5000
        $this->order($this->leaderB, 1000); // B's group: 1000

        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSeeInOrder(['Leader A', 'Leader B']);
    }

    public function test_attainment_is_shown_against_each_leaders_group_target(): void
    {
        SalesTarget::setFor($this->leaderA->id, now()->startOfMonth(), 4000);
        $this->order($this->leaderA, 2000);
        $this->order($this->memberA1, 2000);

        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSee('100% of $4,000.00', false);
    }

    public function test_the_bridge_links_sold_and_distributed_value(): void
    {
        $order = $this->order($this->leaderA, 1000);
        $order->update(['status' => 'completed']);
        $order->valueDistributions()->create([
            'user_id' => $this->memberA1->id,
            'amount' => 800,
            'distributed_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSee('Distributed', false)
            ->assertSee('$800.00');
    }

    public function test_the_page_holds_up_with_no_sales_at_all(): void
    {
        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSee('Nobody has sold anything this month yet.');
    }

    public function test_the_sidebar_shows_leadership_overview_to_om_but_not_my_sales(): void
    {
        $om = User::factory()->create();
        $om->assignRole('operation-manager');

        $response = $this->actingAs($om)->get(route('orders.index'))->assertOk();

        $response->assertSee('Leadership Overview')
            ->assertDontSee('My Sales');
    }

    public function test_company_sales_are_split_into_fresh_and_repeat(): void
    {
        $profile = Profile::factory()->create();

        // The leader flagged the Acme order as repeat (yellow) when entering
        // it; the member's order is from a brand new client, so it's fresh.
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'sales_user_id' => $this->leaderA->id,
            'created_by' => $this->leaderA->id,
            'name' => 'Acme Corp',
            'value' => 1200,
            'is_repeat' => true,
        ]);
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'sales_user_id' => $this->memberA1->id,
            'created_by' => $this->memberA1->id,
            'name' => 'Brand New',
            'value' => 800,
        ]);

        $this->actingAs($this->admin)
            ->get(route('sales.leadership'))
            ->assertOk()
            ->assertSee('Fresh vs. Repeat')
            ->assertSee('$2,000.00')   // company sold this month
            ->assertSee('$1,200.00')   // repeat
            ->assertSee('$800.00');    // fresh
    }

    public function test_quarterly_progress_table_and_data_is_shown_on_leadership_page(): void
    {
        Carbon::setTestNow('2026-08-16 12:00:00');

        try {
            Order::factory()->create([
                'sales_user_id' => $this->memberA1->id,
                'created_by' => $this->memberA1->id,
                'value' => 3000,
                'created_at' => '2026-07-15 10:00:00',
            ]);

            Order::factory()->create([
                'sales_user_id' => $this->memberA1->id,
                'created_by' => $this->memberA1->id,
                'value' => 1500,
                'created_at' => '2026-08-10 10:00:00',
            ]);

            Order::factory()->create([
                'sales_user_id' => $this->memberA1->id,
                'created_by' => $this->memberA1->id,
                'value' => 2000,
                'created_at' => '2026-09-05 10:00:00',
            ]);

            $response = $this->actingAs($this->admin)
                ->get(route('sales.leadership'))
                ->assertOk();

            $this->assertStringContainsString('Team Quarter Progress', $response->getContent());
            $this->assertStringContainsString('Member A1', $response->getContent());
            $this->assertStringContainsString('$3,000', $response->getContent());
            $this->assertStringContainsString('$1,500', $response->getContent());
            $this->assertStringContainsString('$2,000', $response->getContent());
            $this->assertStringContainsString('$6,500', $response->getContent());
        } finally {
            Carbon::setTestNow();
        }
    }
}
