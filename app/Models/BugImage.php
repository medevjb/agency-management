<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugImage extends Model
{
    /** @use HasFactory<\Database\Factories\BugImageFactory> */
    use HasFactory;

    protected $fillable = [
        'bug_report_id',
        'image_path',
        'image_name',
        'size',
        'mime_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'size' => 'integer',
    ];

    public function bugReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BugReport::class);
    }

    public function getUrlAttribute(): string
    {
        return storage_path($this->image_path);
    }
}
