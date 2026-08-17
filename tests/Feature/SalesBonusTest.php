<?php

namespace Tests\Feature;

use App\Models\BonusFinancialSetting;
use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\User;
use App\Services\SalesBonusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesBonusTest extends TestCase
{
    use RefreshDatabase;

    protected User $salesperson;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'sales-member']);
        $this->salesperson = User::factory()->create();
        $this->salesperson->assignRole('sales-member');
    }

    public function test_it_calculates_a_one_percent_bonus_in_bdt_using_the_admin_rate(): void
    {
        BonusFinancialSetting::create(['bonus_percentage' => '0.010000', 'usd_to_bdt_rate' => '122.5000', 'effective_from' => '2026-01-01']);
        SalesTarget::setFor($this->salesperson->id, Carbon::parse('2026-08-01'), 100000);
        Order::factory()->create(['sales_user_id' => $this->salesperson->id, 'created_by' => $this->salesperson->id, 'value' => 125000, 'created_at' => '2026-08-15']);
        $bonus = app(SalesBonusService::class)->calculate($this->salesperson, Carbon::parse('2026-08-16'));

        $this->assertSame('25000.00', $bonus['qualified_extra_sales']);
        $this->assertSame('250.000000', $bonus['bonus_source_amount']);
        $this->assertSame('30625.00', $bonus['bonus_payment_amount']);
    }

    public function test_it_supports_a_changed_bonus_percentage(): void
    {
        BonusFinancialSetting::create(['bonus_percentage' => '0.015000', 'usd_to_bdt_rate' => '122.0000', 'effective_from' => '2026-01-01']);
        SalesTarget::setFor($this->salesperson->id, Carbon::parse('2026-08-01'), 100000);
        Order::factory()->create(['sales_user_id' => $this->salesperson->id, 'created_by' => $this->salesperson->id, 'value' => 120000, 'created_at' => '2026-08-15']);
        $bonus = app(SalesBonusService::class)->calculate($this->salesperson, Carbon::parse('2026-08-16'));

        $this->assertSame('300.000000', $bonus['bonus_source_amount']);
        $this->assertSame('36600.00', $bonus['bonus_payment_amount']);
    }

    public function test_a_finalized_bonus_keeps_its_original_rate_and_percentage(): void
    {
        BonusFinancialSetting::create(['bonus_percentage' => '0.010000', 'usd_to_bdt_rate' => '120.0000', 'effective_from' => '2026-01-01']);
        SalesTarget::setFor($this->salesperson->id, Carbon::parse('2026-01-01'), 100000);
        Order::factory()->create(['sales_user_id' => $this->salesperson->id, 'created_by' => $this->salesperson->id, 'value' => 150000, 'created_at' => '2026-01-15']);
        $service = app(SalesBonusService::class);
        $finalized = $service->finalize($this->salesperson, Carbon::parse('2026-01-31'));
        BonusFinancialSetting::create(['bonus_percentage' => '0.015000', 'usd_to_bdt_rate' => '125.0000', 'effective_from' => '2026-04-01']);
        $bonus = $service->calculate($this->salesperson, Carbon::parse('2026-01-31'));

        $this->assertSame('0.010000', $finalized->bonus_percentage_applied);
        $this->assertSame('120.0000', $finalized->exchange_rate_applied);
        $this->assertSame('60000.00', $bonus['bonus_payment_amount']);
    }

    public function test_only_an_admin_can_save_bonus_financial_settings(): void
    {
        $payload = ['bonus_percentage' => '0.010000', 'usd_to_bdt_rate' => '122.5000', 'effective_from' => '2026-08-01'];
        $this->actingAs($this->salesperson)->post(route('settings.sales-bonus.store'), $payload)->assertForbidden();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin)->post(route('settings.sales-bonus.store'), $payload)->assertRedirect(route('settings.sales-bonus.index'));
        $this->assertDatabaseHas('bonus_financial_settings', ['created_by' => $admin->id, 'usd_to_bdt_rate' => '122.5000']);
    }
}
