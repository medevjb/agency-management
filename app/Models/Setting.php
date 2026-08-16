<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Scope to get settings by group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope to get public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        if (! static::tableExists()) {
            return $default;
        }

        $setting = static::where('key', $key)->first();

        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Get many setting values in a single query, keyed by setting key.
     *
     * @param  array<string, mixed>  $defaults  key → fallback value
     * @return array<string, mixed>
     */
    public static function getMany(array $keys, array $defaults = []): array
    {
        if (! static::tableExists() || $keys === []) {
            return array_merge(array_fill_keys($keys, null), $defaults);
        }

        $rows = static::whereIn('key', $keys)->get();

        $values = [];
        foreach ($keys as $key) {
            $setting = $rows->firstWhere('key', $key);
            $values[$key] = $setting ? $setting->getValue() : ($defaults[$key] ?? null);
        }

        return $values;
    }

    /**
     * Whether the settings table exists yet (false on a fresh/unmigrated database).
     */
    protected static function tableExists(): bool
    {
        static $exists = null;

        if ($exists === null) {
            try {
                $exists = Schema::hasTable('settings');
            } catch (\Throwable $e) {
                $exists = false;
            }
        }

        return $exists;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
            ]
        );
    }

    /**
     * Get the typed value.
     */
    public function getValue()
    {
        return match ($this->type) {
            'boolean', 'checkbox' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number', 'integer' => (int) $this->value,
            'float', 'decimal' => (float) $this->value,
            'json', 'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get project statuses from settings.
     */
    public static function getProjectStatuses(): array
    {
        $statuses = static::tableExists()
            ? static::where('key', 'project_statuses')->first()
            : null;

        if (! $statuses) {
            // Return default statuses if none exist in database
            return [
                'nra' => ['key' => 'nra', 'label' => 'NRA', 'icon' => '🤷‍♀️', 'color' => 'bg-gray-100 text-gray-800'],
                'pending' => ['key' => 'pending', 'label' => 'Pending', 'icon' => '⏳', 'color' => 'bg-yellow-100 text-yellow-800'],
                'in-progress' => ['key' => 'in-progress', 'label' => 'In Progress', 'icon' => '🔄', 'color' => 'bg-blue-100 text-blue-800'],
                'completed' => ['key' => 'completed', 'label' => 'Completed', 'icon' => '✅', 'color' => 'bg-green-100 text-green-800'],
                'on-hold' => ['key' => 'on-hold', 'label' => 'On Hold', 'icon' => '⏸️', 'color' => 'bg-orange-100 text-orange-800'],
                'cancelled' => ['key' => 'cancelled', 'label' => 'Cancelled', 'icon' => '❌', 'color' => 'bg-red-100 text-red-800'],
            ];
        }

        $data = $statuses->getValue();

        // Convert array to key-value pairs for easier access
        $result = [];
        foreach ($data as $status) {
            $result[$status['key']] = $status;
        }

        return $result;
    }

    /**
     * Get task statuses from project statuses (reuse same statuses).
     */
    public static function getTaskStatuses(): array
    {
        return static::getProjectStatuses();
    }
}
