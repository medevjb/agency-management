<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKeys;
use App\Observers\OrderObserver;
use App\Services\PlatformFeeCalculator;
use App\SpecialStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use EncryptsRouteKeys, HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'name',
        'description',
        'notes',
        'credentials',
        'client_documents',
        'status',
        'is_repeat',
        'repeat_client_key',
        'progress',
        'priority',
        'value',
        'start_date',
        'end_date',
        'actual_revenue',
        'delivered_at',
        'team_id',
        'tech_stack_id',
        'freelance_profile_id',
        'special_status',
        'last_update_date',
        'created_by',
        'sales_user_id',
        'completed_at',
        'cancelled_at',
        'value_distributed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'actual_revenue' => 'decimal:2',
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'value_distributed_at' => 'datetime',
            'special_status' => SpecialStatus::class,
            'last_update_date' => 'datetime',
            'credentials' => 'encrypted',
            'client_documents' => 'array',
            'progress' => 'integer',
            'is_repeat' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($order) {
            if ($order->isDirty('name')) {
                $order->client_key = Str::of($order->name)->trim()->lower()->value();
            }
        });

        static::updating(function ($order) {
            // delivered_at is an informational milestone only — it no longer
            // drives revenue, monthly grouping, or carry-forward. 'delivered'
            // is just another active status, same as 'in-progress'.
            if ($order->isDirty('status') && $order->status === 'delivered' && $order->getOriginal('status') !== 'delivered') {
                $order->delivered_at = now();
            } elseif ($order->isDirty('status') && $order->status !== 'delivered' && $order->getOriginal('status') === 'delivered') {
                $order->delivered_at = null;
            }

            // A delivered order is a finished order.
            if ($order->isDirty('status') && $order->status === 'delivered' && ! $order->isDirty('progress')) {
                $order->progress = 100;
            }

            // completed_at is what drives revenue, monthly attribution, and
            // carry-forward: an order only stops carrying into next month
            // once it's completed (or cancelled, below).
            if ($order->isDirty('status') && $order->status === 'completed' && $order->getOriginal('status') !== 'completed') {
                $order->completed_at = now();

                if (! $order->isDirty('progress')) {
                    $order->progress = 100;
                }
            } elseif ($order->isDirty('status') && $order->status !== 'completed' && $order->getOriginal('status') === 'completed') {
                $order->completed_at = null;
            }

            // Mirrors the completed_at handling above: cancelled_at is the
            // other timestamp that stops an order from carrying forward.
            if ($order->isDirty('status') && $order->status === 'cancelled' && $order->getOriginal('status') !== 'cancelled') {
                $order->cancelled_at = now();
            } elseif ($order->isDirty('status') && $order->status !== 'cancelled' && $order->getOriginal('status') === 'cancelled') {
                $order->cancelled_at = null;
            }
        });
    }

    /**
     * The month carry-forward filter shared by the order board and the main
     * dashboard: an order is visible in month M if it started on or before
     * the last day of M and was not finished before M began.
     *
     * @param  array<int, string>  $finishedStatuses
     */
    public function scopeInMonth(Builder $query, Carbon $monthStart, Carbon $monthEnd, array $finishedStatuses = ['delivered', 'completed']): Builder
    {
        return $query->where(function (Builder $q) use ($monthStart, $monthEnd, $finishedStatuses) {
            $q->where('start_date', '<=', $monthEnd)
                ->where(function (Builder $inner) use ($monthStart, $finishedStatuses) {
                    $inner->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
                        ->orWhere(function (Builder $done) use ($monthStart, $finishedStatuses) {
                            $done->whereIn('status', $finishedStatuses)
                                ->where(function (Builder $dt) use ($monthStart) {
                                    $dt->whereNull('delivered_at')
                                        ->orWhere('delivered_at', '>=', $monthStart);
                                });
                        });
                });
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function techStack(): BelongsTo
    {
        return $this->belongsTo(TechStack::class);
    }

    public function freelanceProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'freelance_profile_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The sales person who brought this order in.
     */
    public function salesUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    /**
     * How this order's amount is credited across sales people. One row when a
     * single person sold it, several when the credit is shared.
     */
    public function salesShares(): HasMany
    {
        return $this->hasMany(OrderSalesShare::class);
    }

    public function salesPeople(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'order_sales_shares')
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Limit a query to the orders a sales person is allowed to see: the ones
     * they opened, and the ones they hold a share of.
     */
    public function scopeVisibleToSalesUser(Builder $query, User $user): Builder
    {
        $ids = $user->salesVisibleUserIds();

        return $query->where(function (Builder $scoped) use ($ids) {
            $scoped->whereIn('sales_user_id', $ids)
                ->orWhereHas('salesShares', fn ($share) => $share->whereIn('user_id', $ids));
        });
    }

    /**
     * The part of this order's amount credited to a given sales person.
     */
    public function salesShareFor(int $userId): float
    {
        return (float) ($this->salesShares->firstWhere('user_id', $userId)?->amount ?? 0);
    }

    /**
     * The part of this order's net (post-platform-fee) amount credited to a
     * given sales person — their gross share scaled by the same fee ratio
     * applied to the whole order. This is what counts toward sales targets
     * and dashboards; the gross share above is only the entry-time split.
     */
    public function netSalesShareFor(int $userId): float
    {
        $grossValue = (float) ($this->value ?? 0);

        if ($grossValue <= 0.0) {
            return 0.0;
        }

        return round($this->salesShareFor($userId) * ($this->netValue() / $grossValue), 2);
    }

    /**
     * Amount not credited to anyone yet.
     */
    public function unattributedAmount(): float
    {
        return round((float) ($this->actual_revenue ?? $this->value ?? 0) - (float) $this->salesShares->sum('amount'), 2);
    }

    /**
     * Fields a sales person owns. Everything else on an order — status,
     * progress, team, tech stack, assignees, dates — belongs to operations.
     *
     * @return array<int, string>
     */
    public static function salesEditableFields(): array
    {
        return ['order_id', 'name', 'description', 'notes', 'credentials', 'value', 'freelance_profile_id'];
    }

    /**
     * How this order's net (post-platform-fee) value is credited across the
     * people who delivered it, once the order is completed.
     */
    public function valueDistributions(): HasMany
    {
        return $this->hasMany(OrderValueDistribution::class);
    }

    /**
     * The order's value after its platform's cut — the figure that gets
     * distributed once the order is completed.
     */
    public function netValue(): float
    {
        return PlatformFeeCalculator::netFor((float) ($this->value ?? 0), $this->freelance_profile_id);
    }

    /**
     * What's left of the net value after existing distribution shares.
     */
    public function undistributedValue(): float
    {
        return round($this->netValue() - (float) $this->valueDistributions->sum('amount'), 2);
    }

    public function isValueFullyDistributed(): bool
    {
        return abs($this->undistributedValue()) < 0.01 && $this->valueDistributions->isNotEmpty();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'order_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function audits(): HasMany
    {
        return $this->hasMany(OrderAudit::class)->orderBy('created_at', 'desc');
    }

    /**
     * Uploaded client documents, newest first.
     *
     * @return array<int, array{id: string, name: string, path: string, disk: string, mime: ?string, size: int, uploaded_at: string, uploaded_by: ?int, uploaded_by_name: ?string}>
     */
    public function documents(): array
    {
        return $this->client_documents ?? [];
    }

    /**
     * @return array{id: string, name: string, path: string, disk: string, mime: ?string, size: int, uploaded_at: string, uploaded_by: ?int, uploaded_by_name: ?string}|null
     */
    public function findDocument(string $documentId): ?array
    {
        foreach ($this->documents() as $document) {
            if ($document['id'] === $documentId) {
                return $document;
            }
        }

        return null;
    }
}
