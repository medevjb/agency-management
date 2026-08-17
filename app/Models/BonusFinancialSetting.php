<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusFinancialSetting extends Model
{
    protected $fillable = ['bonus_percentage', 'usd_to_bdt_rate', 'effective_from', 'effective_to', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['bonus_percentage' => 'decimal:6', 'usd_to_bdt_rate' => 'decimal:4', 'effective_from' => 'date', 'effective_to' => 'date', 'is_active' => 'boolean'];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function applicableOn(Carbon $date): ?self
    {
        return static::query()->where('is_active', true)->whereDate('effective_from', '<=', $date->toDateString())
            ->where(fn ($query) => $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date->toDateString()))
            ->orderByDesc('effective_from')->first();
    }
}
