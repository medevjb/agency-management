<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Get selected month from request or default to current month
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        // Parse the selected month
        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $monthDate->startOfMonth()->format('Y-m-d');
        $endDate = $monthDate->endOfMonth()->format('Y-m-d');

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        $query = Order::with(['team', 'techStack', 'users']);

        // Filter by user's role and team
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                // Stack leaders see orders that use their tech stack
                $query->where('tech_stack_id', $user->tech_stack_id);
            } else {
                // Team leaders and members see orders from their team
                $query->where('team_id', $user->team_id);
            }
        }

        // Get all orders (without date filter for stats and lists)
        $orders = $query->get();

        // Separate query for month-filtered revenue calculations (based on delivered_at)
        $dateFilteredQuery = Order::query();

        // Apply the same team filtering
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $dateFilteredQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $dateFilteredQuery->where('team_id', $user->team_id);
            }
        }

        // Apply month filter based on delivered_at for revenue calculations
        $dateFilteredQuery->whereBetween('delivered_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        $dateFilteredOrders = $dateFilteredQuery->get();

        // Get team information for team users
        $team = null;
        $teamMembers = collect();
        if ($user->team_id && ! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $team = \App\Models\Team::with('members', 'teamLeader')->find($user->team_id);
            $teamMembers = $team ? $team->members : collect();
        }

        // Calculate statistics (use date-filtered for revenue, all orders for counts)
        $stats = [
            'totalOrders' => $orders->count(),
            'totalRevenue' => $dateFilteredOrders->where('status', 'delivered')->sum('actual_revenue'),
            'activeOrders' => $orders->whereIn('status', ['pending', 'in-progress'])->count(),
            'completedOrders' => $orders->where('status', 'delivered')->count(),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'overdueOrders' => $orders->whereNotNull('end_date')->where('end_date', '<', now())->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'averageRevenue' => $dateFilteredOrders->where('status', 'delivered')->where('actual_revenue', '>', 0)->avg('actual_revenue') ?? 0,
        ];

        // Orders by status
        $ordersByStatus = [
            'nra' => $orders->where('status', 'nra')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'in-progress' => $orders->where('status', 'in-progress')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'on-hold' => $orders->where('status', 'on-hold')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Orders by priority
        $ordersByPriority = [
            'high' => $orders->where('priority', 'high')->count(),
            'medium' => $orders->where('priority', 'medium')->count(),
            'low' => $orders->where('priority', 'low')->count(),
        ];

        // Recent orders (filtered by team for non-managers)
        $recentOrdersQuery = Order::with(['team', 'techStack']);
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $recentOrdersQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $recentOrdersQuery->where('team_id', $user->team_id);
            }
        }
        $recentOrders = $recentOrdersQuery->latest()->take(5)->get();

        // Orders due soon (next 4 days, filtered by team for non-managers)
        $ordersDueSoonQuery = Order::with(['team', 'techStack'])
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(4)->endOfDay())
            ->whereNotIn('status', ['delivered', 'cancelled']);
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $ordersDueSoonQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $ordersDueSoonQuery->where('team_id', $user->team_id);
            }
        }
        $ordersDueSoon = $ordersDueSoonQuery->orderBy('end_date')->take(5)->get();

        // Overdue orders (filtered by team for non-managers)
        $overdueOrdersQuery = Order::with(['team', 'techStack'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['delivered', 'cancelled']);
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $overdueOrdersQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $overdueOrdersQuery->where('team_id', $user->team_id);
            }
        }
        $overdueOrders = $overdueOrdersQuery->orderBy('end_date')->take(5)->get();

        // High priority orders (filtered by team for non-managers)
        $highPriorityOrdersQuery = Order::with(['team', 'techStack'])
            ->where('priority', 'high')
            ->whereNotIn('status', ['delivered', 'cancelled']);
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $highPriorityOrdersQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $highPriorityOrdersQuery->where('team_id', $user->team_id);
            }
        }
        $highPriorityOrders = $highPriorityOrdersQuery->latest()->take(5)->get();

        // Revenue by team (use date-filtered orders, only completed)
        $revenueByTeamQuery = Order::with('team')->whereNotNull('team_id')->whereHas('team')->where('status', 'delivered');

        // Apply team filtering for non-managers
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $revenueByTeamQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $revenueByTeamQuery->where('team_id', $user->team_id);
            }
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $revenueByTeamQuery->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        }

        $revenueByTeam = $revenueByTeamQuery->get()
            ->groupBy('team_id')
            ->map(function ($group) {
                return [
                    'team_name' => $group->first()->team->name,
                    'revenue' => $group->sum('actual_revenue'),
                    'orders_count' => $group->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // Monthly revenue for the current year (filtered by team for non-managers, only delivered orders)
        $monthlyRevenueQuery = Order::selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue')
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year);

        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $monthlyRevenueQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $monthlyRevenueQuery->where('team_id', $user->team_id);
            }
        }

        $monthlyRevenue = $monthlyRevenueQuery->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Fill in missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (! isset($monthlyRevenue[$i])) {
                $monthlyRevenue[$i] = 0;
            }
        }
        ksort($monthlyRevenue);

        // Monthly delivered status analytics (counts and amounts) - for current year
        $monthlyDeliveredQuery = Order::selectRaw('
                MONTH(delivered_at) as month,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status = "delivered" THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year);

        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $monthlyDeliveredQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $monthlyDeliveredQuery->where('team_id', $user->team_id);
            }
        }

        $monthlyDelivered = $monthlyDeliveredQuery->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill in missing months with 0
        $monthlyDeliveredData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyDeliveredData[$i] = [
                'delivered_count' => $monthlyDelivered->get($i)?->delivered_count ?? 0,
                'delivered_amount' => $monthlyDelivered->get($i)?->delivered_amount ?? 0,
                'cancelled_count' => $monthlyDelivered->get($i)?->cancelled_count ?? 0,
                'total_orders' => $monthlyDelivered->get($i)?->total_orders ?? 0,
            ];
        }

        // Yearly delivered status analytics (last 5 years + current year)
        $yearlyDeliveredQuery = Order::selectRaw('
                YEAR(delivered_at) as year,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status = "delivered" THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', '>=', now()->subYears(5)->year);

        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                $yearlyDeliveredQuery->where('tech_stack_id', $user->tech_stack_id);
            } else {
                $yearlyDeliveredQuery->where('team_id', $user->team_id);
            }
        }

        $yearlyDeliveredResults = $yearlyDeliveredQuery->groupBy('year')
            ->orderBy('year')
            ->get();

        // Convert to a collection keyed by year
        $yearlyDelivered = collect();
        foreach ($yearlyDeliveredResults as $result) {
            $yearlyDelivered->put($result->year, $result);
        }

        return view('orders.dashboard', compact(
            'stats',
            'ordersByStatus',
            'ordersByPriority',
            'recentOrders',
            'ordersDueSoon',
            'overdueOrders',
            'highPriorityOrders',
            'revenueByTeam',
            'monthlyRevenue',
            'monthlyDeliveredData',
            'yearlyDelivered',
            'startDate',
            'endDate',
            'selectedMonth',
            'availableMonths',
            'team',
            'teamMembers'
        ));
    }

    public function getChartData(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'month'); // week, month, year

        $query = Order::query()->where('status', 'delivered');

        // Filter by user's role and team
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('stack-leader')) {
                // Stack leaders see orders that use their tech stack
                $query->where('tech_stack_id', $user->tech_stack_id);
            } else {
                // Team leaders and members see orders from their team
                $query->where('team_id', $user->team_id);
            }
        }

        if ($period === 'week') {
            $data = $query->selectRaw('DATE(created_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($period === 'month') {
            $data = $query->selectRaw('DATE(created_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('created_at', '>=', now()->startOfMonth())
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $data = $query->selectRaw('MONTH(created_at) as month, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json($data);
    }
}
