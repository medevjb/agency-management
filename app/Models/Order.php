<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'start_date',
        'end_date',
        'actual_revenue',
        'team_id',
        'tech_stack_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'actual_revenue' => 'decimal:2',
        ];
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function techStack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TechStack::class);
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
