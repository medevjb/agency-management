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

    public function test_order_assignment_notification_renders(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'name' => 'Test Order',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'high',
            'created_by' => $user->id,
        ]);

        $mail = new OrderAssignmentNotification($order, $user, false);
        $rendered = $mail->render();

        $this->assertStringContainsString('Test Order', $rendered);
        $this->assertStringContainsString('Test Description', $rendered);
        $this->assertStringContainsString('Pending', $rendered);
        $this->assertStringContainsString('High', $rendered);
    }

    public function test_task_assignment_notification_renders(): void
    {
        $user = User::factory()->create();
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task Description',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
        ]);

        $mail = new TaskAssignmentNotification($task, $user);
        $rendered = $mail->render();

        $this->assertStringContainsString('Test Task', $rendered);
        $this->assertStringContainsString('Task Description', $rendered);
        $this->assertStringContainsString('Pending', $rendered);
        $this->assertStringContainsString('Medium', $rendered);
    }

    public function test_email_service_checks_queue_enabled(): void
    {
        // Test that the method returns boolean
        $result = EmailService::isQueueEnabled();
        $this->assertIsBool($result);
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

        $mail = new OrderAssignmentNotification($order, $user, false);
        $envelope = $mail->envelope();

        $this->assertEquals('New Order Assignment - My Test Order', $envelope->subject);
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

        $mail = new TaskAssignmentNotification($task, $user);
        $envelope = $mail->envelope();

        $this->assertEquals('New Task Assignment - My Test Task', $envelope->subject);
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

        $mail = new OrderAssignmentNotification($order, $user, true);
        $rendered = $mail->render();

        $this->assertStringContainsString('Your team has been assigned', $rendered);
    }

    public function test_order_notification_for_individual_user(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'name' => 'User Order',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
        ]);

        $mail = new OrderAssignmentNotification($order, $user, false);
        $rendered = $mail->render();

        $this->assertStringContainsString('You have been assigned', $rendered);
    }

    public function test_task_notification_contains_task_details(): void
    {
        $user = User::factory()->create();
        $task = Task::create([
            'title' => 'Important Task',
            'due_date' => now()->addDays(7),
            'estimated_hours' => 8,
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
        ]);

        $mail = new TaskAssignmentNotification($task, $user);
        $rendered = $mail->render();

        $this->assertStringContainsString('Important Task', $rendered);
        $this->assertStringContainsString('8h', $rendered);
    }
}
