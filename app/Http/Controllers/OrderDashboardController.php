<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Team;
use App\Support\GlobalMonth;
use Illuminate\Http\Request;

class OrderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (auth()->user()->isSalesLeader()) {
            return redirect()->route('sales.leadership');
        }

        if (auth()->user()->isSales()) {
            return redirect()->route('sales.dashboard');
        }

        $user = auth()->user();

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();
        [$periodStart, $periodEnd] = GlobalMonth::periodFor($monthDate);
        $startDate = $periodStart->format('Y-m-d');
        $endDate = $periodEnd->format('Y-m-d');

        // Team leaders, stack leaders and team members only see their own
        // team's orders here — admin, OM, PM and sales see everything.
        $isTeamScoped = ! $user->isSales() && $user->hasRole(['team-leader', 'team-member', 'stack-leader']);

        // Statuses that stop an order from carrying into the next month's
        // view. 'delivered' is just another active/carrying status now —
        // only 'completed' and 'cancelled' take an order off the board.
        $stopCarryingStatuses = ['completed', 'cancelled'];

        // Carry-forward logic: an order is visible in month M if:
        // - It started on or before the last day of M
        // - AND it did not stop carrying before M began (each stop-carrying
        //   status is gated on its own timestamp: completed_at for
        //   'completed', cancelled_at for 'cancelled')
        $carryForwardFilter = function ($q) use ($startDate, $endDate) {
            $q->where('start_date', '<=', $endDate.' 23:59:59')
                ->where(function ($inner) use ($startDate) {
                    // Still active — never stopped carrying.
                    $inner->whereNotIn('status', ['completed', 'cancelled'])
                        ->orWhere(function ($done) use ($startDate) {
                            $done->where('status', 'completed')
                                ->where(function ($dt) use ($startDate) {
                                    $dt->whereNull('completed_at')
                                        ->orWhere('completed_at', '>=', $startDate.' 00:00:00');
                                });
                        })
                        ->orWhere(function ($done) use ($startDate) {
                            $done->where('status', 'cancelled')
                                ->where(function ($dt) use ($startDate) {
                                    $dt->whereNull('cancelled_at')
                                        ->orWhere('cancelled_at', '>=', $startDate.' 00:00:00');
                                });
                        });
                });
        };

        $baseQuery = Order::with(['team', 'techStack', 'users']);
        if ($isTeamScoped) {
            $baseQuery->where('team_id', $user->team_id);
        }

        $orders = (clone $baseQuery)->where($carryForwardFilter)->get();

        // Revenue: only orders completed during the selected month
        $completedThisMonth = Order::query()
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
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
            'deliveredOrders' => $completedThisMonth->count(),
            'workloadOrders' => $workloadOrders->count(),
            'totalRevenue' => $completedThisMonth->sum('actual_revenue'),
            'workloadValue' => $workloadOrders->sum('value'),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'inProgressOrders' => $orders->where('status', 'in-progress')->count(),
            'onHoldOrders' => $orders->where('status', 'on-hold')->count(),
            'overdueOrders' => $workloadOrders->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->count(),
            'averageRevenue' => $completedThisMonth->where('actual_revenue', '>', 0)->avg('actual_revenue') ?? 0,
        ];

        // Status chart: breakdown of all orders visible this month (carry-forward set).
        // This is a status tally, not revenue — 'delivered' and 'completed'
        // are kept as one visual bucket here, same as before.
        $ordersByStatus = [
            'delivered' => $orders->whereIn('status', ['delivered', 'completed'])->count(),
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
            ->whereNotIn('status', $stopCarryingStatuses)
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // Overdue orders (active only)
        $overdueOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', $stopCarryingStatuses)
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // High priority orders (active only)
        $highPriorityOrders = Order::with(['team', 'techStack'])
            ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
            ->where($carryForwardFilter)
            ->where('priority', 'high')
            ->whereNotIn('status', $stopCarryingStatuses)
            ->latest()
            ->take(5)
            ->get();

        // Revenue by team (completed in selected month)
        $revenueByTeamQuery = Order::with('team')
            ->whereNotNull('team_id')
            ->whereHas('team')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        if ($isTeamScoped) {
            $revenueByTeamQuery->where('team_id', $user->team_id);
        }

        // Revenue per team in one grouped query (SQLite-safe aggregate), with
        // the team names pulled in by eager load.
        $revenueByTeam = (clone $revenueByTeamQuery)
            ->selectRaw('team_id, SUM(actual_revenue) as revenue, COUNT(*) as orders_count')
            ->groupBy('team_id')
            ->get()
            ->map(function ($row) {
                return [
                    'team_name' => $row->team?->name ?? 'Unknown',
                    'revenue' => (float) $row->revenue,
                    'orders_count' => (int) $row->orders_count,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // Monthly + yearly completion analytics. Both read from the same
        // one-year fetch, and both are cached per team scope + month because
        // they only change when an order completes.
        [$monthlyRevenue, $monthlyDeliveredData, $yearlyDelivered] = $this->remember(
            'orders.dashboard.charts.'.$selectedMonth.'.'.$user->team_id,
            function () use ($isTeamScoped, $user) {
                // Note: both queries only fetch status='completed' rows, so
                // "cancelled_count" is structurally always 0 here — that's an
                // existing quirk, preserved as-is rather than changed here.
                $completedThisYear = Order::query()
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->whereYear('completed_at', now()->year)
                    ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
                    ->get(['completed_at', 'actual_revenue', 'status']);

                $monthlyRevenue = [];
                $monthlyDeliveredData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $monthRows = $completedThisYear->filter(fn ($order) => (int) $order->completed_at->format('n') === $i);

                    $monthlyRevenue[$i] = (float) $monthRows->sum('actual_revenue');
                    $monthlyDeliveredData[$i] = [
                        'delivered_count' => $monthRows->count(),
                        'delivered_amount' => (float) $monthRows->sum('actual_revenue'),
                        'cancelled_count' => $monthRows->where('status', 'cancelled')->count(),
                        'total_orders' => $monthRows->count(),
                    ];
                }
                ksort($monthlyRevenue);

                $completedLastFiveYears = Order::query()
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->whereYear('completed_at', '>=', now()->subYears(5)->year)
                    ->when($isTeamScoped, fn ($q) => $q->where('team_id', $user->team_id))
                    ->get(['completed_at', 'actual_revenue', 'status']);

                $yearlyDelivered = $completedLastFiveYears
                    ->groupBy(fn ($order) => (int) $order->completed_at->format('Y'))
                    ->map(fn ($rows, $year) => (object) [
                        'year' => $year,
                        'delivered_count' => $rows->count(),
                        'delivered_amount' => (float) $rows->sum('actual_revenue'),
                        'cancelled_count' => $rows->where('status', 'cancelled')->count(),
                        'total_orders' => $rows->count(),
                    ])
                    ->sortKeys();

                return [$monthlyRevenue, $monthlyDeliveredData, $yearlyDelivered];
            }
        );

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

        $query = Order::query()->where('status', 'completed')->whereNotNull('completed_at');

        if (! $user->isSales() && $user->hasRole(['team-leader', 'team-member', 'stack-leader'])) {
            $query->where('team_id', $user->team_id);
        }

        // Grouped in PHP rather than with DATE()/MONTH(), which only exist on MySQL.
        if ($period === 'week' || $period === 'month') {
            $since = $period === 'week' ? now()->subDays(7) : now()->startOfMonth();

            $data = (clone $query)->where('completed_at', '>=', $since)
                ->get(['completed_at', 'actual_revenue'])
                ->groupBy(fn ($order) => $order->completed_at->format('Y-m-d'))
                ->map(fn ($rows, $date) => (object) [
                    'date' => $date,
                    'revenue' => (float) $rows->sum('actual_revenue'),
                    'orders' => $rows->count(),
                ])
                ->sortKeys()
                ->values();
        } else {
            $data = (clone $query)->whereYear('completed_at', now()->year)
                ->get(['completed_at', 'actual_revenue'])
                ->groupBy(fn ($order) => (int) $order->completed_at->format('n'))
                ->map(fn ($rows, $month) => (object) [
                    'month' => $month,
                    'revenue' => (float) $rows->sum('actual_revenue'),
                    'orders' => $rows->count(),
                ])
                ->sortKeys()
                ->values();
        }

        return response()->json($data);
    }
}
