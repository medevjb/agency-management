<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderAudit extends Model
{
    /**
     * Fields that must never be written to the audit trail in clear text.
     */
    public const REDACTED_FIELDS = ['credentials'];

    /**
     * Fields that carry no information for a human reading the trail.
     * Documents are audited separately with their own events.
     */
    public const IGNORED_FIELDS = ['created_at', 'updated_at', 'deleted_at', 'client_documents'];

    protected $fillable = [
        'order_id',
        'user_id',
        'event',
        'old_values',
        'new_values',
        'ip_address',
    ];

    /**
     * Cache of related record names keyed by "model:id" so rendering a long
     * trail does not fire one query per audit row.
     *
     * @var array<string, string>
     */
    protected static array $labelCache = [];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Write an audit entry for an order.
     *
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public static function record(Order $order, string $event, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'event' => $event,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            // CLI / queue contexts have no request to read the IP from.
            'ip_address' => optional(request())->ip(),
        ]);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventLabel(): string
    {
        return match ($this->event) {
            'created' => 'Order created',
            'updated' => 'Order updated',
            'deleted' => 'Order deleted',
            'restored' => 'Order restored',
            'members_updated' => 'Team members updated',
            'document_uploaded' => 'Documents uploaded',
            'document_deleted' => 'Document removed',
            'sales_credit_updated' => 'Sales credit updated',
            'value_distributed' => 'Value distribution updated',
            default => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    public function eventIcon(): string
    {
        return match ($this->event) {
            'created' => 'fa-plus',
            'deleted' => 'fa-trash',
            'restored' => 'fa-rotate-left',
            'members_updated' => 'fa-users',
            'sales_credit_updated' => 'fa-handshake',
            'value_distributed' => 'fa-sitemap',
            'document_uploaded' => 'fa-file-arrow-up',
            'document_deleted' => 'fa-file-circle-xmark',
            default => 'fa-pen',
        };
    }

    /**
     * Colour tone for the event marker. The view maps this to its own CSS.
     */
    public function eventTone(): string
    {
        return match ($this->event) {
            'created' => 'create',
            'deleted' => 'delete',
            'restored' => 'restore',
            'members_updated' => 'people',
            'sales_credit_updated' => 'create',
            'value_distributed' => 'people',
            'document_uploaded', 'document_deleted' => 'files',
            default => 'edit',
        };
    }

    /**
     * What the person did, as the back half of a sentence that starts with
     * their name: "Jobaed changed Status and Deadline".
     */
    public function summary(): string
    {
        $count = count($this->new_values ?? $this->old_values ?? []);

        return match ($this->event) {
            'created' => 'created this order',
            'deleted' => 'deleted this order',
            'restored' => 'restored this order',
            'members_updated' => 'changed who is assigned',
            'sales_credit_updated' => 'changed the sales credit',
            'value_distributed' => 'updated the value distribution',
            'document_uploaded' => 'uploaded '.$this->documentCount().' '.Str::plural('file', $this->documentCount()),
            'document_deleted' => 'removed '.$this->documentCount().' '.Str::plural('file', $this->documentCount()),
            default => $this->changedFieldsSentence($count),
        };
    }

    /**
     * The event groups the change log can be filtered by.
     *
     * @return array<string, array<int, string>>
     */
    public static function filterGroups(): array
    {
        return [
            'fields' => ['created', 'updated', 'deleted', 'restored'],
            'people' => ['members_updated', 'sales_credit_updated'],
            'files' => ['document_uploaded', 'document_deleted'],
        ];
    }

    public function filterGroup(): string
    {
        foreach (self::filterGroups() as $group => $events) {
            if (in_array($this->event, $events, true)) {
                return $group;
            }
        }

        return 'fields';
    }

    protected function documentCount(): int
    {
        $documents = $this->new_values['documents'] ?? $this->old_values['documents'] ?? [];

        return max(1, count($documents));
    }

    protected function changedFieldsSentence(int $count): string
    {
        if ($count === 0) {
            return 'updated this order';
        }

        $labels = array_map(
            fn (string $field) => self::fieldLabel($field),
            array_keys($this->new_values ?? $this->old_values ?? [])
        );

        if ($count <= 2) {
            return 'changed '.implode(' and ', $labels);
        }

        return 'changed '.$count.' fields';
    }

    /**
     * The recorded change set, formatted for display.
     *
     * @return array<int, array{field: string, label: string, old: string, new: string}>
     */
    public function formattedChanges(): array
    {
        $fields = array_keys(($this->new_values ?? []) + ($this->old_values ?? []));

        $changes = [];
        foreach ($fields as $field) {
            $changes[] = [
                'field' => $field,
                'label' => self::fieldLabel($field),
                'old' => self::formatValue($field, $this->old_values[$field] ?? null),
                'new' => self::formatValue($field, $this->new_values[$field] ?? null),
            ];
        }

        return $changes;
    }

    public static function fieldLabel(string $field): string
    {
        return match ($field) {
            'order_id' => 'Custom Order ID',
            'name' => 'Client Name',
            'value' => 'Order Amount',
            'actual_revenue' => 'Net Value',
            'start_date' => 'Start Date',
            'end_date' => 'Deadline',
            'delivered_at' => 'Delivered At',
            'team_id' => 'Team',
            'tech_stack_id' => 'Tech Stack',
            'freelance_profile_id' => 'Freelance Profile',
            'special_status' => 'Special Status',
            'last_update_date' => 'Last Update Date',
            'created_by' => 'Created By',
            'sales_user_id' => 'Sold By',
            'credentials' => 'Credentials',
            'client_documents' => 'Client Documents',
            'progress' => 'Progress',
            'notes' => 'Notes',
            'members' => 'Assigned Members',
            'sales_credit' => 'Sales Credit',
            'value_distribution' => 'Value Distribution',
            'documents' => 'Documents',
            default => ucwords(str_replace('_', ' ', $field)),
        };
    }

    protected static function formatValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '' || $value === []) {
            return '—';
        }

        if (in_array($field, self::REDACTED_FIELDS, true)) {
            return '••••••••';
        }

        if (is_array($value)) {
            return implode(', ', array_map(fn ($item) => is_scalar($item) ? (string) $item : json_encode($item), $value));
        }

        return match ($field) {
            'progress' => $value.'%',
            'value', 'actual_revenue' => '$'.number_format((float) $value, 2),
            'status' => ucfirst(str_replace('-', ' ', (string) $value)),
            'priority' => ucfirst((string) $value),
            'special_status' => ucfirst(str_replace('_', ' ', (string) $value)),
            'start_date', 'end_date' => Carbon::parse($value)->format('M d, Y'),
            'delivered_at', 'last_update_date' => Carbon::parse($value)->format('M d, Y g:i A'),
            'team_id' => self::relatedLabel(Team::class, $value, 'name'),
            'tech_stack_id' => self::relatedLabel(TechStack::class, $value, 'name'),
            'freelance_profile_id' => self::relatedLabel(Profile::class, $value, 'username'),
            'created_by', 'sales_user_id' => self::relatedLabel(User::class, $value, 'name'),
            'notes', 'description' => Str::limit((string) $value, 160),
            default => (string) $value,
        };
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected static function relatedLabel(string $modelClass, mixed $id, string $column): string
    {
        $key = $modelClass.':'.$id;

        if (! array_key_exists($key, self::$labelCache)) {
            self::$labelCache[$key] = $modelClass::query()->whereKey($id)->value($column) ?? "#{$id}";
        }

        return self::$labelCache[$key];
    }
}
