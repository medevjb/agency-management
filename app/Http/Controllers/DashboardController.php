<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Team;
use Carbon\Carbon;
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

        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return redirect()->route('orders.dashboard');
        }

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $monthDate->copy()->startOfMonth()->startOfDay();
        $endDate = $monthDate->copy()->endOfMonth()->endOfDay();

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        // Finished statuses: 'delivered' auto-sets delivered_at; 'completed' is set via bulk action.
        $finishedStatuses = ['delivered', 'completed'];

        // Carry-forward logic: an order is visible in month M if:
        // - It started on or before the last day of M
        // - AND it was not finished before M began
        $carryForwardFilter = function ($q) use ($startDate, $endDate, $finishedStatuses) {
            $q->where('start_date', '<=', $endDate)
                ->where(function ($inner) use ($startDate, $finishedStatuses) {
                    // Active (not delivered/completed/cancelled)
                    $inner->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
                        // OR finished in or after this month started
                        ->orWhere(function ($done) use ($startDate, $finishedStatuses) {
                            $done->whereIn('status', $finishedStatuses)
                                ->where(function ($dt) use ($startDate) {
                                    $dt->whereNull('delivered_at')
                                        ->orWhere('delivered_at', '>=', $startDate);
                                });
                        });
                });
        };

        $orders = Order::with(['team', 'techStack', 'creator'])
            ->where($carryForwardFilter)
            ->get();

        // Delivered this month = delivered/completed AND delivered_at within selected month
        $deliveredOrders = Order::whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->get();

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

        $dailyRevenue = Order::selectRaw('DAY(delivered_at) as day, SUM(COALESCE(actual_revenue, 0)) as revenue')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('revenue', 'day')
            ->toArray();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (! isset($dailyRevenue[$i])) {
                $dailyRevenue[$i] = 0;
            }
        }
        ksort($dailyRevenue);

        // Daily orders by start_date within selected month
        $dailyOrders = Order::selectRaw('DAY(start_date) as day, COUNT(*) as count')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
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
            ->where($carryForwardFilter)
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->orderBy('start_date', 'desc')
            ->take(6)
            ->get();

        $priorityStats = [
            'high' => $workloadOrders->where('priority', 'high')->count(),
            'medium' => $workloadOrders->where('priority', 'medium')->count(),
            'low' => $workloadOrders->where('priority', 'low')->count(),
        ];

        $teams = Team::withCount(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereIn('status', ['delivered', 'completed'])
                ->whereNotNull('delivered_at')
                ->whereBetween('delivered_at', [$startDate, $endDate]);
        }])
            ->having('orders_count', '>', 0)
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
        $prevStartDate = $previousMonth->copy()->startOfMonth()->startOfDay();
        $prevEndDate = $previousMonth->copy()->endOfMonth()->endOfDay();

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
            'analytics'
        ));
    }
}
