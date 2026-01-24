<?php

namespace App\Services;

use App\Models\User;

class TaskAssignmentService
{
    /**
     * Get list of users to whom the current user can assign tasks.
     */
    public static function getAssignableUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        // Super admin and operation manager can assign to anyone
        if ($user->hasRole(['admin', 'operation-manager'])) {
            return User::orderBy('name')->get();
        }

        // Project manager can assign to users in their projects/orders
        if ($user->hasRole('project-manager')) {
            // Get users from orders managed by this project manager
            return User::whereHas('orders', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })->orderBy('name')->get();
        }

        // Team leader can assign to their team members only
        if ($user->hasRole('team-leader') && $user->team_id) {
            // Get team members (including the team leader)
            return User::where('team_id', $user->team_id)
                ->orderBy('name')
                ->get();
        }

        // Tech stack leader can assign to their tech stack members
        if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
            return User::whereHas('techStacks', function ($query) use ($user) {
                $query->where('tech_stacks.id', $user->tech_stack_id);
            })->orderBy('name')->get();
        }

        // Regular team members can only create tasks for themselves
        return User::where('id', $user->id)->get();
    }

    /**
     * Check if a user can assign a task to a specific target user.
     */
    public static function canAssignTo(User $assigner, User $assignee): bool
    {
        // Super admin and operation manager can assign to anyone
        if ($assigner->hasRole(['admin', 'operation-manager'])) {
            return true;
        }

        // Project manager can assign to users in their projects
        if ($assigner->hasRole('project-manager')) {
            return $assignee->orders()->whereHas('users', function ($q) use ($assigner) {
                $q->where('users.id', $assigner->id);
            })->exists();
        }

        // Team leader can assign to their team members
        if ($assigner->hasRole('team-leader') && $assigner->team_id) {
            return $assignee->team_id === $assigner->team_id;
        }

        // Tech stack leader can assign to their tech stack members
        if ($assigner->hasRole('stack-leader') && $assigner->tech_stack_id) {
            return $assignee->techStacks()->where('tech_stacks.id', $assigner->tech_stack_id)->exists();
        }

        // Regular users can only assign to themselves
        return $assignee->id === $assigner->id;
    }

    /**
     * Get default team ID for task created by user.
     */
    public static function getDefaultTeamId(User $user): ?int
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return null; // No restriction
        }

        if ($user->hasRole('team-leader') && $user->team_id) {
            return $user->team_id;
        }

        // For team members and stack leaders, use their team
        return $user->team_id;
    }
}
