<?php

namespace App\Services;

use App\Models\User;

class TaskAssignmentService
{
    /**
     * Get list of users to whom the current user can assign tasks.
     *
     * Admin/OM/PM → anyone
     * Team leader → team members
     * Stack leader → stack members
     * Team member → only themselves
     */
    public static function getAssignableUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return User::orderBy('name')->get();
        }

        if ($user->hasRole('team-leader') && $user->team_id) {
            return User::where('team_id', $user->team_id)
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('stack-leader')) {
            $stackIds = $user->techStacks()->pluck('tech_stacks.id');

            return User::where(function ($q) use ($user, $stackIds) {
                $q->whereHas('techStacks', function ($query) use ($stackIds) {
                    $query->whereIn('tech_stacks.id', $stackIds);
                })->orWhere('id', $user->id);
            })->orderBy('name')->get();
        }

        return User::where('id', $user->id)->get();
    }

    /**
     * Check if a user can assign a task to a specific target user.
     */
    public static function canAssignTo(User $assigner, User $assignee): bool
    {
        if ($assigner->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($assigner->hasRole('team-leader') && $assigner->team_id) {
            return $assignee->team_id === $assigner->team_id;
        }

        if ($assigner->hasRole('stack-leader')) {
            $stackIds = $assigner->techStacks()->pluck('tech_stacks.id');

            return $assignee->techStacks()->whereIn('tech_stacks.id', $stackIds)->exists()
                || $assignee->id === $assigner->id;
        }

        return $assignee->id === $assigner->id;
    }

    /**
     * Get default team ID for task created by user.
     */
    public static function getDefaultTeamId(User $user): ?int
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return null;
        }

        return $user->team_id;
    }
}
