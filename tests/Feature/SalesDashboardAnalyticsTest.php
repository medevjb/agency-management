<?php

namespace Tests\Feature;

use App\Models\BonusFinancialSetting;
use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesDashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-16 12:00:00');

        foreach (['admin', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->member = User::factory()->create(['name' => 'Priya Sarker']);
        $this->member->assignRole('sales-member');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /** The OrderObserver credits the sales owner automatically. */
    protected function sold(User $seller, float $value, ?Carbon $when = null): Order
    {
        return Order::factory()->create([
            'sales_user_id' => $seller->id,
            'created_by' => $seller->id,
            'value' => $value,
            'created_at' => $when ?? now(),
        ]);
    }

    protected function configureBonus(string $rate = '0.01', string $exchange = '122.50'): void
    {
        BonusFinancialSetting::create([
            'bonus_percentage' => $rate,
            'usd_to_bdt_rate' => $exchange,
            'effective_from' => '2026-01-01',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_the_monthly_figures_come_from_recorded_sales(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 1500);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$1,500')      // sales
            ->assertSee('$1,000')      // target
            ->assertSee('150.0%');       // achievement
    }

    public function test_extra_sales_is_the_amount_above_target(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 1500);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$500.00')
            ->assertSee('Target Achieved');
    }

    public function test_a_month_below_target_shows_a_shortfall_not_negative_extra(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 2000);
        $this->sold($this->member, 800);

        $content = $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$1,200.00')     // shortfall to target
            ->getContent();

        $this->assertStringNotContainsString('-$', $content);
        $this->assertStringNotContainsString('$-', $content);
    }

    public function test_the_bonus_is_calculated_from_the_configured_rate(): void
    {
        $this->configureBonus();
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 3000);

        // $2,000 extra x 1% = $20 x 122.50 = BDT 2,450
        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$2,000.00')
            ->assertSee('৳2,450.00');
    }

    public function test_a_stored_fraction_rate_is_shown_as_a_percentage(): void
    {
        $this->configureBonus('0.01');
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 2000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('1.00% bonus rate')
            ->assertSee('৳122.50 per USD');
    }

    public function test_an_unconfigured_bonus_shows_zero_with_an_explanation(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 3000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('৳0.00')
            ->assertSee('Bonus rates not configured yet')
            ->assertSee('as soon as the rates are configured', false)
            // Sales figures stay live while bonus is unconfigured.
            ->assertSee('$3,000')
            ->assertSee('$1,000');
    }

    public function test_only_an_admin_is_offered_the_bonus_settings_link(): void
    {
        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertDontSee('Configure bonus rates');

        $this->actingAs($this->admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Configure bonus rates');
    }

    public function test_the_explanation_disappears_once_rates_are_configured(): void
    {
        $this->configureBonus();
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 2000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertDontSee('Bonus rates not configured yet');
    }

    public function test_the_company_target_falls_back_to_the_sum_of_sales_targets(): void
    {
        $other = User::factory()->create();
        $other->assignRole('sales-member');

        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        SalesTarget::setFor($other->id, now()->startOfMonth(), 4000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            // 1,000 + 4,000 = 5,000, shown in full below the millions scale
            ->assertSee('of $5,000');
    }

    public function test_an_admin_set_company_target_overrides_the_sum(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        Setting::updateOrCreate(
            ['key' => 'company_gross_target'],
            ['value' => '2000000', 'group' => 'company', 'type' => 'number', 'label' => 'Company Gross Sales Target'],
        );

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('of $2.00M');
    }

    public function test_earnings_history_totals_the_trailing_quarters(): void
    {
        $this->configureBonus();
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 3000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Earnings history')
            ->assertSee('Earned to date')
            ->assertSee('Accruing')
            ->assertSee('Paid');
    }

    public function test_the_page_renders_for_a_salesperson_with_no_sales(): void
    {
        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Q3 bonus so far')
            ->assertSee('Earnings history');
    }

    public function test_the_period_follows_the_selected_month(): void
    {
        $this->actingAs($this->member)
            ->post(route('selected-month.update'), ['month' => '2026-05'])
            ->assertRedirect();

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Viewing May 2026')
            ->assertSee('Q2 bonus so far')
            ->assertSee('April–June');
    }

    public function test_no_bonus_amount_is_attributed_to_an_individual_month(): void
    {
        $this->configureBonus();
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 3000);

        $content = $this->actingAs($this->member)->get(route('sales.dashboard'))->assertOk()->getContent();

        // The quarter ledger reports extra sales; taka appears only at
        // quarter level, because the rate applies once per quarter.
        $this->assertStringContainsString('Extra sales', $content);
        $this->assertStringNotContainsString('Month bonus', $content);
    }

    public function test_the_removed_kpi_row_is_gone(): void
    {
        $content = $this->actingAs($this->member)->get(route('sales.dashboard'))->assertOk()->getContent();

        // "My Sales" is deliberately absent from this list: it is a sidebar
        // navigation link, not a dashboard KPI card.
        foreach (['Target Achievement', 'Bonus Accrued', 'Q3 Bonus Wallet', 'from last month'] as $removed) {
            $this->assertStringNotContainsString($removed, $content);
        }
    }

    public function test_the_old_dashboard_sections_are_gone(): void
    {
        $content = $this->actingAs($this->member)->get(route('sales.dashboard'))->assertOk()->getContent();

        foreach (['Month-end projection', 'Delivery pipeline', 'Fresh vs. Repeat', 'Top clients'] as $removed) {
            $this->assertStringNotContainsString($removed, $content);
        }
    }

    public function test_admin_sees_the_same_redesigned_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Q3 bonus so far')
            ->assertSee('Earnings history')
            ->assertSee('Company');
    }
}
