<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSalesShare;
use App\Models\OrderValueDistribution;
use App\Models\Profile;
use App\Models\SalesTarget;
use App\Models\User;
use App\Services\PlatformFeeCalculator;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesLeadershipController extends Controller
{
    /**
     * Roles allowed on this page: admin/OM/PM see the whole company, a sales
     * leader sees the whole company too (comparing themselves against peers
     * is the point), but never a sales member.
     */
    private const ALLOWED_ROLES = ['admin', 'operation-manager', 'project-manager', 'sales-leader'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        abort_unless($user->hasRole(self::ALLOWED_ROLES), 403);

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();
        [$monthStart, $monthEnd] = GlobalMonth::periodFor($monthDate);

        $allSalesIds = User::withRoleName(['sales-leader', 'sales-member'])->pluck('id')->all();

        $sharesThisMonth = OrderSalesShare::with('user.salesLeader')
            ->whereIn('user_id', $allSalesIds)
            ->whereHas('order', fn ($order) => $order->whereBetween('created_at', [$monthStart, $monthEnd]))
            ->get();

        [$previousStart, $previousEnd] = GlobalMonth::periodFor($monthDate->copy()->subMonth());
        $previousShares = OrderSalesShare::whereIn('user_id', $allSalesIds)
            ->whereHas('order', fn ($order) => $order->whereBetween('created_at', [$previousStart, $previousEnd]))
            ->get();

        // Targets are set per calendar month, not per fiscal period.
        $targets = SalesTarget::amountsFor($allSalesIds, $monthDate);

        // Build the repeat map from the stored manual flag once, then reuse
        // it everywhere: an order is a Repeat when the salesperson flagged it
        // as one when the order was entered.
        $repeatById = array_fill_keys(
            Order::whereIn('id', $sharesThisMonth->pluck('order_id')->filter()->unique()->values()->all())
                ->where('is_repeat', true)
                ->pluck('id')
                ->all(),
            true
        );

        $company = $this->companySummary($sharesThisMonth, $previousShares, $targets, $repeatById);
        $leaderComparison = $this->leaderComparison($sharesThisMonth, $targets, $repeatById);
        $topPerformers = $this->topPerformers($sharesThisMonth, $targets, $repeatById);
        $trend = $this->companyTrend($allSalesIds);
        $bridge = $this->soldVsDistributedBridge($monthStart, $monthEnd, $company['sold']);

        $quarter = ceil($monthDate->month / 3);
        $startMonthNum = ($quarter - 1) * 3 + 1;
        $q1Date = Carbon::create($monthDate->year, $startMonthNum, 1)->startOfMonth();
        $q2Date = $q1Date->copy()->addMonth();
        $q3Date = $q1Date->copy()->addMonths(2);

        $quarterBreakdown = $this->quarterlyBreakdown($allSalesIds, $monthDate);

        $quarterNames = [
            'q1' => $q1Date->format('M'),
            'q2' => $q2Date->format('M'),
            'q3' => $q3Date->format('M'),
        ];

        return view('sales.leadership', compact(
            'company', 'leaderComparison', 'topPerformers', 'trend', 'bridge',
            'selectedMonth', 'availableMonths', 'monthDate', 'quarterBreakdown',
            'quarterNames', 'monthStart', 'monthEnd'
        ));
    }

    /**
     * Fresh (green) vs. repeat (yellow) split of a set of sales shares,
     * using the pre-computed repeat map. Amounts sum to the group's sold
     * total, so the two sides are consistent with every other number.
     *
     * @param  Collection<int, OrderSalesShare>  $shares
     * @param  array<int, bool>  $repeatById
     * @return array{freshAmount: float, repeatAmount: float, freshOrders: int, repeatOrders: int}
     */
    protected function freshRepeatSplit(Collection $shares, array $repeatById): array
    {
        $repeatIds = array_keys($repeatById);
        $repeatShares = $shares->whereIn('order_id', $repeatIds);

        return [
            'freshAmount' => round((float) $shares->reject(fn ($share) => isset($repeatById[$share->order_id]))->sum('amount'), 2),
            'repeatAmount' => round((float) $repeatShares->sum('amount'), 2),
            'freshOrders' => $shares->pluck('order_id')->unique()->diff($repeatShares->pluck('order_id')->unique())->count(),
            'repeatOrders' => $repeatShares->pluck('order_id')->unique()->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function companySummary(Collection $sharesThisMonth, Collection $previousShares, Collection $targets, array $repeatById): array
    {
        $sold = (float) $sharesThisMonth->sum('amount');
        $previousSold = (float) $previousShares->sum('amount');
        $target = (float) $targets->sum();
        $split = $this->freshRepeatSplit($sharesThisMonth, $repeatById);

        return array_merge([
            'sold' => $sold,
            'target' => $target,
            'attainment' => $target > 0 ? (int) round($sold / $target * 100) : null,
            'remaining' => max(0, $target - $sold),
            'change' => $previousSold > 0 ? round((($sold - $previousSold) / $previousSold) * 100, 1) : null,
            'ordersSold' => $sharesThisMonth->pluck('order_id')->unique()->count(),
            'activeSellers' => $sharesThisMonth->pluck('user_id')->unique()->count(),
        ], $split);
    }

    /**
     * Every sales leader's own group (themselves plus their members) held up
     * against every other leader's group, company-wide.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function leaderComparison(Collection $sharesThisMonth, Collection $targets, array $repeatById): Collection
    {
        return User::withRoleName('sales-leader')
            ->with('salesMembers:id,sales_leader_id')
            ->orderBy('name')
            ->get()
            ->map(function (User $leader) use ($sharesThisMonth, $targets, $repeatById) {
                $groupIds = $leader->salesMembers->pluck('id')->push($leader->id)->all();
                $groupShares = $sharesThisMonth->whereIn('user_id', $groupIds);
                $amount = (float) $groupShares->sum('amount');
                $target = (float) $targets->only($groupIds)->sum();
                $split = $this->freshRepeatSplit($groupShares, $repeatById);

                return [
                    'id' => $leader->id,
                    'name' => $leader->name,
                    'members' => count($groupIds) - 1,
                    'amount' => $amount,
                    'orders' => $groupShares->pluck('order_id')->unique()->count(),
                    'target' => $target,
                    'attainment' => $target > 0 ? (int) round($amount / $target * 100) : null,
                ] + $split;
            })
            ->sortByDesc('amount')
            ->values();
    }

    /**
     * Every individual sales person, ranked, regardless of which leader's
     * group they belong to.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function topPerformers(Collection $sharesThisMonth, Collection $targets, array $repeatById): Collection
    {
        return $sharesThisMonth
            ->groupBy('user_id')
            ->map(function (Collection $shares) use ($targets, $repeatById) {
                $seller = $shares->first()->user;
                $amount = (float) $shares->sum('amount');
                $target = (float) ($targets[$seller?->id] ?? 0);
                $split = $this->freshRepeatSplit($shares, $repeatById);

                return [
                    'id' => $seller?->id,
                    'name' => $seller?->name ?? 'Removed user',
                    'is_leader' => (bool) $seller?->isSalesLeader(),
                    'leader' => $seller?->salesLeader?->name,
                    'amount' => $amount,
                    'orders' => $shares->pluck('order_id')->unique()->count(),
                    'target' => $target,
                    'achievement' => $target > 0 ? (int) round($amount / $target * 100) : null,
                ] + $split;
            })
            ->sortByDesc('amount')
            ->values();
    }

    /**
     * @param  array<int, int>  $allSalesIds
     * @return Collection<int, array<string, mixed>>
     */
    protected function companyTrend(array $allSalesIds): Collection
    {
        $points = collect(range(11, 0))->map(fn (int $monthsAgo) => now()->subMonths($monthsAgo));

        // The exact cycle ranges each label draws from (honouring the
        // billing-cycle start day). Consecutive cycles never overlap, so one
        // query over the whole window can be bucketed precisely.
        $periods = $points->mapWithKeys(fn (Carbon $point) => [
            $point->format('Y-m') => GlobalMonth::periodFor($point),
        ]);

        [$windowStart, $windowEnd] = [$periods->first()[0], $periods->last()[1]];

        $shares = OrderSalesShare::query()
            ->whereIn('user_id', $allSalesIds)
            ->whereHas('order', fn ($order) => $order->whereBetween('created_at', [$windowStart, $windowEnd]))
            ->with('order:id,created_at')
            ->get(['id', 'user_id', 'order_id', 'amount']);

        $repeatById = array_fill_keys(
            Order::whereIn('id', $shares->pluck('order_id')->filter()->unique()->values()->all())
                ->where('is_repeat', true)
                ->pluck('id')
                ->all(),
            true
        );

        return $points->map(function (Carbon $point) use ($shares, $periods, $repeatById) {
            [$start, $end] = $periods[$point->format('Y-m')];

            $monthShares = $shares->filter(fn ($share) => $share->order?->created_at?->between($start, $end));
            $split = $this->freshRepeatSplit($monthShares, $repeatById);

            return [
                'label' => $point->format('M Y'),
                'short' => $point->format('M'),
                'amount' => round((float) $monthShares->sum('amount'), 2),
                'count' => $monthShares->pluck('order_id')->unique()->count(),
                'fresh' => $split['freshAmount'],
                'repeat' => $split['repeatAmount'],
            ];
        })->values();
    }

    /**
     * Links the two halves of the pipeline: what sales sold (gross, credited
     * when the order was opened) against what operations has actually paid
     * out (net, credited once an order is completed). The two rarely land in
     * the same month for the same orders, so this also reports the lifetime
     * conversion rate as the real health check.
     *
     * @return array<string, mixed>
     */
    protected function soldVsDistributedBridge(Carbon $monthStart, Carbon $monthEnd, float $soldThisMonth): array
    {
        $distributedThisMonth = (float) OrderValueDistribution::whereHas(
            'order',
            fn ($order) => $order->whereBetween('completed_at', [$monthStart, $monthEnd])
        )->sum('amount');

        $allTimeSoldGross = (float) Order::whereNotNull('sales_user_id')->sum('value');
        $allTimeDistributedNet = (float) OrderValueDistribution::sum('amount');

        // Resolve every order's platform fee in a single query (profile id →
        // platform name) instead of one Profile lookup per order.
        $pendingOrders = Order::whereNotNull('sales_user_id')
            ->where('status', '!=', 'cancelled')
            ->get(['id', 'value', 'freelance_profile_id']);

        $platforms = $pendingOrders->pluck('freelance_profile_id')->filter()->unique()->isNotEmpty()
            ? Profile::whereIn('id', $pendingOrders->pluck('freelance_profile_id')->filter()->unique()->all())->pluck('platform', 'id')
            : collect();

        $pendingNet = $pendingOrders->sum(fn (Order $order) => round(
            (float) ($order->value ?? 0) * (1 - PlatformFeeCalculator::rateForPlatform($platforms->get($order->freelance_profile_id))),
            2
        )) - $allTimeDistributedNet;

        return [
            'soldThisMonth' => $soldThisMonth,
            'distributedThisMonth' => $distributedThisMonth,
            'allTimeSoldGross' => $allTimeSoldGross,
            'allTimeDistributedNet' => $allTimeDistributedNet,
            'pendingNet' => max(0, round($pendingNet, 2)),
            'conversionPercent' => $allTimeSoldGross > 0 ? round($allTimeDistributedNet / $allTimeSoldGross * 100, 1) : null,
        ];
    }

    /**
     * Compute quarterly breakdown for the team/group.
     *
     * @param  array<int, int>  $sellerIds
     * @return Collection<int, array<string, mixed>>
     */
    protected function quarterlyBreakdown(array $sellerIds, Carbon $monthDate): Collection
    {
        if ($sellerIds === []) {
            return collect();
        }

        $quarter = ceil($monthDate->month / 3);
        $startMonthNum = ($quarter - 1) * 3 + 1;
        $q1Date = Carbon::create($monthDate->year, $startMonthNum, 1)->startOfMonth();
        $q2Date = $q1Date->copy()->addMonth();
        $q3Date = $q1Date->copy()->addMonths(2);

        [$q1Start, $q1End] = GlobalMonth::periodFor($q1Date);
        [$q2Start, $q2End] = GlobalMonth::periodFor($q2Date);
        [$q3Start, $q3End] = GlobalMonth::periodFor($q3Date);

        $orders = Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereBetween('created_at', [$q1Start, $q3End])
            ->with(['salesShares' => fn ($share) => $share->whereIn('user_id', $sellerIds)])
            ->get();

        return User::whereIn('id', $sellerIds)
            ->orderBy('name')
            ->get()
            ->map(function (User $seller) use ($orders, $q1Start, $q1End, $q2Start, $q2End, $q3Start, $q3End) {
                $sellerOrders = $orders->filter(fn (Order $order) => $order->salesShares->contains('user_id', $seller->id));

                $q1Amount = (float) $sellerOrders->filter(fn ($o) => $o->created_at->between($q1Start, $q1End))
                    ->sum(fn ($o) => $o->salesShareFor($seller->id));

                $q2Amount = (float) $sellerOrders->filter(fn ($o) => $o->created_at->between($q2Start, $q2End))
                    ->sum(fn ($o) => $o->salesShareFor($seller->id));

                $q3Amount = (float) $sellerOrders->filter(fn ($o) => $o->created_at->between($q3Start, $q3End))
                    ->sum(fn ($o) => $o->salesShareFor($seller->id));

                return [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'q1_amount' => round($q1Amount, 2),
                    'q2_amount' => round($q2Amount, 2),
                    'q3_amount' => round($q3Amount, 2),
                    'quarter_amount' => round($q1Amount + $q2Amount + $q3Amount, 2),
                ];
            })
            ->sortByDesc('quarter_amount')
            ->values();
    }
}
