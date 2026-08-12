<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SalesDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->isSales() || $user->hasRole('admin'), 403);

        $isAdmin = $user->hasRole('admin');

        // "Mine" is what this person sold; "group" is the whole sales unit they
        // belong to. A member sees both, so they can place themselves in the
        // team total. An admin sells nothing, so they only get the group view.
        $mineIds = $isAdmin ? [] : [$user->id];
        $groupIds = $this->groupIds($user, $isAdmin);

        $showsBreakdown = $user->isSalesLeader() || $isAdmin;

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        $groupOrders = $this->ordersFor($groupIds, $monthStart, $monthEnd);
        $mineOrders = $mineIds === [] ? collect() : $groupOrders->whereIn('sales_user_id', $mineIds);

        $previousStart = $monthStart->copy()->subMonth();
        $previousGroup = $this->ordersFor($groupIds, $previousStart, $previousStart->copy()->endOfMonth());
        $previousMine = $mineIds === [] ? collect() : $previousGroup->whereIn('sales_user_id', $mineIds);

        // Targets in force this month, per person.
        $targets = SalesTarget::amountsFor($groupIds, $monthStart);

        $group = $this->summarise($groupOrders, $previousGroup, $groupIds, $monthStart, $monthEnd, (float) $targets->sum());
        $mine = $mineIds === []
            ? null
            : $this->summarise($mineOrders, $previousMine, $mineIds, $monthStart, $monthEnd, (float) $targets->only($mineIds)->sum());

        $trend = collect(range(11, 0))->map(function (int $monthsAgo) use ($groupIds, $mineIds) {
            $point = now()->subMonths($monthsAgo);
            $orders = $this->ordersFor($groupIds, $point->copy()->startOfMonth(), $point->copy()->endOfMonth());

            return [
                'label' => $point->format('M Y'),
                'short' => $point->format('M'),
                'amount' => round((float) $orders->sum('value'), 2),
                'mine' => $mineIds === [] ? 0.0 : round((float) $orders->whereIn('sales_user_id', $mineIds)->sum('value'), 2),
                'count' => $orders->count(),
            ];
        })->values();

        $bestMonth = $trend->sortByDesc('amount')->first();

        $memberBreakdown = $showsBreakdown
            ? $this->breakdown($groupIds, $groupOrders, $user, $targets)
            : collect();

        $recentOrders = Order::query()
            ->whereIn('sales_user_id', $groupIds)
            ->with('salesUser')
            ->latest()
            ->take(8)
            ->get();

        return view('sales.dashboard', compact(
            'group', 'mine', 'trend', 'bestMonth', 'memberBreakdown', 'recentOrders',
            'selectedMonth', 'availableMonths', 'monthDate', 'showsBreakdown', 'isAdmin'
        ));
    }

    /**
     * Every sales person whose numbers roll up into this person's view.
     *
     * @return array<int, int>
     */
    protected function groupIds(User $user, bool $isAdmin): array
    {
        if ($isAdmin) {
            return User::withRoleName(['sales-leader', 'sales-member'])->pluck('id')->all();
        }

        if ($user->isSalesLeader()) {
            return $user->salesVisibleUserIds();
        }

        // A member rolls up into their leader's group, so they can see the team
        // total alongside their own.
        if ($user->sales_leader_id) {
            return User::where('sales_leader_id', $user->sales_leader_id)
                ->pluck('id')
                ->push($user->sales_leader_id)
                ->unique()
                ->values()
                ->all();
        }

        return [$user->id];
    }

    /**
     * @param  array<int, int>  $sellerIds
     * @return Collection<int, Order>
     */
    protected function ordersFor(array $sellerIds, Carbon $from, Carbon $to): Collection
    {
        if ($sellerIds === []) {
            return collect();
        }

        return Order::query()
            ->whereIn('sales_user_id', $sellerIds)
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  Collection<int, Order>  $previousOrders
     * @param  array<int, int>  $sellerIds
     * @return array<string, mixed>
     */
    protected function summarise(Collection $orders, Collection $previousOrders, array $sellerIds, Carbon $monthStart, Carbon $monthEnd, float $target = 0.0): array
    {
        $amount = (float) $orders->sum('value');
        $previousAmount = (float) $previousOrders->sum('value');

        $delivered = $sellerIds === [] ? collect() : Order::query()
            ->whereIn('sales_user_id', $sellerIds)
            ->whereIn('status', ['delivered', 'completed'])
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$monthStart, $monthEnd])
            ->get();

        $open = $sellerIds === [] ? collect() : Order::query()
            ->whereIn('sales_user_id', $sellerIds)
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
            ->get();

        return [
            'amount' => $amount,
            'count' => $orders->count(),
            'averageDeal' => $orders->count() ? $amount / $orders->count() : 0.0,
            'previousAmount' => $previousAmount,
            'changePercent' => $previousAmount > 0 ? round((($amount - $previousAmount) / $previousAmount) * 100, 1) : null,
            'deliveredAmount' => (float) $delivered->sum('value'),
            'deliveredCount' => $delivered->count(),
            'openAmount' => (float) $open->sum('value'),
            'openCount' => $open->count(),
            // How far along the work is on everything still in delivery.
            'openProgress' => $open->count() ? (int) round($open->avg('progress')) : 0,
            'target' => $target,
            'achievement' => $target > 0 ? (int) round($amount / $target * 100) : null,
            'remaining' => max(0, $target - $amount),
        ];
    }

    /**
     * Per-seller split of the month, for leaders and admins.
     *
     * @param  array<int, int>  $sellerIds
     * @param  Collection<int, Order>  $orders
     * @param  Collection<int, float>  $targets  target per user id
     * @return Collection<int, array<string, mixed>>
     */
    protected function breakdown(array $sellerIds, Collection $orders, User $viewer, Collection $targets): Collection
    {
        return User::whereIn('id', $sellerIds)
            ->with('salesLeader')
            ->orderBy('name')
            ->get()
            ->map(function (User $seller) use ($orders, $viewer, $targets) {
                $sold = $orders->where('sales_user_id', $seller->id);
                $amount = (float) $sold->sum('value');
                $target = (float) ($targets[$seller->id] ?? 0);

                return [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'is_leader' => $seller->isSalesLeader(),
                    'is_you' => $seller->id === $viewer->id,
                    'leader' => $seller->salesLeader?->name,
                    'count' => $sold->count(),
                    'amount' => $amount,
                    'target' => $target,
                    'achievement' => $target > 0 ? (int) round($amount / $target * 100) : null,
                    'remaining' => max(0, $target - $amount),
                ];
            })
            ->sortByDesc(fn (array $seller) => $seller['achievement'] ?? -1)
            ->values();
    }
}
