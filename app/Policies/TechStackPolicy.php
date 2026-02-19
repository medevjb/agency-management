<?php

namespace App\Policies;

use App\Models\TechStack;
use App\Models\User;

class TechStackPolicy
{
    /**
     * All authenticated users can view the tech stacks list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin/OM/PM can view any tech stack.
     * Team leader can view tech stacks from their team.
     * Stack leader can view tech stacks they lead or belong to.
     * Team member can view their team's tech stacks.
     */
    public function view(User $user, TechStack $techStack): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $techStack->team_id === $user->team_id) {
            return true;
        }

        if ($user->hasRole('stack-leader')) {
            if ($techStack->stack_leader_id === $user->id) {
                return true;
            }
            if ($techStack->team_id === $user->team_id) {
                return true;
            }
        }

        if ($techStack->team_id === $user->team_id) {
            return true;
        }

        return false;
    }

    /**
     * Admin, OM, PM, team leader can create tech stacks.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager', 'team-leader']);
    }

    /**
     * Admin/OM/PM can update any tech stack.
     * Team leader can update tech stacks from their team.
     * Stack leader can update tech stacks they lead.
     */
    public function update(User $user, TechStack $techStack): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $techStack->team_id === $user->team_id) {
            return true;
        }

        if ($user->hasRole('stack-leader') && $techStack->stack_leader_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can delete any tech stack.
     * Team leader can delete tech stacks from their team.
     */
    public function delete(User $user, TechStack $techStack): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $techStack->team_id === $user->team_id) {
            return true;
        }

        return false;
    }

    public function restore(User $user, TechStack $techStack): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, TechStack $techStack): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Admin/OM/PM can add members to any tech stack.
     * Team leader can add members to their team's tech stacks.
     * Stack leader can add members to their own tech stack.
     */
    public function addMembers(User $user, TechStack $techStack): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $techStack->team_id === $user->team_id) {
            return true;
        }

        if ($user->hasRole('stack-leader') && $techStack->stack_leader_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can remove members from any tech stack.
     * Team leader can remove members from their team's tech stacks.
     * Stack leader can remove members from their own tech stack.
     */
    public function removeMember(User $user, TechStack $techStack): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $techStack->team_id === $user->team_id) {
            return true;
        }

        if ($user->hasRole('stack-leader') && $techStack->stack_leader_id === $user->id) {
            return true;
        }

        return false;
    }
}
