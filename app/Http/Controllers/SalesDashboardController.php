<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalesTarget;
use App\Models\Setting;
use App\Models\User;
use App\Services\SalesBonusService;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SalesDashboardController extends Controller
{
    /**
     * An admin may state a company-wide gross target here; when unset it is
     * derived from the sum of every salesperson's own target.
     */
    public const COMPANY_TARGET_SETTING = 'company_gross_target';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();

        abort_unless($user->isSales() || $user->hasRole('admin'), 403);

        $isAdmin = $user->hasRole('admin');
        $monthDate = GlobalMonth::current();

        // A member reads their own sales; a leader or admin reads the whole
        // unit's, matching how this page has always split the two. Bonus is
        // always personal — it is an individual entitlement either way.
        $sellerIds = ($user->isSalesLeader() || $isAdmin)
            ? $this->groupIds($user, $isAdmin)
            : [$user->id];

        return view('sales.dashboard', [
            'dashboard' => $this->payload($user, $sellerIds, $monthDate),
            'selectedMonth' => GlobalMonth::key(),
            'availableMonths' => GlobalMonth::available(),
            'monthDate' => $monthDate,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * A sample rendering of the sales-member dashboard, for admin/OM/PM to
     * see the layout without needing a real sales member's account. Every
     * figure here is fabricated — nothing is read from the database.
     */
    public function memberPreview(): View
    {
        abort_unless(auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']), 403);

        $monthDate = GlobalMonth::current();

        return view('sales.dashboard', [
            'dashboard' => $this->samplePayload($monthDate),
            'selectedMonth' => GlobalMonth::key(),
            'availableMonths' => GlobalMonth::available(),
            'monthDate' => $monthDate,
            'isAdmin' => false,
            'isPreview' => true,
        ]);
    }

    /**
     * Everything the dashboard renders, assembled in one place so the view
     * reads values and computes nothing.
     *
     * @param  array<int, int>  $sellerIds
     * @return array<string, mixed>
     */
    protected function payload(User $user, array $sellerIds, Carbon $monthDate): array
    {
        // The service is the authority on the configured rate, exchange rate
        // and status. The money figures are re-derived on the same seller
        // basis the rest of the page uses, so quarter totals can never
        // disagree with the months they are made of.
        $bonus = app(SalesBonusService::class)->calculate($user, $monthDate);
        $rate = (float) $bonus['bonus_percentage'];
        $exchangeRate = (float) $bonus['exchange_rate'];

        $quarterMonths = $this->quarterMonths($monthDate);
        $months = $this->monthsFor($sellerIds, $quarterMonths, $monthDate);
        $quarter = $this->quarterTotals($months, $rate, $exchangeRate);

        $sales = $this->creditFor($sellerIds, $monthDate);
        $target = $this->targetFor($sellerIds, $monthDate);

        return [
            'period' => [
                'month' => $monthDate->format('F'),
                'year' => $monthDate->year,
                'quarter' => 'Q'.(int) ceil($monthDate->month / 3),
                'quarterMonths' => $quarterMonths->first()->format('F').'–'.$quarterMonths->last()->format('F'),
            ],
            'currentMonth' => [
                'sales' => $sales,
                'target' => $target,
                'achievement' => $this->percentage($sales, $target),
                'extraSales' => max(0.0, $sales - $target),
                'achieved' => $target > 0 && $sales >= $target,
                'unlockNeeded' => max(0.0, $target - $sales),
            ],
            'bonus' => [
                'qualifiedExtra' => $quarter['qualifiedExtra'],
                // Stored as a fraction (0.01 = 1%); never shown raw.
                'rate' => $rate * 100,
                'bonusUsd' => $quarter['bonusUsd'],
                'exchangeRate' => $exchangeRate,
                'bonusBdt' => $quarter['bonusBdt'],
                'status' => strtolower((string) $bonus['status']) === 'accruing' ? 'accruing' : 'paid',
                'configured' => (bool) $bonus['configuration_available'],
                'motivationUsd' => 1000.0,
                'motivationBdt' => 1000.0 * $rate * $exchangeRate,
            ],
            'months' => $months,
            'quarter' => [
                'sales' => $quarter['sales'],
                'target' => $quarter['target'],
                'achievement' => $this->percentage($quarter['sales'], $quarter['target']),
                'qualifiedExtra' => $quarter['qualifiedExtra'],
                'bonusBdt' => $quarter['bonusBdt'],
            ],
            'history' => $this->historyFor($user, $sellerIds, $monthDate),
            'company' => $this->companyFor($sales, $monthDate),
        ];
    }

    /**
     * The three calendar months of the quarter the given month falls in.
     *
     * @return Collection<int, Carbon>
     */
    protected function quarterMonths(Carbon $monthDate): Collection
    {
        $quarter = (int) ceil($monthDate->month / 3);

        return collect(range(($quarter - 1) * 3 + 1, $quarter * 3))
            ->map(fn (int $month) => Carbon::create($monthDate->year, $month, 1));
    }

    /**
     * Sales credit these sellers hold in the billing cycle labelled by the
     * given month. Uses GlobalMonth::periodFor so the range matches what the
     * bonus service counts — a cycle need not be a calendar month.
     *
     * @param  array<int, int>  $sellerIds
     */
    protected function creditFor(array $sellerIds, Carbon $monthDate): float
    {
        if ($sellerIds === []) {
            return 0.0;
        }

        [$start, $end] = GlobalMonth::periodFor($monthDate);

        return (float) Order::query()
            ->whereHas('salesShares', fn ($share) => $share->whereIn('user_id', $sellerIds))
            ->whereBetween('created_at', [$start, $end])
            ->with(['salesShares' => fn ($share) => $share->whereIn('user_id', $sellerIds)])
            ->get()
            ->sum(fn (Order $order) => (float) $order->salesShares->sum('amount'));
    }

    /**
     * @param  array<int, int>  $sellerIds
     */
    protected function targetFor(array $sellerIds, Carbon $monthDate): float
    {
        return (float) SalesTarget::amountsFor($sellerIds, $monthDate)->sum();
    }

    /**
     * The quarter's months, each with its own qualification outcome. Extra
     * sales is clamped at zero — a month below target contributes nothing
     * rather than a negative amount.
     *
     * @param  array<int, int>  $sellerIds
     * @param  Collection<int, Carbon>  $quarterMonths
     * @return array<int, array<string, mixed>>
     */
    protected function monthsFor(array $sellerIds, Collection $quarterMonths, Carbon $selected): array
    {
        return $quarterMonths->map(function (Carbon $month) use ($sellerIds, $selected) {
            $isFuture = $month->copy()->startOfMonth()->gt(now()->startOfMonth());
            $sales = $isFuture ? 0.0 : $this->creditFor($sellerIds, $month);
            $target = $this->targetFor($sellerIds, $month);
            $achieved = $target > 0 && $sales >= $target;

            return [
                'name' => $month->format('F'),
                'sales' => $sales,
                'target' => $target,
                'achievement' => $this->percentage($sales, $target),
                // A month with no target recorded qualifies nothing — there is
                // no bar to have cleared.
                'extra' => $target > 0 ? max(0.0, $sales - $target) : 0.0,
                'remaining' => max(0.0, $target - $sales),
                'status' => match (true) {
                    $isFuture => 'upcoming',
                    $achieved && $month->isSameMonth($selected) => 'bonus-active',
                    $achieved => 'achieved',
                    default => 'in-progress',
                },
            ];
        })->all();
    }

    /**
     * A quarter's totals, summed from the months it is made of so the two can
     * never disagree. Bonus is the quarter's qualified extra taken once at the
     * configured rate, matching how SalesBonusService derives it.
     *
     * @param  array<int, array<string, mixed>>  $months
     * @return array<string, float>
     */
    protected function quarterTotals(array $months, float $rate, float $exchangeRate): array
    {
        $collection = collect($months);
        $qualifiedExtra = (float) $collection->sum('extra');
        $bonusUsd = $qualifiedExtra * $rate;

        return [
            'sales' => (float) $collection->sum('sales'),
            'target' => (float) $collection->sum('target'),
            'qualifiedExtra' => $qualifiedExtra,
            'bonusUsd' => $bonusUsd,
            'bonusBdt' => round($bonusUsd * $exchangeRate, 2),
        ];
    }

    /**
     * The trailing four quarters with their bonus outcomes. Computed rather
     * than read from finalized snapshots, so history is populated before any
     * finalize workflow exists; SalesBonusService already prefers a snapshot
     * where one has been written.
     *
     * @param  array<int, int>  $sellerIds
     * @return array<string, mixed>
     */
    protected function historyFor(User $user, array $sellerIds, Carbon $monthDate): array
    {
        $service = app(SalesBonusService::class);
        $earned = 0.0;

        $quarters = collect(range(3, 0))->map(function (int $back) use ($user, $sellerIds, $monthDate, $service, &$earned) {
            $anchor = $monthDate->copy()->subMonths($back * 3);
            $bonus = $service->calculate($user, $anchor);

            $months = $this->monthsFor($sellerIds, $this->quarterMonths($anchor), $anchor);
            $totals = $this->quarterTotals($months, (float) $bonus['bonus_percentage'], (float) $bonus['exchange_rate']);
            $earned += $totals['bonusBdt'];

            return [
                'label' => 'Q'.(int) ceil($anchor->month / 3)." '".$anchor->format('y'),
                'payoutBdt' => $totals['bonusBdt'],
                'status' => $anchor->isSameQuarter($monthDate) ? 'accruing' : 'paid',
                'months' => collect($months)->map(fn (array $month) => [
                    'name' => substr($month['name'], 0, 3),
                    'sales' => $month['sales'],
                    'target' => $month['target'],
                ])->all(),
            ];
        })->all();

        return ['earnedToDate' => $earned, 'quarters' => $quarters];
    }

    /**
     * Company-wide figures for the period. The gross target is an admin
     * override when set, and otherwise the sum of every salesperson's own
     * target — so the number is never simply absent.
     *
     * @return array<string, mixed>
     */
    protected function companyFor(float $mySales, Carbon $monthDate): array
    {
        [$start, $end] = GlobalMonth::periodFor($monthDate);

        $gross = (float) Order::query()->whereBetween('created_at', [$start, $end])->sum('value');

        $override = (float) Setting::get(self::COMPANY_TARGET_SETTING, 0);
        $target = $override > 0
            ? $override
            : $this->targetFor(User::withRoleName(['sales-leader', 'sales-member'])->pluck('id')->all(), $monthDate);

        return [
            'grossSales' => $gross,
            'target' => $target,
            'achievement' => $this->percentage($gross, $target),
            'contribution' => $this->percentage($mySales, $gross),
        ];
    }

    /**
     * Null rather than zero when there is nothing to divide by, so "no target
     * recorded" never renders as "achieved nothing".
     */
    protected function percentage(float $value, float $of): ?float
    {
        return $of > 0 ? round($value / $of * 100, 1) : null;
    }

    /**
     * Every sales person whose numbers roll up into this person's view.
     *
     * @return array<int, int>
     */
    protected function groupIds(User $user, bool $isAdmin): array
    {
        if ($isAdmin) {
            return User::withRoleName(['sales-leader', 'sales-member'])->pluck('id')->all();
        }

        if ($user->isSalesLeader()) {
            return $user->salesVisibleUserIds();
        }

        return [$user->id];
    }

    /**
     * The same payload shape as payload(), built from fabricated figures so
     * the preview cannot drift from the real page.
     *
     * @return array<string, mixed>
     */
    protected function samplePayload(Carbon $monthDate): array
    {
        $quarterMonths = $this->quarterMonths($monthDate);
        $sample = [125000.0, 128500.0, 100000.0];

        $months = $quarterMonths->values()->map(function (Carbon $month, int $index) use ($sample, $monthDate) {
            $sales = $sample[$index];
            $target = 120000.0;
            $achieved = $sales >= $target;

            return [
                'name' => $month->format('F'),
                'sales' => $sales,
                'target' => $target,
                'achievement' => $this->percentage($sales, $target),
                'extra' => max(0.0, $sales - $target),
                'remaining' => max(0.0, $target - $sales),
                'status' => match (true) {
                    $achieved && $month->isSameMonth($monthDate) => 'bonus-active',
                    $achieved => 'achieved',
                    default => 'in-progress',
                },
            ];
        })->all();

        return [
            'period' => [
                'month' => $monthDate->format('F'),
                'year' => $monthDate->year,
                'quarter' => 'Q'.(int) ceil($monthDate->month / 3),
                'quarterMonths' => $quarterMonths->first()->format('F').'–'.$quarterMonths->last()->format('F'),
            ],
            'currentMonth' => [
                'sales' => 128500.0,
                'target' => 120000.0,
                'achievement' => 107.1,
                'extraSales' => 8500.0,
                'achieved' => true,
                'unlockNeeded' => 0.0,
            ],
            'bonus' => [
                'qualifiedExtra' => 13500.0,
                'rate' => 1.0,
                'bonusUsd' => 135.0,
                'exchangeRate' => 122.50,
                'bonusBdt' => 16537.50,
                'status' => 'accruing',
                'configured' => true,
                'motivationUsd' => 1000.0,
                'motivationBdt' => 1225.0,
            ],
            'months' => $months,
            'quarter' => [
                'sales' => 353500.0,
                'target' => 360000.0,
                'achievement' => 98.2,
                'qualifiedExtra' => 13500.0,
                'bonusBdt' => 16537.50,
            ],
            'history' => [
                'earnedToDate' => 63837.50,
                'quarters' => collect(range(3, 0))->map(function (int $back) use ($monthDate) {
                    $anchor = $monthDate->copy()->subMonths($back * 3);
                    $payouts = [6125.0, 12250.0, 28925.0, 16537.50];

                    return [
                        'label' => 'Q'.(int) ceil($anchor->month / 3)." '".$anchor->format('y'),
                        'payoutBdt' => $payouts[3 - $back],
                        'status' => $back === 0 ? 'accruing' : 'paid',
                        'months' => $this->quarterMonths($anchor)->map(fn (Carbon $month) => [
                            'name' => $month->format('M'),
                            'sales' => 100000.0 + ($month->month * 2500),
                            'target' => 120000.0,
                        ])->all(),
                    ];
                })->all(),
            ],
            'company' => [
                'grossSales' => 4820000.0,
                'target' => 5000000.0,
                'achievement' => 96.4,
                'contribution' => 2.7,
            ],
        ];
    }
}
