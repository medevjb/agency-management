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

            return User::whereHas('techStacks', function ($query) use ($stackIds) {
                $query->whereIn('tech_stacks.id', $stackIds);
            })->orderBy('name')->get();
        }

        return User::where('id', $user->id)->get();
    }

    /**
     * Check if a user can manage/edit a specific target user.
     */
    public static function canManageUser(User $manager, User $targetUser): bool
    {
        if ($manager->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($manager->hasRole('team-leader') && $manager->team_id) {
            return $targetUser->team_id === $manager->team_id;
        }

        if ($manager->hasRole('stack-leader')) {
            $stackIds = $manager->techStacks()->pluck('tech_stacks.id');

            return $targetUser->techStacks()->whereIn('tech_stacks.id', $stackIds)->exists();
        }

        return $targetUser->id === $manager->id;
    }

    /**
     * Get users for team members view display.
     */
    public static function getTeamMembersForView(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return User::with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('team-leader') && $user->team_id) {
            return User::where('team_id', $user->team_id)
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('stack-leader')) {
            $stackIds = $user->techStacks()->pluck('tech_stacks.id');

            return User::whereHas('techStacks', function ($query) use ($stackIds) {
                $query->whereIn('tech_stacks.id', $stackIds);
            })
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        if ($user->team_id) {
            return User::where('team_id', $user->team_id)
                ->with(['roles', 'team', 'techStacks'])
                ->orderBy('name')
                ->get();
        }

        return User::where('id', $user->id)
            ->with(['roles', 'team', 'techStacks'])
            ->get();
    }
}
