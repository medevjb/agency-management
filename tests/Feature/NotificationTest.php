<?php

namespace Tests\Feature;

use App\Mail\OrderAssignmentNotification;
use App\Mail\TaskAssignmentNotification;
use App\Models\Order;
use App\Models\Task;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_assignment_notification_renders_and_addresses_the_recipient(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'name' => 'Test Order',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'high',
            'created_by' => $user->id,
        ]);

        $rendered = (new OrderAssignmentNotification($order, $user, false))->render();

        $this->assertStringContainsString('Test Order', $rendered);
        $this->assertStringContainsString('Test Description', $rendered);
        $this->assertStringContainsString('Pending', $rendered);
        $this->assertStringContainsString('High', $rendered);
        $this->assertStringContainsString('You have been assigned', $rendered);
    }

    public function test_order_notification_for_team_leader(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'name' => 'Team Order',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
        ]);

        $rendered = (new OrderAssignmentNotification($order, $user, true))->render();

        $this->assertStringContainsString('Your team has been assigned', $rendered);
    }

    public function test_order_notification_subject(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'name' => 'My Test Order',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
        ]);

        $envelope = (new OrderAssignmentNotification($order, $user, false))->envelope();

        $this->assertEquals('New Order Assignment - My Test Order', $envelope->subject);
    }

    public function test_task_assignment_notification_renders_and_includes_details(): void
    {
        $user = User::factory()->create();
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task Description',
            'due_date' => now()->addDays(7),
            'estimated_hours' => 8,
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
        ]);

        $rendered = (new TaskAssignmentNotification($task, $user))->render();

        $this->assertStringContainsString('Test Task', $rendered);
        $this->assertStringContainsString('Task Description', $rendered);
        $this->assertStringContainsString('Pending', $rendered);
        $this->assertStringContainsString('Medium', $rendered);
        $this->assertStringContainsString('8h', $rendered);
    }

    public function test_task_notification_subject(): void
    {
        $user = User::factory()->create();
        $task = Task::create([
            'title' => 'My Test Task',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
        ]);

        $envelope = (new TaskAssignmentNotification($task, $user))->envelope();

        $this->assertEquals('New Task Assignment - My Test Task', $envelope->subject);
    }

    public function test_email_service_queue_flag_follows_config(): void
    {
        config(['mail.queue_enabled' => true]);
        $this->assertTrue(EmailService::isQueueEnabled());

        config(['mail.queue_enabled' => false]);
        $this->assertFalse(EmailService::isQueueEnabled());
    }
}
