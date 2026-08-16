<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderSalesShare;
use App\Models\Profile;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Profile-wise order analytics: fresh vs. repeat business, revenue, and trend.
 *
 * "Repeat" is a manual decision made when an order is entered (see the order
 * form's Fresh/Repeat control) and stored on the order itself. This class only
 * reads that stored flag; it never infers repeats from client history.
 */
class ProfileAnalytics
{
    private Collection $orders;

    public function __construct(Profile $profile)
    {
        // Reads whatever is already on $profile->orders — eager-loaded by
        // the caller when analysing many profiles at once (index), or
        // lazy-loaded here when analysing just one (show).
        $this->orders = $profile->orders->sortBy('created_at')->values();
    }

    public function totalOrders(): int
    {
        return $this->orders->count();
    }

    public function totalRevenue(): float
    {
        return (float) $this->orders->sum('value');
    }

    public function averageOrderValue(): float
    {
        return $this->totalOrders() > 0
            ? round($this->totalRevenue() / $this->totalOrders(), 2)
            : 0.0;
    }

    /**
     * Orders falling inside the fiscal period labelled by $month — not
     * necessarily the calendar month, if a billing-cycle start day other
     * than the 1st is configured (see GlobalMonth).
     */
    private function ordersForMonth(Carbon $month): Collection
    {
        [$start, $end] = GlobalMonth::periodFor($month);

        return $this->orders->filter(fn (Order $order) => $order->created_at?->between($start, $end));
    }

    public function monthOrderCount(Carbon $month): int
    {
        return $this->ordersForMonth($month)->count();
    }

    public function monthRevenue(Carbon $month): float
    {
        return (float) $this->ordersForMonth($month)->sum('value');
    }

    public function monthAverageOrderValue(Carbon $month): float
    {
        $count = $this->monthOrderCount($month);

        return $count > 0 ? round($this->monthRevenue($month) / $count, 2) : 0.0;
    }

    public function monthRepeatOrderCount(Carbon $month): int
    {
        return $this->ordersForMonth($month)->where('is_repeat', true)->count();
    }

    public function monthNewOrderCount(Carbon $month): int
    {
        return $this->ordersForMonth($month)->where('is_repeat', false)->count();
    }

    public function monthRepeatOrderRate(Carbon $month): float
    {
        $count = $this->monthOrderCount($month);

        return $count > 0 ? round(($this->monthRepeatOrderCount($month) / $count) * 100, 1) : 0.0;
    }

    /**
     * Green (fresh) revenue this month: order value from clients ordering
     * through this profile for the first time.
     */
    public function monthFreshRevenue(Carbon $month): float
    {
        return (float) $this->ordersForMonth($month)->where('is_repeat', false)->sum('value');
    }

    /**
     * Yellow (repeat) revenue this month: order value from clients who had
     * already ordered through this profile before.
     */
    public function monthRepeatRevenue(Carbon $month): float
    {
        return (float) $this->ordersForMonth($month)->where('is_repeat', true)->sum('value');
    }

    /**
     * The month's fresh/repeat split in one shape, for the New vs. Repeat
     * panel: orders and revenue for each side.
     *
     * @return array{fresh: array{orders: int, revenue: float}, repeat: array{orders: int, revenue: float}}
     */
    public function monthFreshRepeatSplit(Carbon $month): array
    {
        $monthOrders = $this->ordersForMonth($month);

        return [
            'fresh' => [
                'orders' => $monthOrders->where('is_repeat', false)->count(),
                'revenue' => (float) $monthOrders->where('is_repeat', false)->sum('value'),
            ],
            'repeat' => [
                'orders' => $monthOrders->where('is_repeat', true)->count(),
                'revenue' => (float) $monthOrders->where('is_repeat', true)->sum('value'),
            ],
        ];
    }

    /**
     * The identity an order belongs to. Repeat orders are attributed to the
     * client they were matched to when entered (repeat_client_key), so a
     * client who renamed still counts as the same repeat client. Fresh orders
     * and repeat orders without a tracked client fall back to their own
     * client_key.
     */
    private function clientKeyFor(Order $order): ?string
    {
        if ($order->is_repeat && filled($order->repeat_client_key)) {
            return $order->repeat_client_key;
        }

        return $order->client_key;
    }

    private function clientGroupsFor(Collection $orders): Collection
    {
        $groups = [];

        foreach ($orders as $order) {
            $key = $this->clientKeyFor($order);

            if (filled($key)) {
                $groups[$key][] = $order;
            }
        }

        return collect($groups)->map(fn (array $members) => collect($members));
    }

    private function clientGroups(): Collection
    {
        return $this->clientGroupsFor($this->orders);
    }

    /**
     * The client identities that carry at least one manually-flagged repeat
     * order in the given set — a client is a repeat client once any of their
     * orders on this profile is flagged repeat.
     *
     * @return Collection<int, string>
     */
    private function repeatClientKeysFor(Collection $orders): Collection
    {
        return $orders
            ->filter(fn (Order $order) => $order->is_repeat)
            ->map(fn (Order $order) => $this->clientKeyFor($order))
            ->filter(fn (?string $key) => filled($key))
            ->unique()
            ->values();
    }

    public function monthUniqueClientCount(Carbon $month): int
    {
        return $this->clientGroupsFor($this->ordersForMonth($month))->count();
    }

    public function monthRepeatClientCount(Carbon $month): int
    {
        return $this->repeatClientKeysFor($this->ordersForMonth($month))->count();
    }

    public function monthNewClientCount(Carbon $month): int
    {
        return $this->monthUniqueClientCount($month) - $this->monthRepeatClientCount($month);
    }

    public function uniqueClientCount(): int
    {
        return $this->clientGroups()->count();
    }

    public function repeatClientCount(): int
    {
        return $this->repeatClientKeysFor($this->orders)->count();
    }

    public function newClientCount(): int
    {
        return $this->uniqueClientCount() - $this->repeatClientCount();
    }

    public function repeatOrderCount(): int
    {
        return $this->orders->where('is_repeat', true)->count();
    }

    public function newOrderCount(): int
    {
        return $this->totalOrders() - $this->repeatOrderCount();
    }

    public function repeatOrderRate(): float
    {
        return $this->totalOrders() > 0
            ? round(($this->repeatOrderCount() / $this->totalOrders()) * 100, 1)
            : 0.0;
    }

    public function repeatClientRate(): float
    {
        return $this->uniqueClientCount() > 0
            ? round(($this->repeatClientCount() / $this->uniqueClientCount()) * 100, 1)
            : 0.0;
    }

    /**
     * @return array<int, array{key: string, name: string, orders: int, total: float, last_order_at: ?Carbon}>
     */
    private function topClientsFor(Collection $orders, int $limit): array
    {
        return $this->clientGroupsFor($orders)
            ->filter(fn (Collection $group) => $group->contains(fn (Order $order) => $order->is_repeat))
            ->map(function (Collection $group, string $key) {
                $latest = $group->sortByDesc('created_at')->first();

                return [
                    'key' => $key,
                    'name' => $latest->name,
                    'orders' => $group->count(),
                    'total' => (float) $group->sum('value'),
                    'last_order_at' => $latest->created_at,
                ];
            })
            ->sortByDesc('orders')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{key: string, name: string, orders: int, total: float, last_order_at: ?Carbon}>
     */
    public function topRepeatClients(int $limit = 5): array
    {
        return $this->topClientsFor($this->orders, $limit);
    }

    /**
     * @return array<int, array{key: string, name: string, orders: int, total: float, last_order_at: ?Carbon}>
     */
    public function monthTopRepeatClients(Carbon $month, int $limit = 5): array
    {
        return $this->topClientsFor($this->ordersForMonth($month), $limit);
    }

    /**
     * @return array<int, array{label: string, orders: int, revenue: float}>
     */
    public function monthlyTrend(int $months = 6): array
    {
        $result = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $bucket = $this->ordersForMonth($cursor);

            $result[] = [
                'label' => $cursor->format('M Y'),
                'orders' => $bucket->count(),
                'revenue' => (float) $bucket->sum('value'),
            ];

            $cursor->addMonth();
        }

        return $result;
    }

    /**
     * @return array<int, array{label: string, new: int, repeat: int}>
     */
    public function monthlyOrderSplit(int $months = 6): array
    {
        $result = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $result[] = [
                'label' => $cursor->format('M Y'),
                'new' => $this->monthNewOrderCount($cursor),
                'repeat' => $this->monthRepeatOrderCount($cursor),
            ];

            $cursor->addMonth();
        }

        return $result;
    }

    /**
     * Fresh (green) vs. repeat (yellow) revenue per month, for stacked
     * charts.
     *
     * @return array<int, array{label: string, fresh: float, repeat: float}>
     */
    public function monthlyRevenueSplit(int $months = 6): array
    {
        $result = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $result[] = [
                'label' => $cursor->format('M Y'),
                'fresh' => $this->monthFreshRevenue($cursor),
                'repeat' => $this->monthRepeatRevenue($cursor),
            ];

            $cursor->addMonth();
        }

        return $result;
    }

    /**
     * @return array<string, int>
     */
    public function statusBreakdown(): array
    {
        return $this->orders->countBy('status')->all();
    }

    /**
     * @return array<string, int>
     */
    public function monthStatusBreakdown(Carbon $month): array
    {
        return $this->ordersForMonth($month)->countBy('status')->all();
    }

    public function recentOrders(int $limit = 8): Collection
    {
        return $this->orders->sortByDesc('created_at')->take($limit)->values();
    }

    public function monthRecentOrders(Carbon $month, int $limit = 8): Collection
    {
        return $this->ordersForMonth($month)->sortByDesc('created_at')->take($limit)->values();
    }

    /**
     * Sales people ranked by their credited sales value on orders sold
     * through this profile this month — the same crediting the Sales
     * Dashboard and Leadership pages use, so the numbers agree everywhere.
     *
     * @return array<int, array{id: int, name: string, amount: float, orders: int}>
     */
    public function monthSalesRanking(Carbon $month, int $limit = 5): array
    {
        return $this->ordersForMonth($month)
            ->flatMap(fn (Order $order) => $order->salesShares)
            ->filter(fn (OrderSalesShare $share) => $share->user !== null)
            ->groupBy('user_id')
            ->map(function (Collection $shares) {
                $seller = $shares->first()->user;

                return [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'amount' => (float) $shares->sum('amount'),
                    'orders' => $shares->pluck('order_id')->unique()->count(),
                ];
            })
            ->sortByDesc('amount')
            ->take($limit)
            ->values()
            ->all();
    }
}
