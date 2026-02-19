<?php

namespace App\Http\Controllers;

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

        // Admin, OM, PM get the full dashboard. Team leader gets redirected to orders dashboard (which has team progress).
        // Stack leader and team member also go to orders dashboard.
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return redirect()->route('orders.dashboard');
        }

        // Get selected month from request (default to current month)
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        // Parse the selected month
        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $monthDate->copy()->startOfMonth()->startOfDay();
        $endDate = $monthDate->copy()->endOfMonth()->endOfDay();

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        // Base query for the selected month
        $baseQuery = \App\Models\Order::with(['team', 'techStack', 'creator'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Get all orders for statistics in selected month
        $orders = (clone $baseQuery)->get();

        // Delivered (completed) orders - these generate revenue
        $deliveredOrders = $orders->where('status', 'completed');

        // Workload (active) orders - these are in progress but not delivered yet
        $workloadOrders = $orders->whereIn('status', ['pending', 'in-progress', 'on-hold']);

        // Calculate statistics for selected month
        $stats = [
            'total' => $orders->count(),
            'delivered' => $deliveredOrders->count(),
            'workload' => $workloadOrders->count(),
            'revenue' => $deliveredOrders->whereNotNull('actual_revenue')->sum('actual_revenue'),
            'pending' => $orders->where('status', 'pending')->count(),
            'inProgress' => $orders->where('status', 'in-progress')->count(),
            'onHold' => $orders->where('status', 'on-hold')->count(),
        ];

        // Orders by status - focusing on delivered vs workload
        $ordersByStatus = [
            'delivered' => $stats['delivered'],
            'pending' => $stats['pending'],
            'in-progress' => $stats['inProgress'],
            'on-hold' => $stats['onHold'],
        ];

        // Daily delivered revenue for the selected month
        $daysInMonth = $monthDate->daysInMonth;

        $dailyRevenue = \App\Models\Order::selectRaw('DAY(created_at) as day, SUM(CASE WHEN actual_revenue IS NOT NULL THEN actual_revenue ELSE 0 END) as revenue')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('revenue', 'day')
            ->toArray();

        // Fill in missing days with 0
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (! isset($dailyRevenue[$i])) {
                $dailyRevenue[$i] = 0;
            }
        }
        ksort($dailyRevenue);

        // Daily orders count for the selected month (for workload chart)
        $dailyOrders = \App\Models\Order::selectRaw('DAY(created_at) as day, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Fill in missing days with 0
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (! isset($dailyOrders[$i])) {
                $dailyOrders[$i] = 0;
            }
        }
        ksort($dailyOrders);

        // Revenue by team (only delivered orders in selected month)
        $revenueByTeam = \App\Models\Order::with('team')
            ->whereNotNull('team_id')
            ->whereHas('team')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
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

        // Recent delivered orders in selected month
        $recentDelivered = \App\Models\Order::with(['team', 'techStack', 'creator'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(6)
            ->get();

        // Recent workload orders in selected month
        $recentWorkload = \App\Models\Order::with(['team', 'techStack', 'creator'])
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(6)
            ->get();

        // Orders by priority (only workload orders in selected month)
        $priorityStats = [
            'high' => $workloadOrders->where('priority', 'high')->count(),
            'medium' => $workloadOrders->where('priority', 'medium')->count(),
            'low' => $workloadOrders->where('priority', 'low')->count(),
        ];

        // Teams with order counts for selected month
        $teams = \App\Models\Team::withCount(['orders' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->having('orders_count', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        // Due soon (workload orders only - across all time, not filtered by month)
        $dueSoon = \App\Models\Order::with(['team'])
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(14))
            ->orderBy('end_date')
            ->take(4)
            ->get();

        // Additional analytics for selected month
        $analytics = [
            'totalValue' => $orders->whereNotNull('value')->sum('value') ?? 0,
            'actualRevenue' => $deliveredOrders->whereNotNull('actual_revenue')->sum('actual_revenue') ?? 0,
            'revenuePercent' => 0,
            'avgRevenuePerOrder' => 0,
            'highPriorityCount' => $orders->where('priority', 'high')->count(),
            'overdueCount' => $workloadOrders->where('end_date', '<', now())->count(),
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
            'revenue' => \App\Models\Order::where('status', 'completed')
                ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->sum('actual_revenue') ?? 0,
            'orders' => \App\Models\Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->count(),
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
