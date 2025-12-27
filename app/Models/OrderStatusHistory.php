<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'notes',
        'actual_revenue',
    ];

    protected function casts(): array
    {
        return [
            'actual_revenue' => 'decimal:2',
        ];
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
