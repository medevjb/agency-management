<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Task $task,
        public User $recipient
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Task Assignment - '.$this->task->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assignment',
            with: [
                'recipientName' => $this->recipient->name,
                'taskTitle' => $this->task->title,
                'taskDescription' => $this->task->description,
                'taskStatus' => $this->task->status,
                'taskPriority' => $this->task->priority,
                'dueDate' => $this->task->due_date?->format('M j, Y'),
                'estimatedHours' => $this->task->estimated_hours,
                'orderName' => $this->task->order?->name,
                'orderId' => $this->task->order?->id,
                'teamName' => $this->task->team?->name,
                'creatorName' => $this->task->createdBy?->name,
                'taskUrl' => route('tasks.show', $this->task),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
