<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKeys;
use Database\Factories\GithubRepositoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GithubRepository extends Model
{
    /** @use HasFactory<GithubRepositoryFactory> */
    use EncryptsRouteKeys, HasFactory;

    protected $fillable = [
        'github_account_id',
        'full_name',
        'name',
        'html_url',
        'default_branch',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(GithubAccount::class, 'github_account_id');
    }
}
