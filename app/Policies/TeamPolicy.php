<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * All authenticated users can view the teams list.
     * Data scoping (which teams) is handled in controllers.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin/OM/PM can view any team.
     * Team leader, stack leader, team member can only view their own team.
     */
    public function view(User $user, Team $team): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        return $user->team_id === $team->id;
    }

    /**
     * Only Admin, OM, PM can create teams.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Admin/OM/PM can update any team.
     * Team leader can update their own team.
     */
    public function update(User $user, Team $team): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }

    /**
     * Only Admin, OM, PM can delete teams.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function restore(User $user, Team $team): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Admin/OM/PM can add members to any team.
     * Team leader can add members to their own team.
     */
    public function addMembers(User $user, Team $team): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can remove members from any team.
     * Team leader can remove members from their own team.
     */
    public function removeMember(User $user, Team $team): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }
}
