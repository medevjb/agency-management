<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Operation Manager, Project Manager can view all orders
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can view their team's orders
        if ($user->hasRole('team-leader') && $user->team_id) {
            return true;
        }

        // Stack leaders can view orders using their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
            return true;
        }

        // Team members can view their team's orders
        if ($user->team_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin, Operation Manager, Project Manager can view any order
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can view their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can view orders using their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members can view their team's orders
        if ($user->team_id === $order->team_id) {
            return true;
        }

        // Users assigned to the order can view it
        if ($order->users()->where('users.id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, Operation Manager, Project Manager can create orders
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can create orders for their team
        if ($user->hasRole('team-leader')) {
            return true;
        }

        // Team members cannot create orders
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin, Operation Manager, Project Manager can update any order
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can update their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can manage orders using their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members cannot update orders
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        // Admin, Operation Manager, Project Manager can delete any order
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can delete their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can delete orders using their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members cannot delete orders
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        // Admin, Operation Manager, Project Manager can restore orders
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        // Only admin can force delete orders
        return $user->hasRole('admin');
    }
}
