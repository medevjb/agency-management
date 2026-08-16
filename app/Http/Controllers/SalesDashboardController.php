<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\User;
use App\Support\GlobalMonth;
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

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();
        [$monthStart, $monthEnd] = GlobalMonth::periodFor($monthDate);

        $groupOrders = $this->ordersFor($groupIds, $monthStart, $monthEnd);
        $mineOrders = $mineIds === [] ? collect() : $this->ordersFor($mineIds, $monthStart, $monthEnd);

        [$previousStart, $previousEnd] = GlobalMonth::periodFor($monthDate->copy()->subMonth());
        $previousGroup = $this->ordersFor($groupIds, $previousStart, $previousEnd);
        $previousMine = $mineIds === [] ? collect() : $this->ordersFor($mineIds, $previousStart, $previousEnd);

        // Targets are set per calendar month, not per fiscal period.
        $targets = SalesTarget::amountsFor($groupIds, $monthDate);

        $group = $this->summarise($groupOrders, $previousGroup, $groupIds, $monthStart, $monthEnd, (float) $targets->sum());
        $mine = $mineIds === []
            ? null
            : $this->summarise($mineOrders, $previousMine, $mineIds, $monthStart, $monthEnd, (float) $targets->only($mineIds)->sum());

        $trend = $this->trend($groupIds, $mineIds);

        $bestMonth = $trend->sortByDesc('amount')->first();

        $memberBreakdown = $showsBreakdown
            ? $this->breakdown($groupIds, $groupOrders, $user, $targets)
            : collect();

        // Only sales users and admin ever reach this action (see the
        // abort_unless above), and both see every running order company-wide
        // here — same as admin's order dashboard — not scoped to a sales
        // group at all, since plenty of orders never carry sales credit.
        // Sales users get their own sales pinned to the top.
        $recentOrders = Order::query()
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
            ->with('salesUser')
            ->when($user->isSales(), fn ($q) => $q->orderByRaw('CASE WHEN sales_user_id = ? THEN 0 ELSE 1 END', [$user->id]))
            ->latest()
            ->take(8)
            ->get();

        return view('sales.dashboard', compact(
            'group', 'mine', 'trend', 'bestMonth', 'memberBreakdown', 'recentOrders',
            'selectedMonth', 'availableMonths', 'monthDate', 'showsBreakdown', 'isAdmin'
        ));
    }

    /**
     * A sample rendering of the sales-member dashboard, for admin/OM/PM to
     * see the layout without needing a real sales member's account. Every
     * figure here is fabricated — nothing is read from the database.
     */
    public function memberPreview()
    {
        abort_unless(auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']), 403);

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();

        $mine = [
            'amount' => 4200.0, 'count' => 5, 'averageDeal' => 840.0,
            'previousAmount' => 3600.0, 'changePercent' => 16.7,
            'deliveredAmount' => 2400.0, 'deliveredCount' => 3,
            'openAmount' => 1800.0, 'openCount' => 2, 'openProgress' => 55,
            'target' => 5000.0, 'achievement' => 84, 'remaining' => 800.0,
            'freshAmount' => 2600.0, 'repeatAmount' => 1600.0,
            'freshCount' => 3, 'repeatCount' => 2, 'repeatRate' => 40.0,
        ];

        $group = array_merge($mine, [
            'amount' => 11000.0, 'count' => 13, 'target' => 13000.0, 'achievement' => 85,
        ]);

        // A plausible, deterministic-looking twelve month curve rather than
        // random noise, so the preview looks the same on every visit.
        $sample = [2400, 2800, 3100, 2600, 3400, 3900, 3500, 4000, 4600, 4100, 4800, 4200];
        $trend = collect(range(11, 0))->map(function (int $monthsAgo, int $index) use ($sample) {
            $point = now()->subMonths($monthsAgo);
            $amount = (float) $sample[$index];

            return [
                'label' => $point->format('M Y'),
                'short' => $point->format('M'),
                'amount' => $amount * 2.6, // team total
                'mine' => $amount,
                'count' => (int) round($amount / 840),
                'fresh' => round($amount * 2.6 * 0.62, 2),
                'repeat' => round($amount * 2.6 * 0.38, 2),
            ];
        })->values();

        $bestMonth = $trend->sortByDesc('amount')->first();
        $memberBreakdown = collect();
        $recentOrders = collect();
        $showsBreakdown = false;
        $isAdmin = false;
        $isPreview = true;

        return view('sales.dashboard', compact(
            'group', 'mine', 'trend', 'bestMonth', 'memberBreakdown', 'recentOrders',
            'selectedMonth', 'availableMonths', 'monthDate', 'showsBreakdown', 'isAdmin', 'isPreview'
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
     * Orders these people hold sales credit in, each carrying `credit`: the
     * part of the order amount attributed to this group.
     *
     * @param  array<int, int>  $sellerIds
     * @return Collection<int, Order>
     */
    protected function ordersFor(array $sellerIds, Carbon $from, Carbon $to): Collection
    {
        if ($sellerIds === []) {
            return collect();
        }

        return Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereBetween('created_at', [$from, $to])
            ->with(['salesShares' => fn ($share) => $share->whereIn('user_id', $sellerIds)])
            ->get()
            ->each(fn (Order $order) => $order->credit = (float) $order->salesShares->sum('amount'));
    }

    /**
     * The twelve-month credit trend, from one query per group instead of one
     * per month. Each point is bucketed by its exact billing-cycle range so
     * the numbers match the old per-month queries precisely.
     *
     * @param  array<int, int>  $groupIds
     * @param  array<int, int>  $mineIds
     * @return Collection<int, array<string, mixed>>
     */
    protected function trend(array $groupIds, array $mineIds): Collection
    {
        $points = collect(range(11, 0))->map(fn (int $monthsAgo) => now()->subMonths($monthsAgo));

        $periods = $points->mapWithKeys(fn (Carbon $point) => [
            $point->format('Y-m') => GlobalMonth::periodFor($point),
        ]);

        [$windowStart, $windowEnd] = [$periods->first()[0], $periods->last()[1]];

        $groupOrders = $this->ordersInWindow($groupIds, $windowStart, $windowEnd);
        $mineOrders = $mineIds === [] ? collect() : $this->ordersInWindow($mineIds, $windowStart, $windowEnd);

        return $points->map(function (Carbon $point) use ($groupOrders, $mineOrders, $periods) {
            [$start, $end] = $periods[$point->format('Y-m')];

            $groupMonth = $groupOrders->filter(fn (Order $order) => $order->created_at->between($start, $end));
            $mineMonth = $mineOrders->filter(fn (Order $order) => $order->created_at->between($start, $end));

            return [
                'label' => $point->format('M Y'),
                'short' => $point->format('M'),
                'amount' => round((float) $groupMonth->sum('credit'), 2),
                'mine' => round((float) $mineMonth->sum('credit'), 2),
                'count' => $groupMonth->count(),
                'fresh' => round((float) $groupMonth->where('is_repeat', false)->sum('credit'), 2),
                'repeat' => round((float) $groupMonth->where('is_repeat', true)->sum('credit'), 2),
            ];
        })->values();
    }

    /**
     * @param  array<int, int>  $sellerIds
     * @return Collection<int, Order>
     */
    protected function ordersInWindow(array $sellerIds, Carbon $from, Carbon $to): Collection
    {
        if ($sellerIds === []) {
            return collect();
        }

        return Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereBetween('created_at', [$from, $to])
            ->with(['salesShares' => fn ($share) => $share->whereIn('user_id', $sellerIds)])
            ->get(['id', 'created_at', 'is_repeat'])
            ->each(fn (Order $order) => $order->credit = (float) $order->salesShares->sum('amount'));
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  Collection<int, Order>  $previousOrders
     * @param  array<int, int>  $sellerIds
     * @return array<string, mixed>
     */
    protected function summarise(Collection $orders, Collection $previousOrders, array $sellerIds, Carbon $monthStart, Carbon $monthEnd, float $target = 0.0): array
    {
        $amount = (float) $orders->sum('credit');
        $previousAmount = (float) $previousOrders->sum('credit');

        $fresh = $orders->filter(fn (Order $order) => ! $order->is_repeat);
        $repeat = $orders->filter(fn (Order $order) => $order->is_repeat);

        $delivered = $sellerIds === [] ? collect() : Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereIn('status', ['delivered', 'completed'])
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$monthStart, $monthEnd])
            ->get();

        $open = $sellerIds === [] ? collect() : Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
            ->get();

        return [
            'amount' => $amount,
            'count' => $orders->count(),
            'averageDeal' => $orders->count() ? $amount / $orders->count() : 0.0,
            'freshAmount' => (float) $fresh->sum('credit'),
            'repeatAmount' => (float) $repeat->sum('credit'),
            'freshCount' => $fresh->count(),
            'repeatCount' => $repeat->count(),
            'repeatRate' => $orders->count() > 0 ? round(($repeat->count() / $orders->count()) * 100, 1) : 0.0,
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
                $sold = $orders->filter(fn (Order $order) => $order->salesShares->contains('user_id', $seller->id));
                $amount = (float) $sold->sum(fn (Order $order) => $order->salesShareFor($seller->id));
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
