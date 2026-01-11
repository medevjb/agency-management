<?php

namespace App\Policies;

use App\Models\TechStack;
use App\Models\User;

class TechStackPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admins and managers can view tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TechStack $techStack): bool
    {
        // Only admins and managers can view tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins and managers can create tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TechStack $techStack): bool
    {
        // Only admins and managers can update tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TechStack $techStack): bool
    {
        // Only admins can delete tech stacks
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TechStack $techStack): bool
    {
        // Only admins can restore tech stacks
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TechStack $techStack): bool
    {
        // Only admins can force delete tech stacks
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can add members to the tech stack.
     */
    public function addMembers(User $user, TechStack $techStack): bool
    {
        // Only admins and managers can add members to tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can remove members from the tech stack.
     */
    public function removeMember(User $user, TechStack $techStack): bool
    {
        // Only admins and managers can remove members from tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }
}
