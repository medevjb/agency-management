<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tech_stack_id',
        'title',
        'description',
        'project_url',
        'github_url',
        'project_date',
        'status',
        'is_featured',
        'screenshot',
    ];

    protected function casts(): array
    {
        return [
            'project_date' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function techStack()
    {
        return $this->belongsTo(TechStack::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByTechStack($query, $techStackId)
    {
        return $query->where('tech_stack_id', $techStackId);
    }
}
