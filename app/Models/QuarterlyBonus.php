<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuarterlyBonus extends Model
{
    protected $fillable = ['salesperson_id', 'year', 'quarter', 'quarter_sales', 'quarter_target', 'qualified_extra_sales', 'bonus_percentage_applied', 'bonus_source_currency', 'bonus_source_amount', 'exchange_rate_applied', 'payment_currency', 'bonus_payment_amount', 'status', 'calculated_at', 'finalized_at', 'paid_at'];

    protected function casts(): array
    {
        return ['quarter_sales' => 'decimal:2', 'quarter_target' => 'decimal:2', 'qualified_extra_sales' => 'decimal:2', 'bonus_percentage_applied' => 'decimal:6', 'bonus_source_amount' => 'decimal:4', 'exchange_rate_applied' => 'decimal:4', 'bonus_payment_amount' => 'decimal:2', 'calculated_at' => 'datetime', 'finalized_at' => 'datetime', 'paid_at' => 'datetime'];
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }
}
