<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
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
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned: '.$this->task->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('You have been assigned a new task.')
            ->line('**Task Title:** '.$this->task->title)
            ->line('**Priority:** '.$this->task->priority)
            ->line('**Due Date:** '.$this->task->due_date?->format('F d, Y'))
            ->lineWhen($this->task->description, '**Description:** '.$this->task->description)
            ->lineWhen($this->task->order_id, '**Order:** '.$this->task->order->name)
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Please review and start working on this task.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'priority' => $this->task->priority,
            'due_date' => $this->task->due_date,
        ];
    }
}
