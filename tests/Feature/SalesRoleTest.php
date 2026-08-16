<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderSalesShare;
use App\Models\Profile;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $salesLeader;

    protected User $salesMember;

    protected User $otherSalesMember;

    protected User $operationManager;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'sales-leader', 'sales-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->salesLeader = User::factory()->create(['name' => 'Sales Leader']);
        $this->salesLeader->assignRole('sales-leader');

        $this->salesMember = User::factory()->create([
            'name' => 'Sales Member',
            'sales_leader_id' => $this->salesLeader->id,
        ]);
        $this->salesMember->assignRole('sales-member');

        $this->otherSalesMember = User::factory()->create(['name' => 'Other Seller']);
        $this->otherSalesMember->assignRole('sales-member');

        $this->operationManager = User::factory()->create(['name' => 'Ops Manager']);
        $this->operationManager->assignRole('operation-manager');
    }

    public function test_sales_person_creates_an_order_from_the_deal_facts_alone(): void
    {
        $response = $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Northwind Trading',
            'value' => 1000,
            'notes' => 'Client wants a Shopify store.',
        ]);

        $response->assertRedirect(route('orders.index'));

        $order = Order::first();
        $this->assertSame($this->salesMember->id, $order->sales_user_id);
        $this->assertSame('Northwind Trading', $order->name);
        $this->assertSame(1000.0, (float) $order->value);
        $this->assertSame(800.0, (float) $order->actual_revenue);
        $this->assertSame('pending', $order->status);
        $this->assertSame(0, $order->progress);
        $this->assertNull($order->team_id);
    }

    public function test_operational_fields_sent_by_sales_are_ignored_on_create(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Contoso',
            'value' => 500,
            'status' => 'delivered',
            'progress' => 90,
            'team_id' => $team->id,
            'end_date' => now()->addDays(5)->toDateString(),
        ]);

        $order = Order::first();
        $this->assertSame('pending', $order->status);
        $this->assertSame(0, $order->progress);
        $this->assertNull($order->team_id);
        $this->assertNull($order->end_date);
    }

    public function test_a_sales_person_marks_an_order_as_repeat_when_entering_it(): void
    {
        $profile = Profile::factory()->create();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp', 'value' => 800]);

        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Acme Corp',
            'value' => 1200,
            'freelance_profile_id' => $profile->id,
            'is_repeat' => 1,
            'repeat_client_key' => 'acme corp',
        ])->assertRedirect(route('orders.index'));

        $repeatOrder = Order::where('name', 'Acme Corp')->orderByDesc('id')->first();
        $this->assertTrue($repeatOrder->is_repeat);
        $this->assertSame('acme corp', $repeatOrder->repeat_client_key);
        $this->assertSame(1, Order::where('name', 'Acme Corp')->where('is_repeat', true)->count());
    }

    public function test_a_repeat_order_tracks_the_picked_client_even_when_the_name_differs(): void
    {
        $profile = Profile::factory()->create();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp']);

        // The client renamed — the salesperson types the new name but picks
        // the existing client from the picker, so the repeat is attributed to
        // that client (acme corp), not to the freshly typed name.
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Acme Worldwide',
            'value' => 950,
            'freelance_profile_id' => $profile->id,
            'is_repeat' => 1,
            'repeat_client_key' => 'acme corp',
        ])->assertRedirect(route('orders.index'));

        $repeatOrder = Order::where('name', 'Acme Worldwide')->first();
        $this->assertTrue($repeatOrder->is_repeat);
        $this->assertSame('acme corp', $repeatOrder->repeat_client_key);
        $this->assertSame('acme worldwide', $repeatOrder->client_key);
    }

    public function test_a_sales_person_leaves_an_order_fresh_by_default(): void
    {
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Brand New Client',
            'value' => 600,
        ])->assertRedirect(route('orders.index'));

        $this->assertFalse(Order::first()->is_repeat);
    }

    public function test_a_repeat_order_may_reuse_the_existing_client_name(): void
    {
        $profile = Profile::factory()->create();
        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp']);

        // The client name no longer has to be unique — a repeat order for the
        // same client is exactly what the Fresh/Repeat control produces.
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Acme Corp',
            'value' => 900,
            'freelance_profile_id' => $profile->id,
            'is_repeat' => 1,
            'repeat_client_key' => 'acme corp',
        ])->assertRedirect(route('orders.index'));

        $this->assertSame(2, Order::where('name', 'Acme Corp')->count());
    }

    public function test_a_sales_person_can_change_an_orders_fresh_repeat_flag(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
        ]);
        $this->assertFalse($order->is_repeat);

        $this->actingAs($this->salesMember)->patch(route('orders.update', $order), [
            'name' => $order->name,
            'is_repeat' => 1,
            'repeat_client_key' => $order->client_key,
        ])->assertRedirect(route('orders.index'));

        $this->assertTrue($order->fresh()->is_repeat);
    }

    public function test_the_existing_client_picker_searches_client_names_directly(): void
    {
        $profile = Profile::factory()->create();
        $other = Order::factory()->create(['name' => 'Widget LLC']);

        Order::factory()->create(['freelance_profile_id' => $profile->id, 'name' => 'Acme Corp']);

        // No profile required — the picker searches the orders table by name.
        $this->actingAs($this->salesMember)
            ->getJson(route('orders.search-clients', ['q' => 'Acme']))
            ->assertOk()
            ->assertJsonPath('clients.0.key', 'acme corp')
            ->assertJsonPath('clients.0.name', 'Acme Corp')
            ->assertJsonPath('clients.0.profile_id', $profile->id);

        // A picked client carries its profile so the form can fill it in.
        $this->actingAs($this->salesMember)
            ->getJson(route('orders.search-clients', ['q' => 'Acme', 'exclude' => $other->id]))
            ->assertOk()
            ->assertJsonPath('clients.0.name', 'Acme Corp');

        // No match, no clients.
        $this->actingAs($this->salesMember)
            ->getJson(route('orders.search-clients', ['q' => 'Bolt']))
            ->assertOk()
            ->assertJsonPath('clients', []);
    }

    public function test_sales_person_edits_deal_facts_but_never_delivery_fields(): void
    {
        $team = Team::factory()->create();
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
            'status' => 'in-progress',
            'progress' => 40,
        ]);

        $this->actingAs($this->salesMember)->patch(route('orders.update', $order), [
            'name' => 'Renamed Client',
            'value' => 2000,
            'notes' => 'Budget increased.',
            'status' => 'delivered',
            'progress' => 100,
            'team_id' => $team->id,
        ])->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertSame('Renamed Client', $order->name);
        $this->assertSame(2000.0, (float) $order->value);
        $this->assertSame('Budget increased.', $order->notes);
        $this->assertSame('in-progress', $order->status);
        $this->assertSame(40, $order->progress);
        $this->assertNull($order->team_id);
    }

    public static function salesOrderVisibilityProvider(): array
    {
        return [
            'sales member' => ['salesMember'],
            'sales leader' => ['salesLeader'],
        ];
    }

    #[DataProvider('salesOrderVisibilityProvider')]
    public function test_a_sales_role_sees_all_orders_with_theirs_highlighted(string $actor): void
    {
        $actor = $this->$actor;
        $mine = Order::factory()->create(['sales_user_id' => $actor->id, 'created_by' => $actor->id]);
        $theirs = Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id]);
        $otherOrder = Order::factory()->create(['sales_user_id' => null, 'created_by' => $this->operationManager->id]);

        $data = $this->actingAs($actor)->getJson(route('orders.data'))->assertOk()->json('data');

        // Sales roles can now see all orders like admin/OM/PM
        $visibleIds = array_column($data, 'id');
        $this->assertContains($mine->id, $visibleIds);
        $this->assertContains($theirs->id, $visibleIds);
        $this->assertContains($otherOrder->id, $visibleIds);

        // Their own order is flagged and sorted ahead of everyone else's.
        $byId = collect($data)->keyBy('id');
        $this->assertTrue($byId[$mine->id]['is_my_sale']);
        $this->assertFalse($byId[$theirs->id]['is_my_sale']);
        $this->assertSame($mine->id, $data[0]['id']);

        // They can view every order
        $this->actingAs($actor)->get(route('orders.show', $theirs))->assertOk();
        $this->actingAs($actor)->get(route('orders.show', $mine))->assertOk();
        $this->actingAs($actor)->get(route('orders.show', $otherOrder))->assertOk();
    }

    public function test_a_sales_leader_who_also_holds_team_leader_still_sees_every_order(): void
    {
        // A person can pick up both roles in practice — their sales-side
        // team_id points at an auto-created sales team, not an engineering
        // delivery team, so the team-leader scoping must not apply to them.
        Role::create(['name' => 'team-leader']);
        $this->salesLeader->assignRole('team-leader');
        $this->salesLeader->update(['team_id' => Team::factory()->create()->id]);

        $orderOnAnotherTeam = Order::factory()->create([
            'sales_user_id' => $this->otherSalesMember->id,
            'created_by' => $this->otherSalesMember->id,
            'team_id' => Team::factory()->create()->id,
        ]);

        $data = $this->actingAs($this->salesLeader->fresh())
            ->getJson(route('orders.data'))
            ->assertOk()
            ->json('data');

        $this->assertContains($orderOnAnotherTeam->id, array_column($data, 'id'));
    }

    public function test_sales_can_change_operational_fields_inline_but_never_assignment(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
            'status' => 'in-progress',
        ]);
        $worker = User::factory()->create();

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.quick-update', $order), ['status' => 'delivered'])
            ->assertOk();

        $this->assertSame('delivered', $order->fresh()->status);

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.assign-users', $order), ['users' => [$worker->id]])
            ->assertForbidden();
    }

    public function test_sales_cannot_bulk_action_or_delete_orders(): void
    {
        $order = Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id]);

        $this->actingAs($this->salesMember)
            ->postJson(route('orders.bulk-action'), ['ids' => [$order->id], 'action' => 'mark-completed'])
            ->assertForbidden();

        $this->actingAs($this->salesMember)
            ->deleteJson(route('orders.destroy', $order))
            ->assertForbidden();

        $this->assertSame('in-progress', $order->fresh()->status);
    }

    public function test_the_sales_order_form_hides_delivery_fields(): void
    {
        $response = $this->actingAs($this->salesMember)->get(route('orders.create'))->assertOk();

        $response->assertSee('Client Name', false)
            ->assertSee('Order Amount', false)
            ->assertSee('Initial Client Documents', false);

        $this->assertStringNotContainsString('name="status"', $response->getContent());
        $this->assertStringNotContainsString('name="team_id"', $response->getContent());
        $this->assertStringNotContainsString('name="progress"', $response->getContent());
        $this->assertStringNotContainsString('name="end_date"', $response->getContent());
        $this->assertStringNotContainsString('Assign Users', $response->getContent());
    }

    public function test_operations_never_sees_the_sales_owner_on_an_order(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
        ]);

        $this->actingAs($this->operationManager)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertDontSee('Opened by')
            ->assertDontSee('Sales credit')
            ->assertDontSee('Sales Member');

        $this->actingAs($this->salesMember)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Opened by')
            ->assertSee('Sales credit');
    }

    public function test_sales_people_land_on_the_sales_dashboard(): void
    {
        $this->actingAs($this->salesMember)->get(route('dashboard'))->assertRedirect(route('sales.dashboard'));
        $this->actingAs($this->salesMember)->get(route('orders.dashboard'))->assertRedirect(route('sales.dashboard'));
        $this->actingAs($this->operationManager)->get(route('sales.dashboard'))->assertForbidden();
    }

    public function test_a_sale_is_credited_wholly_to_whoever_opened_it(): void
    {
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Solo Deal',
            'value' => 1200,
        ]);

        $order = Order::first();

        $this->assertCount(1, $order->salesShares);
        $this->assertSame($this->salesMember->id, $order->salesShares->first()->user_id);
        // Sales credit is based on net revenue after default 20% platform fee: 1200 * 0.80 = 960
        $this->assertSame(960.0, (float) $order->salesShares->first()->amount);
        $this->assertSame(0.0, $order->unattributedAmount());
    }

    public function test_an_order_amount_can_be_split_across_several_sales_people(): void
    {
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Shared Deal',
            'value' => 5000,
            'sales_shares' => [
                // Split the net revenue: 5000 * 0.80 = 4000, split as 2400 + 1600
                ['user_id' => $this->salesMember->id, 'amount' => 2400],
                ['user_id' => $this->salesLeader->id, 'amount' => 1600],
            ],
        ])->assertRedirect(route('orders.index'));

        $order = Order::first();

        $this->assertSame(2400.0, $order->salesShareFor($this->salesMember->id));
        $this->assertSame(1600.0, $order->salesShareFor($this->salesLeader->id));
        $this->assertSame(0.0, $order->unattributedAmount());
    }

    public function test_the_split_cannot_exceed_the_order_amount(): void
    {
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Too Generous',
            'value' => 1000,
            'sales_shares' => [
                ['user_id' => $this->salesMember->id, 'amount' => 800],
                ['user_id' => $this->salesLeader->id, 'amount' => 500],
            ],
        ])->assertSessionHasErrors('sales_shares');

        $this->assertSame(0, Order::count());
    }

    public function test_a_shared_order_counts_for_each_person_at_their_own_share(): void
    {
        $this->actingAs($this->salesMember)->post(route('orders.store'), [
            'name' => 'Split Deal',
            'value' => 5000,
            'sales_shares' => [
                // Split based on net revenue: 5000 * 0.80 = 4000, split as 1200 + 2800
                ['user_id' => $this->salesMember->id, 'amount' => 1200],
                ['user_id' => $this->salesLeader->id, 'amount' => 2800],
            ],
        ]);

        // The member's own figure is their slice, not the whole order.
        $this->actingAs($this->salesMember)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$1,200.00')
            ->assertSee('$5,000.00');   // the team still sold the full amount

        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('$2,800.00');
    }

    public function test_sales_users_can_see_all_orders_regardless_of_shares(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->otherSalesMember->id,
            'created_by' => $this->otherSalesMember->id,
            'value' => 2000,
        ]);

        // Sales users can see all orders regardless of shares
        $this->actingAs($this->salesMember)->get(route('orders.show', $order))->assertOk();

        // Shares are still relevant for revenue sharing, but not for visibility
        $order->salesShares()->create(['user_id' => $this->salesMember->id, 'amount' => 500]);

        $this->actingAs($this->salesMember)->get(route('orders.show', $order))->assertOk();
    }

    public function test_changing_the_amount_carries_a_single_person_credit_with_it(): void
    {
        $order = Order::factory()->create([
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
            'value' => 1000,
        ]);

        $this->assertSame(1000.0, $order->fresh()->salesShareFor($this->salesMember->id));

        $order->update(['value' => 2500]);

        $this->assertSame(2500.0, $order->fresh()->salesShareFor($this->salesMember->id));
    }

    public function test_a_member_sees_their_achievement_against_the_month_target(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->startOfMonth(), 5000);
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 2000]);

        $this->actingAs($this->salesMember)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Your target')
            ->assertSee('40%')
            ->assertSee('$2,000.00 of $5,000.00 minimum', false)
            ->assertSee('$3,000.00 to go', false);
    }

    public function test_a_leader_sees_each_member_against_their_target(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->startOfMonth(), 4000);
        SalesTarget::setFor($this->salesLeader->id, now()->startOfMonth(), 6000);

        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 4000]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 3000]);

        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Team target')
            ->assertSee('100% of $4,000.00', false)   // the member met theirs
            ->assertSee('50% of $6,000.00', false)    // the leader is halfway
            ->assertSee('$3,000.00 to go', false);
    }

    public function test_a_target_carries_forward_until_a_newer_one_is_set(): void
    {
        SalesTarget::setFor($this->salesMember->id, now()->subMonths(3)->startOfMonth(), 1000);
        SalesTarget::setFor($this->salesMember->id, now()->subMonth()->startOfMonth(), 2500);

        $this->assertSame(1000.0, SalesTarget::amountFor($this->salesMember->id, now()->subMonths(2)));
        $this->assertSame(2500.0, SalesTarget::amountFor($this->salesMember->id, now()));
        $this->assertNull(SalesTarget::amountFor($this->salesMember->id, now()->subMonths(6)));
    }

    public function test_an_admin_sets_a_members_target_from_the_user_form(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->patchJson(route('users.update', $this->salesMember), [
            'name' => $this->salesMember->name,
            'email' => $this->salesMember->email,
            'sales_target' => 7500,
            'sales_target_month' => now()->format('Y-m'),
        ])->assertOk();

        $this->assertSame(7500.0, $this->salesMember->fresh()->salesTargetFor());
        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $this->salesMember->id,
            'amount' => 7500,
            'set_by' => $admin->id,
        ]);
    }

    public function test_an_admin_can_put_a_sales_member_under_a_leader(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->patchJson(route('users.update', $this->otherSalesMember), [
            'name' => $this->otherSalesMember->name,
            'email' => $this->otherSalesMember->email,
            'sales_leader_id' => $this->salesLeader->id,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame($this->salesLeader->id, $this->otherSalesMember->fresh()->sales_leader_id);
        $this->assertEqualsCanonicalizing(
            [$this->salesLeader->id, $this->salesMember->id, $this->otherSalesMember->id],
            $this->salesLeader->fresh()->salesVisibleUserIds()
        );
    }

    public function test_selecting_a_sales_leader_for_a_member_auto_assigns_a_shared_team(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->patchJson(route('users.update', $this->otherSalesMember), [
            'name' => $this->otherSalesMember->name,
            'email' => $this->otherSalesMember->email,
            'sales_leader_id' => $this->salesLeader->id,
        ])->assertOk();

        $leader = $this->salesLeader->fresh();
        $member = $this->otherSalesMember->fresh();

        $this->assertNotNull($leader->team_id);
        $this->assertSame($leader->team_id, $member->team_id);
        $this->assertSame($leader->id, Team::find($leader->team_id)->team_leader_id);
    }

    public function test_a_new_sales_leader_gets_their_own_team(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->postJson(route('users.store'), [
            'name' => 'Fresh Leader',
            'email' => 'fresh.leader@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'track' => 'sales',
            'role' => 'sales-leader',
        ])->assertOk();

        $newLeader = User::where('email', 'fresh.leader@example.com')->firstOrFail();

        $this->assertNotNull($newLeader->team_id);
        $this->assertSame($newLeader->id, Team::find($newLeader->team_id)->team_leader_id);
    }

    public function test_the_sync_command_backfills_teams_for_sales_users_created_before_the_fix(): void
    {
        // setUp() creates salesLeader/salesMember straight through the
        // factory, the way old seed data or a pre-fix signup would — no
        // team_id, exactly the state that left members with no visible team.
        $this->assertNull($this->salesLeader->team_id);
        $this->assertNull($this->salesMember->team_id);

        $this->artisan('sales:sync-teams')->assertSuccessful();

        $leader = $this->salesLeader->fresh();
        $member = $this->salesMember->fresh();

        $this->assertNotNull($leader->team_id);
        $this->assertSame($leader->team_id, $member->team_id);

        // A sales member with no leader assigned has nothing to sync to.
        $this->assertNull($this->otherSalesMember->fresh()->team_id);
    }

    public function test_sales_dashboard_totals_the_sales_amount_for_the_group(): void
    {
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 2500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 9999]);

        // The leader leads with the group total and sees who sold what.
        // ("Other Seller" now legitimately appears too, in the company-wide
        // running-orders widget — see test_sales_dashboard_shows_every_running_order_not_just_the_group.)
        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Your team sold in')
            ->assertSee('$4,000.00')
            ->assertSee('Sales Member');
    }

    public function test_sales_dashboard_shows_every_running_order_not_just_the_group(): void
    {
        $mine = Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'name' => 'Leader Deal']);
        $outsider = Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'name' => 'Outsider Deal']);
        $finished = Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'name' => 'Finished Deal', 'status' => 'completed']);

        $response = $this->actingAs($this->salesLeader)->get(route('sales.dashboard'))->assertOk();

        // Company-wide running orders — not scoped to the leader's own group.
        $response->assertSee('Leader Deal')->assertSee('Outsider Deal');
        // But an order that's no longer running doesn't clutter the widget.
        $response->assertDontSee('Finished Deal');

        $html = $response->getContent();
        $this->assertTrue(strpos($html, 'Leader Deal') < strpos($html, 'Outsider Deal'), 'The leader\'s own order should be pinned above others.');
    }

    public function test_a_member_sees_their_own_number_and_the_team_total(): void
    {
        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->salesLeader->id, 'created_by' => $this->salesLeader->id, 'value' => 2500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 9999]);

        $this->actingAs($this->salesMember)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('You sold in')
            ->assertSee('$1,500.00')      // their own
            ->assertSee('Your team sold')
            ->assertSee('$4,000.00')      // the group they belong to
            ->assertSee('38%')            // their share of it
            ->assertDontSee('Other Seller');
    }

    public function test_an_admin_sees_every_sales_person(): void
    {
        $admin = User::factory()->create(['name' => 'The Admin']);
        $admin->assignRole('admin');

        Order::factory()->create(['sales_user_id' => $this->salesMember->id, 'created_by' => $this->salesMember->id, 'value' => 1500]);
        Order::factory()->create(['sales_user_id' => $this->otherSalesMember->id, 'created_by' => $this->otherSalesMember->id, 'value' => 500]);

        $this->actingAs($admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('All sales')
            ->assertSee('$2,000.00')
            ->assertSee('Sales Member')
            ->assertSee('Other Seller');
    }

    public function test_an_admin_sees_running_orders_on_the_sales_dashboard_even_with_no_sales_credit_yet(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // An order created directly by operations, never touched by the
        // sales-credit flow — no OrderSalesShare row exists for it at all.
        $order = Order::factory()->create([
            'name' => 'Ops-Only Order',
            'sales_user_id' => null,
            'status' => 'in-progress',
        ]);

        $this->actingAs($admin)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Ops-Only Order');

        $this->assertSame(0, OrderSalesShare::count());
    }

    public function test_sales_dashboard_splits_this_months_sales_into_fresh_and_repeat(): void
    {
        $profile = Profile::factory()->create();

        // The salesperson flagged the Acme order as repeat when entering it;
        // the member's order is from a brand new client, so it stays fresh.
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'sales_user_id' => $this->salesLeader->id,
            'created_by' => $this->salesLeader->id,
            'name' => 'Acme Corp',
            'value' => 1500,
            'is_repeat' => true,
        ]);
        Order::factory()->create([
            'freelance_profile_id' => $profile->id,
            'sales_user_id' => $this->salesMember->id,
            'created_by' => $this->salesMember->id,
            'name' => 'Brand New',
            'value' => 500,
        ]);

        // The leader's group (leader + member) sold $2,000: $1,500 repeat
        // and $500 fresh, and the two sides add up to the headline number.
        $this->actingAs($this->salesLeader)
            ->get(route('sales.dashboard'))
            ->assertOk()
            ->assertSee('Fresh vs. Repeat')
            ->assertSee('$2,000.00')   // group sold
            ->assertSee('$1,500.00')   // repeat
            ->assertSee('$500.00');    // fresh
    }
}
