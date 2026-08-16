<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderSalesShare;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    public function test_repeat_orders_follow_the_stored_manual_flag_scoped_to_the_profile(): void
    {
        $profileA = Profile::factory()->create();
        $profileB = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profileA->id, 'name' => 'Acme Corp']);
        $this->travel(1)->days();
        // The salesperson flagged this order as repeat when entering it.
        Order::factory()->create(['freelance_profile_id' => $profileA->id, 'name' => 'Acme Corp', 'is_repeat' => true]);
        $this->travel(1)->days();
        // Same client name, but on a different profile — not flagged, so fresh.
        Order::factory()->create(['freelance_profile_id' => $profileB->id, 'name' => 'Acme Corp']);

        $profileA->refresh();
        $analyticsA = new ProfileAnalytics($profileA);

        $this->assertSame(2, $analyticsA->totalOrders());
        $this->assertSame(1, $analyticsA->uniqueClientCount());
        $this->assertSame(1, $analyticsA->repeatClientCount());
        $this->assertSame(0, $analyticsA->newClientCount());
        $this->assertSame(1, $analyticsA->repeatOrderCount());
        $this->assertSame(1, $analyticsA->newOrderCount());
        $this->assertSame(50.0, $analyticsA->repeatOrderRate());

        $profileB->refresh();
        $analyticsB = new ProfileAnalytics($profileB);

        $this->assertSame(1, $analyticsB->totalOrders());
        $this->assertSame(0, $analyticsB->repeatOrderCount());
        $this->assertSame(0.0, $analyticsB->repeatOrderRate());
    }

    public function test_different_client_names_are_never_treated_as_repeat(): void
    {
        $profile = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp']);
        $this->travel(1)->days();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Widget LLC']);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $this->assertSame(2, $analytics->totalOrders());
        $this->assertSame(2, $analytics->uniqueClientCount());
        $this->assertSame(0, $analytics->repeatOrderCount());
        $this->assertSame(0.0, $analytics->repeatOrderRate());
    }

    public function test_a_repeat_order_is_grouped_under_the_tracked_client_even_when_renamed(): void
    {
        $profile = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500]);
        $this->travel(1)->days();
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'name' => 'Acme Worldwide',
            'value' => 700,
            'is_repeat' => true,
            'repeat_client_key' => 'acme corp',
        ]);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $this->assertSame(1, $analytics->uniqueClientCount());
        $this->assertSame(1, $analytics->repeatClientCount());
        $this->assertSame(0, $analytics->newClientCount());
        $this->assertSame(1, $analytics->repeatOrderCount());

        $topClients = $analytics->topRepeatClients();
        $this->assertCount(1, $topClients);
        $this->assertSame('acme corp', $topClients[0]['key']);
        $this->assertSame('Acme Worldwide', $topClients[0]['name']);
        $this->assertSame(2, $topClients[0]['orders']);
    }

    public function test_top_repeat_clients_lists_only_clients_with_a_repeat_flagged_order(): void
    {
        $profile = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500]);
        $this->travel(1)->days();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 700, 'is_repeat' => true]);
        $this->travel(1)->days();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'One Timer', 'value' => 300]);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $topClients = $analytics->topRepeatClients();

        $this->assertCount(1, $topClients);
        $this->assertSame('Acme Corp', $topClients[0]['name']);
        $this->assertSame(2, $topClients[0]['orders']);
        $this->assertSame(1200.0, $topClients[0]['total']);
    }

    public function test_month_scoped_methods_exclude_previous_month_orders(): void
    {
        $profile = Profile::factory()->create();

        $this->travel(-1)->months();
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'name' => 'Old Client',
            'value' => 999,
            'status' => 'completed',
        ]);
        $this->travelBack();

        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'name' => 'This Month Client',
            'value' => 250,
            'status' => 'in-progress',
        ]);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        // All-time totals are untouched — only the month-scoped view changes.
        $this->assertSame(2, $analytics->totalOrders());

        $this->assertSame(1, $analytics->monthOrderCount(now()));
        $this->assertSame(250.0, $analytics->monthRevenue(now()));
        $this->assertSame(['in-progress' => 1], $analytics->monthStatusBreakdown(now()));

        $recent = $analytics->monthRecentOrders(now());
        $this->assertCount(1, $recent);
        $this->assertSame('This Month Client', $recent->first()->name);
    }

    public function test_month_sales_ranking_credits_each_seller_by_their_share(): void
    {
        $profile = Profile::factory()->create();
        $leader = User::factory()->create(['name' => 'Top Seller']);
        $member = User::factory()->create(['name' => 'Second Seller']);

        $order1 = Order::factory()->create(['freelance_profile_id' => $profile->id, 'value' => 1000]);
        OrderSalesShare::create(['order_id' => $order1->id, 'user_id' => $leader->id, 'amount' => 1000]);

        $order2 = Order::factory()->create(['freelance_profile_id' => $profile->id, 'value' => 400]);
        OrderSalesShare::create(['order_id' => $order2->id, 'user_id' => $member->id, 'amount' => 400]);

        $order3 = Order::factory()->create(['freelance_profile_id' => $profile->id, 'value' => 200]);
        OrderSalesShare::create(['order_id' => $order3->id, 'user_id' => $leader->id, 'amount' => 200]);

        $profile->load('orders.salesShares.user');
        $analytics = new ProfileAnalytics($profile);

        $ranking = $analytics->monthSalesRanking(now());

        $this->assertCount(2, $ranking);
        $this->assertSame('Top Seller', $ranking[0]['name']);
        $this->assertSame(1200.0, $ranking[0]['amount']);
        $this->assertSame(2, $ranking[0]['orders']);
        $this->assertSame('Second Seller', $ranking[1]['name']);
        $this->assertSame(400.0, $ranking[1]['amount']);
    }

    public function test_status_breakdown_counts_orders_by_status(): void
    {
        $profile = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'status' => 'delivered']);
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'status' => 'delivered']);
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'status' => 'in-progress']);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $breakdown = $analytics->statusBreakdown();

        $this->assertSame(2, $breakdown['delivered']);
        $this->assertSame(1, $breakdown['in-progress']);
    }

    public function test_month_fresh_and_repeat_revenue_split_by_manual_flag(): void
    {
        $profile = Profile::factory()->create();

        $this->travel(-1)->months();
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'name' => 'Acme Corp',
            'value' => 900,
            'status' => 'completed',
        ]);
        $this->travelBack();

        // The salesperson flagged this month's Acme order as repeat (yellow).
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500, 'is_repeat' => true]);
        // A brand new client is fresh (green).
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Brand New', 'value' => 300]);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $this->assertSame(800.0, $analytics->monthRevenue(now()));
        $this->assertSame(500.0, $analytics->monthRepeatRevenue(now()));
        $this->assertSame(300.0, $analytics->monthFreshRevenue(now()));

        $split = $analytics->monthFreshRepeatSplit(now());

        $this->assertSame(['orders' => 1, 'revenue' => 300.0], $split['fresh']);
        $this->assertSame(['orders' => 1, 'revenue' => 500.0], $split['repeat']);
    }

    public function test_monthly_revenue_split_buckets_fresh_and_repeat_per_month(): void
    {
        $profile = Profile::factory()->create();

        $this->travel(-1)->months();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 900, 'status' => 'completed']);
        $this->travelBack();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500, 'is_repeat' => true]);
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Brand New', 'value' => 300]);

        $profile->refresh();
        $analytics = new ProfileAnalytics($profile);

        $points = $analytics->monthlyRevenueSplit(6);

        $this->assertCount(6, $points);
        $last = $points[5];

        $this->assertSame(300.0, $last['fresh']);
        $this->assertSame(500.0, $last['repeat']);
        $this->assertSame(0.0, $points[0]['fresh']);
        $this->assertSame(0.0, $points[0]['repeat']);
    }
}
