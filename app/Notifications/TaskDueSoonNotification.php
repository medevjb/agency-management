<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task, public int $daysRemaining) {}

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
        $urgency = $this->daysRemaining <= 1 ? 'urgent' : 'upcoming';

        return (new MailMessage)
            ->subject('Reminder: Task Due Soon')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('This is a reminder that you have a task due soon.')
            ->line('**Task Title:** '.$this->task->title)
            ->line('**Priority:** '.$this->task->priority)
            ->line('**Due Date:** '.$this->task->due_date->format('F d, Y'))
            ->line('**Days Remaining:** '.$this->daysRemaining)
            ->lineWhen($this->task->description, '**Description:** '.$this->task->description)
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Please ensure you complete this task on time.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Task due soon',
            'message' => $this->task->title.' — due in '.$this->daysRemaining.' day'.($this->daysRemaining === 1 ? '' : 's'),
            'icon' => 'fa-clock',
            'color' => 'red',
            'url' => route('tasks.show', $this->task),
        ];
    }
}
