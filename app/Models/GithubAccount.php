<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKeys;
use Database\Factories\GithubAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GithubAccount extends Model
{
    /** @use HasFactory<GithubAccountFactory> */
    use EncryptsRouteKeys, HasFactory;

    protected $fillable = [
        'label',
        'username',
        'token',
        'avatar_url',
        'created_by',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
        ];
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(GithubRepository::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
