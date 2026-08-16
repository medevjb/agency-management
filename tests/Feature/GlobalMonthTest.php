<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GlobalMonthTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_the_current_month_when_nothing_is_selected_yet(): void
    {
        $this->assertSame(now()->format('Y-m'), GlobalMonth::key());
    }

    public function test_setting_the_month_persists_across_requests(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $target = now()->subMonths(3)->format('Y-m');

        $this->actingAs($user)
            ->post(route('selected-month.update'), ['month' => $target])
            ->assertRedirect();

        $this->assertSame($target, session('selected_month'));

        // A fresh request in the same session sees the same month, unprompted.
        $this->actingAs($user)->get(route('orders.dashboard'))->assertOk();
        $this->assertSame($target, GlobalMonth::key());
    }

    public function test_the_month_picker_appears_in_the_header_for_authenticated_users(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('orders.dashboard'))
            ->assertOk()
            ->assertSee('month-picker-trigger', false)
            ->assertSee(now()->format('F Y'));
    }

    public function test_an_invalid_month_format_is_rejected(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->post(route('selected-month.update'), ['month' => 'not-a-month'])
            ->assertSessionHasErrors('month');
    }

    public function test_the_period_defaults_to_an_ordinary_calendar_month(): void
    {
        [$start, $end] = GlobalMonth::periodFor(Carbon::parse('2026-08-15'));

        $this->assertSame('2026-08-01 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-31 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_a_configured_cycle_start_day_shifts_the_period_into_the_prior_month(): void
    {
        $this->setCycleStartDay('26');

        $this->assertSame(26, GlobalMonth::cycleStartDay());

        [$start, $end] = GlobalMonth::periodFor(Carbon::parse('2026-08-15'));

        // "August" runs July 26 through August 25.
        $this->assertSame('2026-07-26 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-25 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_the_cycle_start_day_clamps_to_a_valid_range(): void
    {
        $this->setCycleStartDay('99');
        $this->assertSame(28, GlobalMonth::cycleStartDay());

        $this->setCycleStartDay('0');
        $this->assertSame(1, GlobalMonth::cycleStartDay());
    }

    private function setCycleStartDay(string $value): void
    {
        Setting::updateOrCreate(
            ['key' => 'month_cycle_start_day'],
            ['value' => $value, 'group' => 'general', 'type' => 'number', 'label' => 'Month Cycle Start Day']
        );
    }
}
