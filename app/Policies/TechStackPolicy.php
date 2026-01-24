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
        // Admins, managers, team leaders, and stack leaders can view tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager', 'team-leader', 'stack-leader']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TechStack $techStack): bool
    {
        // Admins and managers can view any tech stack
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can view tech stacks created by them or assigned to their team
        if ($user->hasRole('team-leader')) {
            return $techStack->stack_leader_id === $user->id
                || $techStack->team_id === $user->team_id;
        }

        // Stack leaders can view tech stacks they lead
        if ($user->hasRole('stack-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins, operation managers, project managers, and team leaders can create tech stacks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager', 'team-leader']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TechStack $techStack): bool
    {
        // Admins and operation managers can update any tech stack
        if ($user->hasRole(['admin', 'operation-manager'])) {
            return true;
        }

        // Team leaders can update tech stacks created by them
        if ($user->hasRole('team-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        // Stack leaders can update their tech stacks
        if ($user->hasRole('stack-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        return false;
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
        // Admins and managers can add any members
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can add their team members
        if ($user->hasRole('team-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        // Stack leaders can add members to their tech stack
        if ($user->hasRole('stack-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can remove members from the tech stack.
     */
    public function removeMember(User $user, TechStack $techStack): bool
    {
        // Admins and managers can remove any members
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can remove their team members
        if ($user->hasRole('team-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        // Stack leaders can remove members from their tech stack
        if ($user->hasRole('stack-leader')) {
            return $techStack->stack_leader_id === $user->id;
        }

        return false;
    }
}
