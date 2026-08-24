<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task, public string $changeType) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->changeType) {
            'status' => 'Task Status Updated',
            'priority' => 'Task Priority Changed',
            'due_date' => 'Task Due Date Changed',
            default => 'Task Updated'
        };

        return (new MailMessage)
            ->subject($subject.': '.$this->task->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('The following task has been updated:')
            ->line('**Task Title:** '.$this->task->title)
            ->line('**Status:** '.$this->task->status)
            ->line('**Priority:** '.$this->task->priority)
            ->line('**Due Date:** '.$this->task->due_date?->format('F d, Y'))
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Please review the changes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $label = match ($this->changeType) {
            'status' => 'Task status updated',
            'priority' => 'Task priority changed',
            'due_date' => 'Task due date changed',
            default => 'Task updated',
        };

        return [
            'title' => $label,
            'message' => $this->task->title,
            'icon' => 'fa-tasks',
            'color' => 'gold',
            'url' => route('tasks.show', $this->task),
        ];
    }
}
