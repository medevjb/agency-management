<?php

namespace App\Services;

use App\Models\User;

class TeamMemberService
{
    /**
     * Get list of users that the current user can manage.
     */
    public static function getManageableUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        // Super admin and operation manager can manage anyone
        if ($user->hasRole(['admin', 'operation-manager'])) {
            return User::orderBy('name')->get();
        }

        // Project manager can manage users in their projects
        if ($user->hasRole('project-manager')) {
            return User::whereHas('orders', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })->orderBy('name')->get();
        }

        // Team leader can manage their team members only
        if ($user->hasRole('team-leader') && $user->team_id) {
            return User::where('team_id', $user->team_id)
                ->orderBy('name')
                ->get();
        }

        // Tech stack leader can manage their tech stack members
        if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
            return User::whereHas('techStacks', function ($query) use ($user) {
                $query->where('tech_stacks.id', $user->tech_stack_id);
            })->orderBy('name')->get();
        }

        // Regular users can only manage themselves
        return User::where('id', $user->id)->get();
    }

    /**
     * Check if a user can manage/edit a specific target user.
     */
    public static function canManageUser(User $manager, User $targetUser): bool
    {
        // Super admin and operation manager can manage anyone
        if ($manager->hasRole(['admin', 'operation-manager'])) {
            return true;
        }

        // Project manager can manage users in their projects
        if ($manager->hasRole('project-manager')) {
            return $targetUser->orders()->whereHas('users', function ($q) use ($manager) {
                $q->where('users.id', $manager->id);
            })->exists();
        }

        // Team leader can manage their team members
        if ($manager->hasRole('team-leader') && $manager->team_id) {
            return $targetUser->team_id === $manager->team_id;
        }

        // Tech stack leader can manage their tech stack members
        if ($manager->hasRole('stack-leader') && $manager->tech_stack_id) {
            return $targetUser->techStacks()->where('tech_stacks.id', $manager->tech_stack_id)->exists();
        }

        // Regular users can only manage themselves
        return $targetUser->id === $manager->id;
    }

    /**
     * Get users that can be managed (for display in team member views).
     */
    public static function getTeamMembersForView(User $user): \Illuminate\Database\Eloquent\Collection
    {
        // For team leaders - get their team members
        if ($user->hasRole('team-leader') && $user->team_id) {
            return User::where('team_id', $user->team_id)
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        // For tech stack leaders - get their stack members
        if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
            return User::whereHas('techStacks', function ($query) use ($user) {
                $query->where('tech_stacks.id', $user->tech_stack_id);
            })
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        // For admins and managers - return all users
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return User::with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        // Regular team members - see themselves and team members
        if ($user->team_id) {
            return User::where('team_id', $user->team_id)
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        // If no team, return only themselves
        return User::where('id', $user->id)
            ->with(['roles', 'team', 'techStacks'])
            ->get();
    }
}
