<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order) {}

    /**
     * Get the notification's delivery channels.
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
        // Who created/owns an order is a sales-attribution detail the order
        // page itself deliberately hides from OM/PM (see $showsSalesOwner in
        // orders/show.blade.php) — the notification must not leak it either.
        return [
            'title' => 'New order created',
            'message' => $this->order->name,
            'icon' => 'fa-shopping-cart',
            'color' => 'ember',
            'url' => route('orders.show', $this->order),
        ];
    }
}
