<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task) {}

    /**
     * Get the notification's delivery channels.
     *
     * Mail for a new assignment is already sent separately via
     * App\Mail\TaskAssignmentNotification — this class only records the
     * in-app notification, so we don't send a duplicate email.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New task assigned',
            'message' => $this->task->title.' — '.ucfirst($this->task->priority).' priority',
            'icon' => 'fa-list-check',
            'color' => 'aqua',
            'url' => route('tasks.show', $this->task),
        ];
    }
}
