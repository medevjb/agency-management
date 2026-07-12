<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * All authenticated users can view orders list.
     * Data scoping (which orders they see) is handled in controllers.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin, OM, PM, and team members can view any order.
     * Team leaders and stack leaders can only view their own team's orders.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager', 'team-member'])) {
            return true;
        }

        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            return $user->team_id === $order->team_id;
        }

        return false;
    }

    /**
     * All users can see order financial data.
     * Revenue = delivered/completed orders — visible to everyone for transparency.
     */
    public function viewAmounts(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Admin, OM, PM can create any order.
     * Team leader can create orders for their team.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader')) {
            return true;
        }

        return false;
    }

    /**
     * Admin, OM, PM can update any order.
     * Team leader can update their team's orders.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        return false;
    }

    /**
     * Admin, OM, PM can delete any order.
     * Team leader can delete their team's orders.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        return false;
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }
}
