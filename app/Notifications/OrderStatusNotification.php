<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order, public string $oldStatus, public string $newStatus) {}

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
        return (new MailMessage)
            ->subject('Order Status Updated: '.$this->order->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('An order status has been updated.')
            ->line('**Order Name:** '.$this->order->name)
            ->line('**Order ID:** '.$this->order->order_id)
            ->line('**Previous Status:** '.$this->oldStatus)
            ->line('**New Status:** '.$this->newStatus)
            ->lineWhen($this->order->value, '**Value:** $'.number_format($this->order->value, 2))
            ->action('View Order', route('orders.show', $this->order))
            ->line('Please review the updated status.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order status updated',
            'message' => $this->order->name.': '.$this->oldStatus.' → '.$this->newStatus,
            'icon' => 'fa-shopping-cart',
            'color' => 'ember',
            'url' => route('orders.show', $this->order),
        ];
    }
}
