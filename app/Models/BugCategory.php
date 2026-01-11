<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugCategory extends Model
{
    /** @use HasFactory<\Database\Factories\BugCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function bugReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BugReport::class, 'bug_category_id');
    }
}
