<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all teams
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can view teams they're assigned to
        if ($user->isManager() && $user->managedTeams()->exists()) {
            return true;
        }

        // Team leaders can view teams (they'll only see their own in the controller)
        return $user->hasRole('team-leader') || $user->team_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        // Admins can view any team
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can view teams they're assigned to
        if ($user->isManager() && $user->isManagerOfTeam($team)) {
            return true;
        }

        // Team leaders can only view their own team
        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        // Stack leaders can view teams that use their tech stack
        if ($user->hasRole('stack-leader')) {
            return $team->members()->whereHas('orders', function ($query) use ($user) {
                $query->where('tech_stack_id', $user->tech_stack_id);
            })->exists();
        }

        // Team members can view their own team
        if ($user->team_id === $team->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create teams
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Admins can update any team
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can update teams they're assigned to
        if ($user->isManager() && $user->isManagerOfTeam($team)) {
            return true;
        }

        // Team leaders can update their own team (but not delete)
        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Only admins can delete teams (team leaders cannot delete)
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        // Only admins can restore teams
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        // Only admins can force delete teams
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can add members to the team.
     */
    public function addMembers(User $user, Team $team): bool
    {
        // Admins can add members to any team
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can add members to teams they manage
        if ($user->isManager() && $user->isManagerOfTeam($team)) {
            return true;
        }

        // Team leaders can add members to their own team
        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can remove members from the team.
     */
    public function removeMember(User $user, Team $team): bool
    {
        // Admins can remove members from any team
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can remove members from teams they manage
        if ($user->isManager() && $user->isManagerOfTeam($team)) {
            return true;
        }

        // Team leaders can remove members from their own team
        if ($user->hasRole('team-leader') && $user->team_id === $team->id) {
            return true;
        }

        return false;
    }
}
