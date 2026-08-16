<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSalesShare;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\User;
use App\Support\GlobalMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if ($user->isSales()) {
            return redirect()->route('sales.dashboard');
        }

        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return redirect()->route('orders.dashboard');
        }

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();
        [$startDate, $endDate] = GlobalMonth::periodFor($monthDate);

        // Finished statuses: 'delivered' auto-sets delivered_at; 'completed' is set via bulk action.
        $finishedStatuses = ['delivered', 'completed'];

        // The month's carry-forward order set and the delivered set drive most
        // of the page's numbers. They change only when orders change, so the
        // two queries are cached per selected month and the PHP derivation
        // below stays cheap.
        $orders = $this->remember('dashboard.orders.'.$selectedMonth, fn () => Order::with(['team', 'techStack', 'creator'])
            ->inMonth($startDate, $endDate, $finishedStatuses)
            ->get());

        $deliveredOrders = $this->remember('dashboard.delivered.'.$selectedMonth, fn () => Order::whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->get());

        // Workload = active (pending/in-progress/on-hold) orders in this month
        $workloadOrders = $orders->whereIn('status', ['pending', 'in-progress', 'on-hold']);

        $stats = [
            'total' => $orders->count(),
            'delivered' => $deliveredOrders->count(),
            'workload' => $workloadOrders->count(),
            'revenue' => $deliveredOrders->sum('actual_revenue'),
            'workloadValue' => $workloadOrders->sum('value'),
            'pending' => $orders->where('status', 'pending')->count(),
            'inProgress' => $orders->where('status', 'in-progress')->count(),
            'onHold' => $orders->where('status', 'on-hold')->count(),
        ];

        $ordersByStatus = [
            'delivered' => $stats['delivered'],
            'pending' => $stats['pending'],
            'in-progress' => $stats['inProgress'],
            'on-hold' => $stats['onHold'],
        ];

        // Daily revenue by delivered_at within selected month
        $daysInMonth = $monthDate->daysInMonth;

        // Grouped in PHP rather than with DAY(), which only exists on MySQL.
        // One month of rows, so the cost is the same either way.
        $dailyRevenue = Order::whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->get(['delivered_at', 'actual_revenue'])
            ->groupBy(fn (Order $order) => (int) $order->delivered_at->format('j'))
            ->map(fn ($group) => (float) $group->sum('actual_revenue'))
            ->toArray();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (! isset($dailyRevenue[$i])) {
                $dailyRevenue[$i] = 0;
            }
        }
        ksort($dailyRevenue);

        // Daily orders by start_date within selected month
        $dailyOrders = Order::whereBetween('start_date', [$startDate, $endDate])
            ->get(['start_date'])
            ->groupBy(fn (Order $order) => (int) $order->start_date->format('j'))
            ->map(fn ($group) => $group->count())
            ->toArray();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (! isset($dailyOrders[$i])) {
                $dailyOrders[$i] = 0;
            }
        }
        ksort($dailyOrders);

        // Revenue by team (delivered in selected month)
        $revenueByTeam = Order::with('team')
            ->whereNotNull('team_id')
            ->whereHas('team')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->get()
            ->groupBy('team_id')
            ->map(function ($group) {
                return [
                    'team_name' => $group->first()->team->name,
                    'revenue' => $group->sum('actual_revenue') ?? 0,
                    'orders_count' => $group->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // Recent delivered orders (delivered this month)
        $recentDelivered = Order::with(['team', 'techStack', 'creator'])
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->orderBy('delivered_at', 'desc')
            ->take(6)
            ->get();

        // Recent workload orders (active in this month, carry-forward)
        $recentWorkload = Order::with(['team', 'techStack', 'creator'])
            ->inMonth($startDate, $endDate, $finishedStatuses)
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->orderBy('start_date', 'desc')
            ->take(6)
            ->get();

        $priorityStats = [
            'high' => $workloadOrders->where('priority', 'high')->count(),
            'medium' => $workloadOrders->where('priority', 'medium')->count(),
            'low' => $workloadOrders->where('priority', 'low')->count(),
        ];

        $deliveredInMonth = function ($query) use ($startDate, $endDate) {
            $query->whereIn('status', ['delivered', 'completed'])
                ->whereNotNull('delivered_at')
                ->whereBetween('delivered_at', [$startDate, $endDate]);
        };

        // whereHas rather than having(), which needs an aggregate query on SQLite.
        $teams = Team::withCount(['orders' => $deliveredInMonth])
            ->whereHas('orders', $deliveredInMonth)
            ->latest()
            ->take(4)
            ->get();

        $dueSoon = Order::with(['team'])
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(14))
            ->orderBy('end_date')
            ->take(4)
            ->get();

        $analytics = [
            'totalValue' => $workloadOrders->whereNotNull('value')->sum('value') ?? 0,
            'actualRevenue' => $stats['revenue'],
            'revenuePercent' => 0,
            'avgRevenuePerOrder' => 0,
            'highPriorityCount' => $workloadOrders->where('priority', 'high')->count(),
            'overdueCount' => $workloadOrders->filter(fn ($o) => $o->end_date && $o->end_date->lt(now()))->count(),
        ];

        if ($analytics['totalValue'] > 0) {
            $analytics['revenuePercent'] = ($analytics['actualRevenue'] / $analytics['totalValue']) * 100;
        }
        if ($stats['delivered'] > 0) {
            $analytics['avgRevenuePerOrder'] = $analytics['actualRevenue'] / $stats['delivered'];
        }

        // Previous month comparison
        $previousMonth = $monthDate->copy()->subMonth();
        [$prevStartDate, $prevEndDate] = GlobalMonth::periodFor($previousMonth);

        $previousStats = [
            'revenue' => Order::whereIn('status', ['delivered', 'completed'])
                ->whereNotNull('delivered_at')
                ->whereBetween('delivered_at', [$prevStartDate, $prevEndDate])
                ->sum('actual_revenue') ?? 0,
            'orders' => Order::where(function ($q) use ($prevStartDate, $prevEndDate) {
                $q->where('start_date', '<=', $prevEndDate)
                    ->where(function ($inner) use ($prevStartDate) {
                        $inner->whereNull('delivered_at')
                            ->orWhere('delivered_at', '>=', $prevStartDate);
                    });
            })->count(),
        ];

        $analytics['revenueChange'] = $previousStats['revenue'] > 0
            ? (($analytics['actualRevenue'] - $previousStats['revenue']) / $previousStats['revenue']) * 100
            : 0;
        $analytics['ordersChange'] = $previousStats['orders'] > 0
            ? (($stats['total'] - $previousStats['orders']) / $previousStats['orders']) * 100
            : 0;

        // ---- Sales ----
        // Half of this page is the sales side: what was sold this month, how it
        // tracks against the targets in force, and who is bringing it in.
        [$sharesThisMonth, $previousShares, $salesTargets, $salesStats, $leaderboard] = $this->remember(
            'dashboard.sales.'.$selectedMonth,
            function () use ($startDate, $endDate, $prevStartDate, $prevEndDate, $monthDate) {
                $salesUserIds = User::withRoleName(['sales-leader', 'sales-member'])->pluck('id')->all();

                // Credit, not order value: an order split between two sellers counts
                // once in total and once per person, at their own share.
                $sharesThisMonth = OrderSalesShare::with('user')
                    ->whereHas('order', fn ($order) => $order->whereBetween('created_at', [$startDate, $endDate]))
                    ->get();

                $previousShares = OrderSalesShare::whereHas('order', fn ($order) => $order->whereBetween('created_at', [$prevStartDate, $prevEndDate]))
                    ->get();

                $salesTargets = SalesTarget::amountsFor($salesUserIds, $monthDate);
                $soldAmount = (float) $sharesThisMonth->sum('amount');
                $previousSold = (float) $previousShares->sum('amount');
                $targetTotal = (float) $salesTargets->sum();
                $ordersSold = $sharesThisMonth->pluck('order_id')->unique()->count();

                $salesStats = [
                    'sold' => $soldAmount,
                    'count' => $ordersSold,
                    'averageDeal' => $ordersSold ? $soldAmount / $ordersSold : 0.0,
                    'target' => $targetTotal,
                    'achievement' => $targetTotal > 0 ? (int) round($soldAmount / $targetTotal * 100) : null,
                    'remaining' => max(0, $targetTotal - $soldAmount),
                    'change' => $previousSold > 0 ? round((($soldAmount - $previousSold) / $previousSold) * 100, 1) : null,
                    'previousSold' => $previousSold,
                    'sellers' => count($salesUserIds),
                    'inDelivery' => (float) OrderSalesShare::whereHas('order', fn ($order) => $order
                        ->whereNotIn('status', ['delivered', 'completed', 'cancelled']))
                        ->sum('amount'),
                ];

                $leaderboard = $sharesThisMonth
                    ->groupBy('user_id')
                    ->map(function ($shares) use ($salesTargets) {
                        $seller = $shares->first()->user;
                        $amount = (float) $shares->sum('amount');
                        $target = (float) ($salesTargets[$seller?->id] ?? 0);

                        return [
                            'id' => $seller?->id,
                            'name' => $seller?->name ?? 'Removed user',
                            'is_leader' => (bool) $seller?->isSalesLeader(),
                            'amount' => $amount,
                            'count' => $shares->pluck('order_id')->unique()->count(),
                            'target' => $target,
                            'achievement' => $target > 0 ? (int) round($amount / $target * 100) : null,
                        ];
                    })
                    // A leaderboard of people who sold nothing is just noise; the full
                    // roster lives on the sales dashboard.
                    ->filter(fn (array $seller) => $seller['amount'] > 0)
                    ->sortByDesc('amount')
                    ->values()
                    ->take(6);

                return [$sharesThisMonth, $previousShares, $salesTargets, $salesStats, $leaderboard];
            }
        );

        return view('dashboard', compact(
            'stats',
            'ordersByStatus',
            'dailyRevenue',
            'dailyOrders',
            'daysInMonth',
            'revenueByTeam',
            'recentDelivered',
            'recentWorkload',
            'priorityStats',
            'teams',
            'dueSoon',
            'selectedMonth',
            'availableMonths',
            'analytics',
            'salesStats',
            'leaderboard'
        ));
    }
}
