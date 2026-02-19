<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view profiles') || $user->can('view freelance profiles');
    }

    public function view(User $user, Profile $profile): bool
    {
        return $user->can('view profiles') || $user->can('view freelance profiles');
    }

    public function create(User $user): bool
    {
        return $user->can('create profile') || $user->can('create freelance profile');
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->can('edit profile') || $user->can('edit freelance profile');
    }

    public function delete(User $user, Profile $profile): bool
    {
        return $user->can('delete profile') || $user->can('delete freelance profile');
    }
}
