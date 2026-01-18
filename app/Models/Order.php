<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'name',
        'description',
        'status',
        'priority',
        'value',
        'start_date',
        'end_date',
        'actual_revenue',
        'delivered_at',
        'team_id',
        'tech_stack_id',
        'freelance_profile_id',
        'order_sheet_url',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'actual_revenue' => 'decimal:2',
            'delivered_at' => 'datetime',
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
        });
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function techStack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TechStack::class);
    }

    public function freelanceProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'freelance_profile_id');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'order_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
