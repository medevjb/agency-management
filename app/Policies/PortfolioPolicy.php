<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view any portfolio');
    }

    public function view(User $user, Portfolio $portfolio): bool
    {
        return $user->can('view any portfolio')
            || ($user->can('view own portfolio') && $portfolio->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('create portfolio');
    }

    public function update(User $user, Portfolio $portfolio): bool
    {
        return $user->can('edit any portfolio')
            || ($user->can('edit own portfolio') && $portfolio->user_id === $user->id);
    }

    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->can('delete any portfolio')
            || ($user->can('delete own portfolio') && $portfolio->user_id === $user->id);
    }

    public function feature(User $user): bool
    {
        return $user->can('feature portfolio');
    }
}
