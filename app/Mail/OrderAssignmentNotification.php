<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAssignmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order,
        public User $recipient,
        public bool $isTeamLeader = false
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Assignment - '.$this->order->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-assignment',
            with: [
                'recipientName' => $this->recipient->name,
                'orderName' => $this->order->name,
                'orderId' => $this->order->order_id,
                'orderDescription' => $this->order->description,
                'orderStatus' => $this->order->status,
                'orderPriority' => $this->order->priority,
                'orderValue' => $this->order->value,
                'startDate' => $this->order->start_date?->format('M j, Y'),
                'endDate' => $this->order->end_date?->format('M j, Y'),
                'teamName' => $this->order->team?->name,
                'techStackName' => $this->order->techStack?->name,
                'creatorName' => $this->order->creator?->name,
                'isTeamLeader' => $this->isTeamLeader,
                'orderUrl' => route('orders.show', $this->order),
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
