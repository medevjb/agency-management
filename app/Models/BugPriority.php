<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugPriority extends Model
{
    /** @use HasFactory<\Database\Factories\BugPriorityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function bugReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BugReport::class, 'bug_priority_id');
    }
}
