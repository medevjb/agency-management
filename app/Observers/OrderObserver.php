<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderAudit;

class OrderObserver
{
    public function created(Order $order): void
    {
        $values = array_filter(
            $this->presentable($order->getAttributes()),
            fn ($value) => $value !== null && $value !== '' && $value !== []
        );

        OrderAudit::record($order, 'created', [], $values);
    }

    public function updated(Order $order): void
    {
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
