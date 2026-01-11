<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugReport extends Model
{
    /** @use HasFactory<\Database\Factories\BugReportFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bug_category_id',
        'bug_priority_id',
        'title',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
        'published_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BugCategory::class, 'bug_category_id');
    }

    public function priority(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BugPriority::class, 'bug_priority_id');
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BugImage::class)->orderBy('sort_order');
    }

    public function resolvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
