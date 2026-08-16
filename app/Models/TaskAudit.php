<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TaskAudit extends Model
{
    /**
     * Fields that must never be written to the audit trail in clear text.
     */
    public const REDACTED_FIELDS = ['credentials'];

    /**
     * Fields that carry no information for a human reading the trail.
     */
    public const IGNORED_FIELDS = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'task_id',
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
     * Write an audit entry for a task.
     *
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public static function record(Task $task, string $event, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'event' => $event,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            // CLI / queue contexts have no request to read the IP from.
            'ip_address' => optional(request())->ip(),
        ]);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventLabel(): string
    {
        return match ($this->event) {
            'created' => 'Task created',
            'updated' => 'Task updated',
            'deleted' => 'Task deleted',
            'restored' => 'Task restored',
            'members_updated' => 'Team members updated',
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
            'created' => 'created this task',
            'deleted' => 'deleted this task',
            'restored' => 'restored this task',
            'members_updated' => 'changed who is assigned',
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
            'people' => ['members_updated'],
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

    protected function changedFieldsSentence(int $count): string
    {
        if ($count === 0) {
            return 'updated this task';
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
            'title' => 'Task Title',
            'description' => 'Description',
            'status' => 'Status',
            'priority' => 'Priority',
            'due_date' => 'Due Date',
            'estimated_hours' => 'Estimated Hours',
            'actual_hours' => 'Actual Hours',
            'order_id' => 'Project/Order',
            'team_id' => 'Team',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'completed_at' => 'Completed At',
            'archived_at' => 'Archived At',
            'members' => 'Assigned Members',
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
            'estimated_hours', 'actual_hours' => (float) $value.' hours',
            'status' => ucfirst(str_replace('-', ' ', (string) $value)),
            'priority' => ucfirst((string) $value),
            'due_date' => Carbon::parse($value)->format('M d, Y'),
            'completed_at', 'archived_at' => Carbon::parse($value)->format('M d, Y g:i A'),
            'order_id' => self::relatedLabel(Order::class, $value, 'name'),
            'team_id' => self::relatedLabel(Team::class, $value, 'name'),
            'assigned_to', 'created_by' => self::relatedLabel(User::class, $value, 'name'),
            'description' => Str::limit((string) $value, 160),
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
