<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProfileAnalyticsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view profiles']);
    }

    public function test_index_page_shows_company_wide_and_per_profile_repeat_stats(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $profile = Profile::factory()->create(['profile_name' => 'Jane Freelancer']);

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500]);
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500, 'is_repeat' => true]);

        $response = $this->actingAs($user)->get(route('freelance-profiles.index'));

        $response->assertOk()
            ->assertSee('Repeat Order Rate')
            ->assertSee('Jane Freelancer')
            ->assertSee('1/1 repeat clients', false);
    }

    public function test_index_summary_aggregates_revenue_repeat_rate_and_top_profile(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $repeater = Profile::factory()->create(['profile_name' => 'Repeater Profile']);
        $oneOff = Profile::factory()->create(['profile_name' => 'One-off Profile']);

        // 1 of the 3 orders this month is repeat business for Repeater Profile.
        Order::factory()->create(['freelance_profile_id' => $repeater->id, 'name' => 'Acme Corp', 'value' => 100]);
        Order::factory()->create(['freelance_profile_id' => $repeater->id, 'name' => 'Acme Corp', 'value' => 100, 'is_repeat' => true]);
        Order::factory()->create(['freelance_profile_id' => $oneOff->id, 'name' => 'Other Client', 'value' => 300]);

        $response = $this->actingAs($user)->get(route('freelance-profiles.index'));

        $response->assertOk()
            ->assertSee('1 of 3 this month were repeat business', false)
            ->assertSee('33.3%', false)
            ->assertSee('$500.00', false)
            // Top profile by revenue this month wins the summary card.
            ->assertSee('One-off Profile');
    }

    public function test_index_repeat_orders_are_scoped_per_profile(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $profileA = Profile::factory()->create(['profile_name' => 'Profile A']);
        $profileB = Profile::factory()->create(['profile_name' => 'Profile B']);

        // Same client name on both profiles: a repeat only within each profile.
        Order::factory()->create(['freelance_profile_id' => $profileA->id, 'name' => 'Acme Corp']);
        Order::factory()->create(['freelance_profile_id' => $profileA->id, 'name' => 'Acme Corp', 'is_repeat' => true]);
        Order::factory()->create(['freelance_profile_id' => $profileB->id, 'name' => 'Acme Corp']);

        $response = $this->actingAs($user)->get(route('freelance-profiles.index'));

        $response->assertOk()
            ->assertSee('1 of 3 this month were repeat business', false);
    }

    public function test_show_page_displays_kpis_and_new_repeat_badges(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $profile = Profile::factory()->create(['profile_name' => 'Jane Freelancer']);

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500]);
        $this->travel(1)->days();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500, 'is_repeat' => true]);
        $this->travelBack();

        $response = $this->actingAs($user)->get(route('freelance-profiles.show', $profile));

        $response->assertOk()
            ->assertSee('Fresh vs. Repeat Business')
            ->assertSee('Top Repeat Clients')
            ->assertSee('Recent Orders')
            ->assertSee('Repeat Order Rate')
            ->assertSee('Repeat')
            ->assertSee('New')
            ->assertSee('Acme Corp');
    }

    public function test_show_page_lists_a_repeat_client_once_it_orders_twice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $profile = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 500]);
        $this->travel(1)->days();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 700, 'is_repeat' => true]);
        $this->travelBack();

        $response = $this->actingAs($user)->get(route('freelance-profiles.show', $profile));

        $response->assertOk()->assertSee('$1,200.00', false);
    }

    public function test_a_repeat_client_on_one_profile_is_not_a_repeat_client_on_another(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $profileA = Profile::factory()->create();
        $profileB = Profile::factory()->create();

        Order::factory()->create(['freelance_profile_id' => $profileA->id, 'name' => 'Acme Corp']);
        Order::factory()->create(['freelance_profile_id' => $profileB->id, 'name' => 'Acme Corp']);

        $response = $this->actingAs($user)->get(route('freelance-profiles.show', $profileB));

        $response->assertOk()->assertSee('No repeat clients this month');
    }

    public function test_show_page_requires_view_profiles_permission(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create();

        $this->actingAs($user)
            ->get(route('freelance-profiles.show', $profile))
            ->assertForbidden();
    }
}
