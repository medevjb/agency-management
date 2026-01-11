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
        // Admins can view all orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can view orders from teams they manage
        if ($user->isManager()) {
            return true;
        }

        // Team leaders, stack leaders, and members can view orders from their team
        return $user->team_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Admins can view any order
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can view orders from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
        }

        // Team leaders can view orders from their team
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can view orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members can view orders from their team
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
        // Admins can create orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can create orders
        if ($user->isManager()) {
            return true;
        }

        // Team leaders can create orders for their team
        if ($user->hasRole('team-leader')) {
            return true;
        }

        // Team members can create orders for their team
        return $user->team_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Admins can update any order
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can update orders from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
        }

        // Team leaders can update orders from their team
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can update orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        // Admins can delete any order
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can delete orders from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
        }

        // Team leaders can delete orders from their team
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can delete orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        // Only admins can restore orders
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        // Only admins can force delete orders
        return $user->hasRole('admin');
    }
}
