<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class SalesTarget extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'amount',
        'set_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Always stored as a bare `Y-m-d` first-of-month, so string comparisons
     * behave the same on MySQL and SQLite. The default date cast would write
     * a time component on SQLite and break `month <= '2026-08-01'`.
     */
    protected function month(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->startOfMonth(),
            set: fn (Carbon|string $value) => Carbon::parse($value)->startOfMonth()->toDateString(),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Record the target that applies from the given month onwards.
     */
    public static function setFor(int $userId, Carbon $month, float $amount): self
    {
        return self::updateOrCreate(
            ['user_id' => $userId, 'month' => $month->copy()->startOfMonth()->toDateString()],
            ['amount' => $amount, 'set_by' => auth()->id()],
        );
    }

    /**
     * The target in force for a month: the most recent one set on or before it,
     * so a target carries forward until it is changed.
     */
    public static function amountFor(int $userId, Carbon $month): ?float
    {
        $amount = self::query()
            ->where('user_id', $userId)
            ->where('month', '<=', $month->copy()->startOfMonth()->toDateString())
            ->orderByDesc('month')
            ->value('amount');

        return $amount === null ? null : (float) $amount;
    }

    /**
     * The same lookup for several people at once, keyed by user id.
     *
     * @param  array<int, int>  $userIds
     * @return Collection<int, float>
     */
    public static function amountsFor(array $userIds, Carbon $month): Collection
    {
        if ($userIds === []) {
            return collect();
        }

        return self::query()
            ->whereIn('user_id', $userIds)
            ->where('month', '<=', $month->copy()->startOfMonth()->toDateString())
            ->orderBy('month')
            ->get()
            ->groupBy('user_id')
            // Ordered ascending, so the last row is the newest one still in force.
            ->map(fn (Collection $rows) => (float) $rows->last()->amount);
    }
}
