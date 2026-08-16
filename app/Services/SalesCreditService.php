<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\User;
use Illuminate\Support\Collection;

class SalesCreditService
{
    /**
     * Apply the sales credit split to an order.
     *
     * Shares arrive as [['user_id' => 3, 'amount' => 250.00], ...]. With none
     * given the whole amount goes to the fallback owner, which keeps a plain
     * one-person sale a no-op for whoever is filling the form.
     *
     * @param  array<int, array{user_id: int|string, amount: int|float|string}>  $shares
     */
    public function apply(Order $order, array $shares, ?int $fallbackUserId = null): void
    {
        $shares = $this->normalise($shares);

        if ($shares === [] && $fallbackUserId !== null) {
            $shares = [$fallbackUserId => (float) ($order->actual_revenue ?? $order->value ?? 0)];
        }

        $before = $this->describe($order);

        $order->salesShares()->whereNotIn('user_id', array_keys($shares))->delete();

        foreach ($shares as $userId => $amount) {
            $order->salesShares()->updateOrCreate(
                ['user_id' => $userId],
                ['amount' => $amount],
            );
        }

        $order->load('salesShares.user');
        $after = $this->describe($order);

        if ($before !== $after) {
            OrderAudit::record($order, 'sales_credit_updated', ['sales_credit' => $before], ['sales_credit' => $after]);
        }
    }

    /**
     * Sum amounts per person and drop empty rows.
     *
     * @param  array<int, array{user_id: int|string, amount: int|float|string}>  $shares
     * @return array<int, float> amount keyed by user id
     */
    public function normalise(array $shares): array
    {
        $normalised = [];

        foreach ($shares as $share) {
            $userId = (int) ($share['user_id'] ?? 0);
            $amount = round((float) ($share['amount'] ?? 0), 2);

            if ($userId === 0) {
                continue;
            }

            $normalised[$userId] = round(($normalised[$userId] ?? 0) + $amount, 2);
        }

        return $normalised;
    }

    /**
     * The sales people this user may credit an order to: admins anyone in
     * sales, everyone else their own sales group.
     *
     * @return Collection<int, User>
     */
    public function creditableUsers(User $user)
    {
        if ($user->hasRole('admin')) {
            return User::withRoleName(['sales-leader', 'sales-member'])->orderBy('name')->get();
        }

        if (! $user->isSales()) {
            return collect();
        }

        $groupIds = $user->isSalesLeader()
            ? $user->salesVisibleUserIds()
            : User::where('sales_leader_id', $user->sales_leader_id)
                ->pluck('id')
                ->push($user->sales_leader_id)
                ->push($user->id)
                ->filter()
                ->unique()
                ->all();

        return User::whereIn('id', $groupIds)->orderBy('name')->get();
    }

    /**
     * A readable "name: amount" list, used for the change log.
     *
     * @return array<int, string>
     */
    protected function describe(Order $order): array
    {
        return $order->salesShares
            ->sortByDesc('amount')
            ->map(fn ($share) => ($share->user?->name ?? 'Unknown').': $'.number_format((float) $share->amount, 2))
            ->values()
            ->all();
    }
}
