<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\OrderValueDistribution;
use App\Models\SalesTarget;
use App\Models\User;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ValueDistributionController extends Controller
{
    /**
     * Roles allowed anywhere on this page. Team leaders are scoped to their
     * own team's orders inside index()/authorizeOrder(); everyone else sees
     * every completed order.
     */
    private const ALLOWED_ROLES = ['admin', 'operation-manager', 'project-manager', 'team-leader'];

    /**
     * Orders per page, for both the pending and the settled list. Chosen so
     * neither list ever renders more distribution cards — each with its own
     * inputs and listeners — than a screen can comfortably hold, regardless
     * of how many orders complete in a given month.
     */
    private const PER_PAGE = 10;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->hasRole(self::ALLOWED_ROLES), 403);

        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();
        [$monthStart, $monthEnd] = GlobalMonth::periodFor($monthDate);

        $scope = $this->monthScope($user, $monthStart, $monthEnd);

        // Pending vs. settled is a plain column check — value_distributed_at
        // is stamped (and cleared) the moment a distribution reaches full
        // allocation, so neither list needs to load every order for the
        // month just to sort them into a bucket in PHP.
        $pendingOrders = Order::query()
            ->where($scope)
            ->whereNull('value_distributed_at')
            ->when($request->filled('search'), fn (Builder $q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->with(['team', 'freelanceProfile', 'users', 'valueDistributions.user'])
            ->orderByDesc('completed_at')
            ->paginate(self::PER_PAGE, ['*'], 'pending_page')
            ->withQueryString();

        $distributedOrders = Order::query()
            ->where($scope)
            ->whereNotNull('value_distributed_at')
            ->with(['team', 'freelanceProfile', 'users', 'valueDistributions.user'])
            ->orderByDesc('completed_at')
            ->paginate(self::PER_PAGE, ['*'], 'distributed_page')
            ->withQueryString();

        // Counts reflect the whole month regardless of the search box above
        // or which page is showing — cheap COUNT queries, no eager loads.
        $pendingCount = Order::query()->where($scope)->whereNull('value_distributed_at')->count();
        $distributedCount = Order::query()->where($scope)->whereNotNull('value_distributed_at')->count();

        // Distributed dollars: a straight SQL sum over stored share amounts,
        // independent of order volume — no per-order fee math needed since
        // a share's amount is already the actual dollar figure paid out.
        $distributedTotal = (float) OrderValueDistribution::whereHas('order', $scope)->sum('amount');

        // Open dollars still needs each pending order's net value, which
        // depends on its freelance profile's platform fee — not a plain
        // column. Pull only the handful of columns that requires, with the
        // credited-so-far sum done in the same query instead of loading
        // every pending order's full distribution list to add it up.
        $openTotal = (float) Order::query()
            ->where($scope)
            ->whereNull('value_distributed_at')
            ->select(['id', 'value', 'freelance_profile_id'])
            ->withSum('valueDistributions as credited_sum', 'amount')
            ->get()
            ->sum(fn (Order $order) => max(0, $order->netValue() - (float) $order->credited_sum));

        // Achievement board: one aggregate query over the shares table for
        // the whole month — this never grows with how many orders exist,
        // only with how many distinct people were credited.
        $leaderboardRows = OrderValueDistribution::query()
            ->whereHas('order', $scope)
            ->selectRaw('user_id, SUM(amount) as total, COUNT(DISTINCT order_id) as orders_count')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('total')
            ->get();

        $targets = SalesTarget::amountsFor($leaderboardRows->pluck('user_id')->all(), $monthDate);

        $leaderboard = $leaderboardRows->map(function ($row) use ($targets) {
            $amount = (float) $row->total;
            $target = (float) ($targets[$row->user_id] ?? 0);

            return [
                'id' => $row->user_id,
                'name' => $row->user?->name ?? 'Removed user',
                'amount' => $amount,
                'orders' => (int) $row->orders_count,
                'target' => $target,
                'achievement' => $target > 0 ? (int) round($amount / $target * 100) : null,
            ];
        })->values();

        return view('value-distribution.index', compact(
            'pendingOrders', 'distributedOrders', 'pendingCount', 'distributedCount',
            'distributedTotal', 'openTotal', 'leaderboard',
            'selectedMonth', 'availableMonths', 'monthDate'
        ));
    }

    /**
     * Save the distribution split for one order.
     */
    public function store(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'shares' => 'required|array|min:1',
            'shares.*.user_id' => 'required|exists:users,id',
            'shares.*.amount' => 'required|numeric|min:0',
        ]);

        $net = $order->netValue();
        $total = round(array_sum(array_map(fn ($share) => (float) $share['amount'], $validated['shares'])), 2);

        if ($total > $net + 0.01) {
            return response()->json([
                'message' => 'That adds up to $'.number_format($total, 2).', more than the net value of $'.number_format($net, 2).'.',
            ], 422);
        }

        $before = $this->describe($order);

        $order->valueDistributions()->delete();

        $userIds = [];
        foreach ($validated['shares'] as $share) {
            $userId = (int) $share['user_id'];
            $amount = round((float) $share['amount'], 2);

            if ($amount <= 0) {
                continue;
            }

            $order->valueDistributions()->create([
                'user_id' => $userId,
                'amount' => $amount,
                'distributed_by' => auth()->id(),
            ]);
            $userIds[] = $userId;
        }

        $order->load('valueDistributions.user');
        $isComplete = $order->isValueFullyDistributed();

        $order->forceFill(['value_distributed_at' => $isComplete ? now() : null])->save();

        $after = $this->describe($order);
        if ($before !== $after) {
            OrderAudit::record($order, 'value_distributed', ['value_distribution' => $before], ['value_distribution' => $after]);
        }

        return response()->json([
            'message' => $isComplete ? 'Distribution saved — fully allocated.' : 'Distribution saved, but not yet fully allocated.',
            'is_complete' => $isComplete,
            'undistributed' => $order->undistributedValue(),
        ]);
    }

    protected function authorizeOrder(Order $order): void
    {
        $user = auth()->user();

        abort_unless($user->hasRole(self::ALLOWED_ROLES), 403);

        if ($user->hasRole('team-leader') && ! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            abort_unless($order->team_id === $user->team_id, 403);
        }

        abort_unless($order->status === 'completed', 422, 'Only completed orders can be distributed.');
    }

    /**
     * The single definition of "counts toward this page, this month" —
     * shared by every query above (direct Order queries and whereHas('order',
     * ...) ones alike) so there is exactly one place that logic can drift.
     */
    protected function monthScope(User $user, Carbon $monthStart, Carbon $monthEnd): Closure
    {
        return function (Builder $query) use ($user, $monthStart, $monthEnd) {
            $query->where('status', 'completed')
                ->whereBetween('completed_at', [$monthStart, $monthEnd]);

            if ($user->hasRole('team-leader') && ! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
                $query->where('team_id', $user->team_id);
            }
        };
    }

    /**
     * @return array<int, string>
     */
    protected function describe(Order $order): array
    {
        return $order->valueDistributions
            ->sortByDesc('amount')
            ->map(fn ($share) => ($share->user?->name ?? 'Unknown').': $'.number_format((float) $share->amount, 2))
            ->values()
            ->all();
    }
}
