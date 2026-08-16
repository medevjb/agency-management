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
     * Only Admin, OM, PM can update teams. Every other role — including
     * team leaders and sales leaders — gets view-only access.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
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
     * Only Admin, OM, PM can add members to a team.
     */
    public function addMembers(User $user, Team $team): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Only Admin, OM, PM can remove members from a team.
     */
    public function removeMember(User $user, Team $team): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }
}
