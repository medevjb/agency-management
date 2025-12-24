<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->getValue() : $default;
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
}
