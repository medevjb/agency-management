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
        // Get date range from request or default to this month
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (! $startDate || ! $endDate) {
            // Default to current month
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        $query = Order::with(['team', 'techStack', 'users']);

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orders = $query->get();

        // Calculate statistics
        $stats = [
            'totalOrders' => $orders->count(),
            'totalRevenue' => $orders->sum('actual_revenue'),
            'activeOrders' => $orders->whereIn('status', ['pending', 'in-progress'])->count(),
            'completedOrders' => $orders->where('status', 'completed')->count(),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'overdueOrders' => $orders->where('end_date', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'averageRevenue' => $orders->where('actual_revenue', '>', 0)->avg('actual_revenue') ?? 0,
        ];

        // Orders by status
        $ordersByStatus = [
            'nra' => $orders->where('status', 'nra')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'in-progress' => $orders->where('status', 'in-progress')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
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
            ->latest()
            ->take(5)
            ->get();

        // Orders due soon (next 7 days)
        $ordersDueSoon = Order::with(['team', 'techStack'])
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // Overdue orders
        $overdueOrders = Order::with(['team', 'techStack'])
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // High priority orders
        $highPriorityOrders = Order::with(['team', 'techStack'])
            ->where('priority', 'high')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->take(5)
            ->get();

        // Revenue by team
        $revenueByTeam = Order::with('team')
            ->whereNotNull('team_id')
            ->whereHas('team')
            ->get()
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
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(actual_revenue) as revenue')
            ->whereYear('created_at', now()->year)
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
            'startDate',
            'endDate'
        ));
    }

    public function getChartData(Request $request)
    {
        $period = $request->input('period', 'month'); // week, month, year

        $query = Order::query();

        if ($period === 'week') {
            $data = Order::selectRaw('DATE(created_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($period === 'month') {
            $data = Order::selectRaw('DATE(created_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->where('created_at', '>=', now()->startOfMonth())
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $data = Order::selectRaw('MONTH(created_at) as month, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json($data);
    }
}
