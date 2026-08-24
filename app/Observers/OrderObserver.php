<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Notifications\OrderCreatedNotification;
use App\Support\NotificationRecipients;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Any order with a sales owner carries credit, so the sales figures can
        // never silently miss one. A split replaces this row.
        if ($order->sales_user_id && $order->salesShares()->doesntExist()) {
            $order->salesShares()->create([
                'user_id' => $order->sales_user_id,
                'amount' => $order->value ?? 0,
            ]);
        }

        $values = array_filter(
            $this->presentable($order->getAttributes()),
            fn ($value) => $value !== null && $value !== '' && $value !== []
        );

        OrderAudit::record($order, 'created', [], $values);

        Notification::send(NotificationRecipients::management(), new OrderCreatedNotification($order));
    }

    public function updated(Order $order): void
    {
        $this->followAmountChange($order);

        $changed = $this->presentable($order->getChanges());

        if ($changed === []) {
            return;
        }

        $original = [];
        foreach (array_keys($changed) as $field) {
            $original[$field] = $this->presentableValue($field, $order->getOriginal($field));
        }

        OrderAudit::record($order, 'updated', $original, $changed);
    }

    public function deleted(Order $order): void
    {
        OrderAudit::record($order, 'deleted');
    }

    public function restored(Order $order): void
    {
        OrderAudit::record($order, 'restored');
    }

    /**
     * When an order that is credited to one person changes value, move the
     * credit with it. A split is left alone — that is a deliberate division
     * someone has to re-balance themselves.
     */
    protected function followAmountChange(Order $order): void
    {
        if (! $order->wasChanged('value')) {
            return;
        }

        $shares = $order->salesShares()->get();

        if ($shares->count() !== 1) {
            return;
        }

        $share = $shares->first();
        $previousValue = round((float) $order->getOriginal('value'), 2);

        if (round((float) $share->amount, 2) === $previousValue) {
            $share->update(['amount' => $order->value ?? 0]);
        }
    }

    /**
     * Strip noise from an attribute set and redact anything sensitive.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function presentable(array $attributes): array
    {
        $presentable = [];

        foreach ($attributes as $field => $value) {
            if (in_array($field, OrderAudit::IGNORED_FIELDS, true) || $field === 'id') {
                continue;
            }

            $presentable[$field] = $this->presentableValue($field, $value);
        }

        return $presentable;
    }

    /**
     * Convert a raw attribute value into something safe and readable to store.
     */
    protected function presentableValue(string $field, mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (in_array($field, OrderAudit::REDACTED_FIELDS, true)) {
            return '[redacted]';
        }

        if ($field === 'client_documents') {
            $documents = is_string($value) ? json_decode($value, true) : $value;

            return collect($documents ?: [])->pluck('name')->all();
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }
}
