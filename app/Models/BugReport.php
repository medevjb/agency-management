<?php

namespace App\Models;

use Database\Factories\BugReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugReport extends Model
{
    /** @use HasFactory<BugReportFactory> */
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
        'ai_triage_reasoning',
        'ai_triage_confidence',
        'ai_triaged_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'published_at' => 'datetime',
        'ai_triage_confidence' => 'integer',
        'ai_triaged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BugCategory::class, 'bug_category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(BugPriority::class, 'bug_priority_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BugImage::class)->orderBy('sort_order');
    }

    public function resolvedBy(): BelongsTo
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
