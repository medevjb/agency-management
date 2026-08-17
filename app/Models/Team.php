<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKeys;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use EncryptsRouteKeys, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'team_leader_id',
    ];

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'team_id');
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'manager_team');
    }

    public function techStacks(): HasMany
    {
        return $this->hasMany(TechStack::class, 'team_id');
    }
}
