<?php

namespace App\Models;

use App\Observers\OrderObserver;
use App\SpecialStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'name',
        'description',
        'notes',
        'credentials',
        'client_documents',
        'status',
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
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'actual_revenue' => 'decimal:2',
            'delivered_at' => 'datetime',
            'special_status' => SpecialStatus::class,
            'last_update_date' => 'datetime',
            'credentials' => 'encrypted',
            'client_documents' => 'array',
            'progress' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function ($order) {
            // Automatically set delivered_at when status changes to delivered
            if ($order->isDirty('status') && $order->status === 'delivered' && $order->getOriginal('status') !== 'delivered') {
                $order->delivered_at = now();
            }
            // Clear delivered_at when status changes from delivered
            elseif ($order->isDirty('status') && $order->status !== 'delivered' && $order->getOriginal('status') === 'delivered') {
                $order->delivered_at = null;
            }

            // A delivered order is a finished order.
            if ($order->isDirty('status') && $order->status === 'delivered' && ! $order->isDirty('progress')) {
                $order->progress = 100;
            }
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
     * Limit a query to the orders a sales person is allowed to see.
     */
    public function scopeVisibleToSalesUser(Builder $query, User $user): Builder
    {
        return $query->whereIn('sales_user_id', $user->salesVisibleUserIds());
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
