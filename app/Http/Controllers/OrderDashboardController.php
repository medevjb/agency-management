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

        // Team leaders and stack leaders only see their own team's data
        $isTeamScoped = $user->hasRole(['team-leader', 'stack-leader']);
        if ($isTeamScoped) {
            $query->where('team_id', $user->team_id);
        }

        // Get all orders (without date filter for stats and lists)
        $orders = $query->get();

        // Separate query for month-filtered revenue calculations (based on delivered_at)
        $dateFilteredQuery = Order::query();
        if ($isTeamScoped) {
            $dateFilteredQuery->where('team_id', $user->team_id);
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

        // Calculate statistics focusing on delivered (revenue) vs workload (active)
        $stats = [
            'totalOrders' => $orders->count(),
            'deliveredOrders' => $orders->where('status', 'completed')->count(),
            'workloadOrders' => $orders->whereIn('status', ['pending', 'in-progress', 'on-hold'])->count(),
            'totalRevenue' => $dateFilteredOrders->where('status', 'completed')->sum('actual_revenue'),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'inProgressOrders' => $orders->where('status', 'in-progress')->count(),
            'onHoldOrders' => $orders->where('status', 'on-hold')->count(),
            'overdueOrders' => $orders->whereNotNull('end_date')->where('end_date', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'averageRevenue' => $dateFilteredOrders->where('status', 'completed')->where('actual_revenue', '>', 0)->avg('actual_revenue') ?? 0,
        ];

        // Orders by status - focusing on delivered vs workload
        $ordersByStatus = [
            'delivered' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'in-progress' => $orders->where('status', 'in-progress')->count(),
            'on-hold' => $orders->where('status', 'on-hold')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Orders by priority
        $ordersByPriority = [
            'high' => $orders->where('priority', 'high')->count(),
            'medium' => $orders->where('priority', 'medium')->count(),
            'low' => $orders->where('priority', 'low')->count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->latest()->take(5)->get();

        // Orders due soon (next 4 days, only workload orders)
        $ordersDueSoon = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(4)->endOfDay())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('end_date')->take(5)->get();

        // Overdue orders (only workload orders)
        $overdueOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('end_date')->take(5)->get();

        // High priority orders (only workload orders)
        $highPriorityOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where('priority', 'high')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()->take(5)->get();

        // Revenue by team
        $revenueByTeamQuery = Order::with('team')->whereNotNull('team_id')->whereHas('team')->where('status', 'completed');
        if ($isTeamScoped) {
            $revenueByTeamQuery->where('team_id', $user->team_id);
        }

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

        // Monthly revenue for the current year
        $monthlyRevenue = Order::selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue')
            ->where('status', 'completed')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('month')
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

        // Monthly delivered status analytics
        $monthlyDelivered = Order::selectRaw('
                MONTH(delivered_at) as month,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status = "completed" THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->where('status', 'completed')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('month')
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

        // Yearly delivered status analytics
        $yearlyDeliveredResults = Order::selectRaw('
                YEAR(delivered_at) as year,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status = "completed" THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->where('status', 'completed')
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', '>=', now()->subYears(5)->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('year')
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
        $period = $request->input('period', 'month');
        $user = auth()->user();

        $query = Order::query()->where('status', 'completed');

        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            $query->where('team_id', $user->team_id);
        }

        if ($period === 'week') {
            $data = $query->selectRaw('DATE(delivered_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('delivered_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($period === 'month') {
            $data = $query->selectRaw('DATE(delivered_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('delivered_at', '>=', now()->startOfMonth())
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $data = $query->selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereYear('delivered_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json($data);
    }
}
