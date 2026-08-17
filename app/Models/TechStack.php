<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKeys;
use Database\Factories\TechStackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechStack extends Model
{
    /** @use HasFactory<TechStackFactory> */
    use EncryptsRouteKeys, HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'stack_leader_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function stackLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stack_leader_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tech_stack_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
