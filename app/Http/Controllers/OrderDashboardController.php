<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (auth()->user()->isSales()) {
            return redirect()->route('sales.dashboard');
        }

        $user = auth()->user();

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        $isTeamScoped = $user->hasRole(['team-leader', 'stack-leader']);

        // Finished statuses — 'delivered' auto-sets delivered_at via model observer,
        // 'completed' is set via bulk action and may not have delivered_at.
        $finishedStatuses = ['delivered', 'completed'];

        // Carry-forward logic: an order is visible in month M if:
        // - It started on or before the last day of M
        // - AND it was not finished before M began
        //   (active orders have no delivered_at; finished orders must have delivered_at >= startDate
        //    or be 'completed' without delivered_at, which we include everywhere)
        $carryForwardFilter = function ($q) use ($startDate, $endDate, $finishedStatuses) {
            $q->where('start_date', '<=', $endDate.' 23:59:59')
                ->where(function ($inner) use ($startDate, $finishedStatuses) {
                    // Active orders (not delivered/completed/cancelled)
                    $inner->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
                        // OR delivered/completed with delivered_at in or after this month
                        ->orWhere(function ($done) use ($startDate, $finishedStatuses) {
                            $done->whereIn('status', $finishedStatuses)
                                ->where(function ($dt) use ($startDate) {
                                    $dt->whereNull('delivered_at')
                                        ->orWhere('delivered_at', '>=', $startDate.' 00:00:00');
                                });
                        });
                });
        };

        $baseQuery = Order::with(['team', 'techStack', 'users']);
        if ($isTeamScoped) {
            $baseQuery->where('team_id', $user->team_id);
        }

        $orders = (clone $baseQuery)->where($carryForwardFilter)->get();

        // Revenue: only orders delivered/completed during the selected month
        $deliveredThisMonth = Order::query()
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->get();

        // Get team information for team users
        $team = null;
        $teamMembers = collect();
        if ($user->team_id && ! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $team = Team::with('members', 'teamLeader')->find($user->team_id);
            $teamMembers = $team ? $team->members : collect();
        }

        $workloadOrders = $orders->whereIn('status', ['pending', 'in-progress', 'on-hold']);

        $stats = [
            'totalOrders' => $orders->count(),
            'deliveredOrders' => $deliveredThisMonth->count(),
            'workloadOrders' => $workloadOrders->count(),
            'totalRevenue' => $deliveredThisMonth->sum('actual_revenue'),
            'workloadValue' => $workloadOrders->sum('value'),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'inProgressOrders' => $orders->where('status', 'in-progress')->count(),
            'onHoldOrders' => $orders->where('status', 'on-hold')->count(),
            'overdueOrders' => $workloadOrders->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->count(),
            'averageRevenue' => $deliveredThisMonth->where('actual_revenue', '>', 0)->avg('actual_revenue') ?? 0,
        ];

        // Status chart: breakdown of all orders visible this month (carry-forward set)
        $ordersByStatus = [
            'delivered' => $orders->whereIn('status', $finishedStatuses)->count(),
            'pending' => $stats['pendingOrders'],
            'in-progress' => $stats['inProgressOrders'],
            'on-hold' => $stats['onHoldOrders'],
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        $ordersByPriority = [
            'high' => $orders->where('priority', 'high')->count(),
            'medium' => $orders->where('priority', 'medium')->count(),
            'low' => $orders->where('priority', 'low')->count(),
        ];

        // Recent orders (carry-forward for selected month)
        $recentOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->latest('start_date')
            ->take(5)
            ->get();

        // Orders due soon (next 4 days, active only)
        $ordersDueSoon = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(4)->endOfDay())
            ->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // Overdue orders (active only)
        $overdueOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // High priority orders (active only)
        $highPriorityOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->where('priority', 'high')
            ->whereNotIn('status', array_merge($finishedStatuses, ['cancelled']))
            ->latest()
            ->take(5)
            ->get();

        // Revenue by team (delivered in selected month)
        $revenueByTeamQuery = Order::with('team')
            ->whereNotNull('team_id')
            ->whereHas('team')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        if ($isTeamScoped) {
            $revenueByTeamQuery->where('team_id', $user->team_id);
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

        // Monthly revenue for the current year (by delivered_at)
        $monthlyRevenue = Order::selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            if (! isset($monthlyRevenue[$i])) {
                $monthlyRevenue[$i] = 0;
            }
        }
        ksort($monthlyRevenue);

        // Monthly delivery analytics
        $monthlyDelivered = Order::selectRaw('
                MONTH(delivered_at) as month,
                SUM(CASE WHEN status IN ("delivered", "completed") THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status IN ("delivered", "completed") THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyDeliveredData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyDeliveredData[$i] = [
                'delivered_count' => $monthlyDelivered->get($i)?->delivered_count ?? 0,
                'delivered_amount' => $monthlyDelivered->get($i)?->delivered_amount ?? 0,
                'cancelled_count' => $monthlyDelivered->get($i)?->cancelled_count ?? 0,
                'total_orders' => $monthlyDelivered->get($i)?->total_orders ?? 0,
            ];
        }

        // Yearly delivery analytics
        $yearlyDeliveredResults = Order::selectRaw('
                YEAR(delivered_at) as year,
                SUM(CASE WHEN status IN ("delivered", "completed") THEN 1 ELSE 0 END) as delivered_count,
                SUM(CASE WHEN status IN ("delivered", "completed") THEN actual_revenue ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_orders
            ')
            ->whereIn('status', $finishedStatuses)
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', '>=', now()->subYears(5)->year)
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->groupBy('year')
            ->orderBy('year')
            ->get();

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

        $query = Order::query()->whereIn('status', ['delivered', 'completed']);

        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            $query->where('team_id', $user->team_id);
        }

        if ($period === 'week') {
            $data = $query->selectRaw('DATE(delivered_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereNotNull('delivered_at')
                ->where('delivered_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($period === 'month') {
            $data = $query->selectRaw('DATE(delivered_at) as date, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereNotNull('delivered_at')
                ->where('delivered_at', '>=', now()->startOfMonth())
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $data = $query->selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue, COUNT(*) as orders')
                ->whereNotNull('delivered_at')
                ->whereYear('delivered_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json($data);
    }
}
