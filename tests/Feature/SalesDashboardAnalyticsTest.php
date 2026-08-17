<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SalesTarget;
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

    protected function sold(User $seller, string $name, float $value, array $extra = []): Order
    {
        // The OrderObserver credits the sales owner automatically.
        return Order::factory()->create(array_merge([
            'sales_user_id' => $seller->id,
            'created_by' => $seller->id,
            'name' => $name,
            'value' => $value,
        ], $extra));
    }

    public function test_the_month_end_projection_judges_the_pace_against_the_target(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 5000);
        $this->sold($this->member, 'Acme Corp', 1000);

        $response = $this->actingAs($this->member)
            ->get(route('sales.dashboard'));
        file_put_contents(base_path('scratch_response.html'), $response->getContent());
        $response->assertOk()
            ->assertSee('Month-end projection')
            ->assertSee('Behind pace');
    }

    public function test_the_projection_shows_on_track_when_the_pace_clears_the_target(): void
    {
        SalesTarget::setFor($this->member->id, now()->startOfMonth(), 1000);
        $this->sold($this->member, 'Acme Corp', 1000);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('On track');
    }

    public function test_the_delivery_pipeline_shows_expected_revenue_and_flags_overdue_orders(): void
    {
        $this->sold($this->member, 'Overdue Deal', 1000, [
            'status' => 'in-progress',
            'progress' => 50,
            'end_date' => '2026-08-10',
        ]);
        $this->sold($this->member, 'On Time Deal', 2000, [
            'status' => 'in-progress',
            'progress' => 25,
            'end_date' => '2026-08-31',
        ]);
        $this->sold($this->member, 'Already Delivered', 500, ['status' => 'delivered']);

        $response = $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk();

        $this->assertStringContainsString('Delivery pipeline', $response->getContent());
        $this->assertStringContainsString('$3,000.00', $response->getContent()); // total in delivery
        $this->assertStringContainsString('$1,000.00', $response->getContent()); // expected revenue
        $this->assertStringContainsString('Overdue Deal', $response->getContent());
        $this->assertStringContainsString('6d overdue', $response->getContent());
        $this->assertStringContainsString('On Time Deal', $response->getContent());
    }

    public function test_the_pipeline_shows_a_clean_state_when_nothing_is_overdue(): void
    {
        $this->sold($this->member, 'On Time Deal', 2000, [
            'status' => 'in-progress',
            'end_date' => '2026-08-31',
        ]);

        $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Nothing overdue', false);
    }

    public function test_top_clients_are_ranked_by_revenue_and_repeat_orders_stay_grouped(): void
    {
        $this->sold($this->member, 'Acme Corp', 1200, ['created_at' => '2026-08-01', 'client_key' => 'acme corp']);
        $this->sold($this->member, 'Acme Worldwide', 800, [
            'created_at' => '2026-08-10',
            'client_key' => 'acme worldwide',
            'repeat_client_key' => 'acme corp',
            'is_repeat' => true,
        ]);
        $this->sold($this->member, 'Globex', 900, ['created_at' => '2026-08-05', 'client_key' => 'globex']);

        $response = $this->actingAs($this->member)
            ->get(route('sales.dashboard'))
            ->assertOk();

        // The renamed repeat order rolls up under the tracked client as its
        // display name, outranking Globex by combined revenue.
        $this->assertStringContainsString('Top clients', $response->getContent());
        $this->assertStringContainsString('Acme Worldwide', $response->getContent());
        $this->assertStringContainsString('×1 repeat', $response->getContent());
        $this->assertTrue(strpos($response->getContent(), 'Acme Worldwide') < strpos($response->getContent(), 'Globex'));
    }

    public function test_admin_sees_the_pipeline_across_every_sales_group(): void
    {
        $this->sold($this->member, 'Company Wide Deal', 1500, [
            'status' => 'in-progress',
            'end_date' => '2026-08-10',
        ]);

        $this->actingAs($this->admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Delivery pipeline')
            ->assertSee('Company Wide Deal')
            ->assertSee('6d overdue');
    }
}
