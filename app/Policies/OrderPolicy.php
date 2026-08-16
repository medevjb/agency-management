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
     * Admin, OM, PM, team members, and sales users can view any order.
     * Team leaders and stack leaders can only view their own team's orders.
     * Sales leaders and members can see all orders like admin/OM/PM.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Sales users can see all orders
        if ($user->isSales()) {
            return true;
        }

        if ($user->hasRole(['operation-manager', 'project-manager', 'team-member'])) {
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

        return $user->isSales();
    }

    /**
     * Admin, OM, PM can update any order.
     * Team leader and stack leader can update their team's orders.
     * Sales people can update the orders they can see — the deal facts on
     * the edit page, and the operational fields inline on the details page.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($user->hasRole(['team-leader', 'stack-leader']) && $user->team_id === $order->team_id) {
            return true;
        }

        if ($user->isSales()) {
            return $this->view($user, $order);
        }

        return false;
    }

    /**
     * Who may touch the operational side of an order: status, priority,
     * dates, team, tech stack and special status. Sales people can too —
     * they just never get to change who is assigned (see manageAssignment).
     */
    public function manageOperations(User $user, Order $order): bool
    {
        if ($user->isSales()) {
            return true;
        }

        return $this->update($user, $order);
    }

    /**
     * Who may assign (or unassign) people to work on an order. Everyone with
     * operational rights except sales — sales opens the deal, operations
     * picks the team.
     */
    public function manageAssignment(User $user, Order $order): bool
    {
        if ($user->isSales()) {
            return false;
        }

        return $this->update($user, $order);
    }

    /**
     * Progress specifically is also open to whoever the order is actually
     * assigned to — a plain team-member has no other management rights on
     * an order, but they're the one doing the work, so they can report how
     * far along it is.
     */
    public function updateProgress(User $user, Order $order): bool
    {
        if ($this->manageOperations($user, $order)) {
            return true;
        }

        return $order->users->contains('id', $user->id);
    }

    /**
     * Who may edit the content fields — notes, description, credentials —
     * and add documents. Open to everyone who can manage the order plus the
     * people assigned to do the work.
     */
    public function editContent(User $user, Order $order): bool
    {
        if ($user->isSales()) {
            return true;
        }

        if ($this->update($user, $order)) {
            return true;
        }

        return $order->users->contains('id', $user->id);
    }

    /**
     * Who may remove a stored document. A plain team member can add files
     * but never delete them.
     */
    public function deleteDocuments(User $user, Order $order): bool
    {
        if ($user->isSales()) {
            return true;
        }

        return $this->update($user, $order);
    }

    /**
     * Admin, OM, PM can delete any order.
     * Team leader can delete their team's orders.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($user->isSales()) {
            return false;
        }

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
