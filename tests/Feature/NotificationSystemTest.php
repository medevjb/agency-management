<?php

namespace Tests\Feature;

use App\Models\BugCategory;
use App\Models\BugPriority;
use App\Models\BugReport;
use App\Models\Order;
use App\Models\User;
use App\Notifications\BugReportStatusNotification;
use App\Notifications\BugReportSubmittedNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusNotification;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $om;

    protected User $pm;

    protected User $salesMember;

    protected User $teamMember;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'operation-manager', 'project-manager', 'sales-member', 'team-member'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->om = User::factory()->create();
        $this->om->assignRole('operation-manager');

        $this->pm = User::factory()->create();
        $this->pm->assignRole('project-manager');

        $this->salesMember = User::factory()->create();
        $this->salesMember->assignRole('sales-member');

        $this->teamMember = User::factory()->create();
        $this->teamMember->assignRole('team-member');
    }

    protected function bugTaxonomy(): array
    {
        return [
            BugCategory::create(['name' => 'Frontend', 'slug' => 'frontend']),
            BugPriority::create(['name' => 'High', 'slug' => 'high', 'color' => '#ff0000', 'level' => 3]),
        ];
    }

    public function test_creating_an_order_notifies_only_management_roles(): void
    {
        Notification::fake();

        $this->actingAs($this->admin)->post(route('orders.store'), [
            'name' => 'Acme Corp',
            'status' => 'in-progress',
            'priority' => 'medium',
        ])->assertRedirect();

        Notification::assertSentTo([$this->admin, $this->om, $this->pm], OrderCreatedNotification::class);
        Notification::assertNotSentTo([$this->salesMember, $this->teamMember], OrderCreatedNotification::class);
    }

    public function test_order_status_change_notifies_assigned_members_and_sales_owner_not_actor(): void
    {
        $order = Order::create([
            'name' => 'Status Order',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $this->admin->id,
            'sales_user_id' => $this->salesMember->id,
        ]);
        $order->users()->attach($this->teamMember->id);

        Notification::fake();

        $this->actingAs($this->om)->patch(route('orders.update', $order), [
            'name' => 'Status Order',
            'status' => 'delivered',
            'priority' => 'medium',
        ])->assertRedirect();

        Notification::assertSentTo([$this->teamMember, $this->salesMember], OrderStatusNotification::class);
        Notification::assertNotSentTo($this->om, OrderStatusNotification::class);
    }

    public function test_assigning_a_task_notifies_the_assignee_not_the_assigner(): void
    {
        Notification::fake();

        $this->actingAs($this->admin)->post(route('tasks.store'), [
            'title' => 'Fix the bug',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => $this->teamMember->id,
        ])->assertRedirect();

        Notification::assertSentTo($this->teamMember, TaskAssignedNotification::class);
        Notification::assertNotSentTo($this->admin, TaskAssignedNotification::class);
    }

    public function test_submitting_a_bug_report_notifies_management_roles_only(): void
    {
        [$category, $priority] = $this->bugTaxonomy();

        Notification::fake();

        $this->actingAs($this->teamMember)->post(route('bug-reports.store'), [
            'title' => 'Broken button',
            'description' => 'The button does nothing.',
            'bug_category_id' => $category->id,
            'bug_priority_id' => $priority->id,
        ])->assertRedirect();

        Notification::assertSentTo([$this->admin, $this->om, $this->pm], BugReportSubmittedNotification::class);
        Notification::assertNotSentTo($this->teamMember, BugReportSubmittedNotification::class);
    }

    public function test_bug_report_status_change_notifies_reporter_not_actor(): void
    {
        [$category, $priority] = $this->bugTaxonomy();

        $bugReport = BugReport::create([
            'user_id' => $this->teamMember->id,
            'bug_category_id' => $category->id,
            'bug_priority_id' => $priority->id,
            'title' => 'Broken button',
            'description' => 'The button does nothing.',
            'status' => 'pending',
        ]);

        Notification::fake();

        $this->actingAs($this->admin)->patch(route('bug-reports.update', $bugReport), [
            'title' => 'Broken button',
            'description' => 'The button does nothing.',
            'bug_category_id' => $category->id,
            'bug_priority_id' => $priority->id,
            'status' => 'resolved',
        ])->assertRedirect();

        Notification::assertSentTo($this->teamMember, BugReportStatusNotification::class);
        Notification::assertNotSentTo($this->admin, BugReportStatusNotification::class);
    }

    public function test_notifications_panel_paginates_and_load_more_returns_the_next_chunk(): void
    {
        for ($i = 0; $i < 20; $i++) {
            Order::create([
                'name' => "Order {$i}",
                'status' => 'pending',
                'priority' => 'medium',
                'created_by' => $this->admin->id,
            ]);
        }

        $this->assertGreaterThanOrEqual(20, $this->admin->notifications()->count());

        $this->actingAs($this->admin)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Load more');

        $loadMore = $this->actingAs($this->admin)->getJson(route('notifications.load-more', ['page' => 2]))
            ->assertOk()
            ->assertJsonStructure(['html', 'has_more', 'next_page']);

        $this->assertNotEmpty($loadMore->json('html'));
    }

    public function test_opening_a_notification_marks_it_read_and_redirects_to_its_target(): void
    {
        $order = Order::create([
            'name' => 'Openable',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $this->admin->id,
        ]);

        $notification = $this->admin->notifications()->firstOrFail();
        $this->assertNull($notification->read_at);

        // The order's route key is encrypted (see EncryptsRouteKeys), so the
        // ciphertext in $notification->data['url'] won't string-match a
        // freshly generated route() call — follow the redirect instead.
        $response = $this->actingAs($this->admin)->get(route('notifications.open', $notification->id));
        $response->assertRedirect();
        $this->actingAs($this->admin)->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Openable');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_cannot_open_another_users_notification(): void
    {
        Order::create([
            'name' => 'Private',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $this->admin->id,
        ]);

        $notification = $this->admin->notifications()->firstOrFail();

        $this->actingAs($this->teamMember)->get(route('notifications.open', $notification->id))
            ->assertNotFound();
    }

    public function test_mark_all_read_clears_every_unread_notification(): void
    {
        Order::create(['name' => 'A', 'status' => 'pending', 'priority' => 'medium', 'created_by' => $this->admin->id]);
        Order::create(['name' => 'B', 'status' => 'pending', 'priority' => 'medium', 'created_by' => $this->admin->id]);

        $this->assertGreaterThan(0, $this->admin->unreadNotifications()->count());

        $this->actingAs($this->admin)->post(route('notifications.read-all'))->assertRedirect();

        $this->assertSame(0, $this->admin->unreadNotifications()->count());
    }
}
