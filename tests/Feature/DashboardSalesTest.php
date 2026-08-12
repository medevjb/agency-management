<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardSalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $topSeller;

    protected User $secondSeller;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->topSeller = User::factory()->create(['name' => 'Ayesha Rahman']);
        $this->topSeller->assignRole('sales-member');

        $this->secondSeller = User::factory()->create(['name' => 'Rafi Islam']);
        $this->secondSeller->assignRole('sales-member');
    }

    public function test_the_main_dashboard_shows_the_sales_total_and_target(): void
    {
        SalesTarget::setFor($this->topSeller->id, now()->startOfMonth(), 6000);
        SalesTarget::setFor($this->secondSeller->id, now()->startOfMonth(), 4000);

        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 6000]);
        Order::factory()->create(['sales_user_id' => $this->secondSeller->id, 'created_by' => $this->secondSeller->id, 'value' => 2000]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sold in')
            ->assertSee('$8,000')          // sold this month
            ->assertSee('Company target')
            ->assertSee('80%')             // 8,000 of the 10,000 target
            ->assertSee('$2,000 to go', false);
    }

    public function test_the_stat_row_has_a_sales_this_month_card(): void
    {
        SalesTarget::setFor($this->topSeller->id, now()->startOfMonth(), 4000);
        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 3000]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sales This Month')
            ->assertSee('$3,000')
            ->assertSee('75% of target');
    }

    public function test_the_sales_card_falls_back_to_the_order_count_without_a_target(): void
    {
        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 900]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sales This Month')
            ->assertSee('$900')
            ->assertSee('1 order sold');
    }

    public function test_the_leaderboard_ranks_sellers_by_what_they_sold(): void
    {
        SalesTarget::setFor($this->topSeller->id, now()->startOfMonth(), 5000);

        Order::factory()->create(['sales_user_id' => $this->secondSeller->id, 'created_by' => $this->secondSeller->id, 'value' => 1200]);
        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 5500]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Top performers')
            ->assertSeeInOrder(['Ayesha Rahman', 'Rafi Islam'])
            ->assertSee('110% of $5,000', false)
            ->assertSee('no target set');
    }

    public function test_the_sales_band_holds_up_with_no_sales_at_all(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sales')
            ->assertSee('No sales targets set yet', false)
            ->assertSee('Nobody has sold anything this month yet.');
    }

    /**
     * Deliberate: operations sees company-wide sales performance, while
     * per-order attribution stays private to sales (see SalesRoleTest).
     */
    public function test_operations_managers_see_the_sales_band_and_leaderboard(): void
    {
        SalesTarget::setFor($this->topSeller->id, now()->startOfMonth(), 5000);
        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 5000]);

        foreach (['operation-manager', 'project-manager'] as $role) {
            $manager = User::factory()->create();
            $manager->assignRole($role);

            $this->actingAs($manager)
                ->get(route('dashboard'))
                ->assertOk()
                ->assertSee('Sold in')
                ->assertSee('Company target')
                ->assertSee('Top performers')
                ->assertSee('Ayesha Rahman')
                ->assertSee('$5,000');
        }
    }

    public function test_orders_without_a_sales_owner_are_not_counted_as_sales(): void
    {
        Order::factory()->create(['created_by' => $this->admin->id, 'value' => 9000, 'sales_user_id' => null]);
        Order::factory()->create(['sales_user_id' => $this->topSeller->id, 'created_by' => $this->topSeller->id, 'value' => 1000]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('$1,000')
            ->assertDontSee('$9,000');
    }
}
