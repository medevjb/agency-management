<?php

namespace App\Support;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * The single month every dashboard reads from. Set once from the header
 * dropdown (via SelectedMonthController), it sticks in the session until
 * changed again — no page carries its own independent month filter.
 *
 * The month a person picks (and every label built from it) is still a plain
 * calendar month — "August" means August. But the actual date range that
 * counts as "August" is configurable in Admin Settings, to match a billing
 * cycle that doesn't start on the 1st (e.g. the 26th of the prior month
 * through the 25th). Use current()/key()/label() for the selector and
 * display; use periodStart()/periodEnd() for the real date range to filter
 * orders, revenue, etc. against.
 */
class GlobalMonth
{
    public const SESSION_KEY = 'selected_month';

    public const CYCLE_START_DAY_SETTING = 'month_cycle_start_day';

    /**
     * The month currently in effect, as the first moment of that month.
     */
    public static function current(): Carbon
    {
        $value = session(self::SESSION_KEY, now()->format('Y-m'));

        try {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    /**
     * The month currently in effect, as a 'Y-m' string — what views compare
     * against to mark the active option.
     */
    public static function key(): string
    {
        return self::current()->format('Y-m');
    }

    public static function label(): string
    {
        return self::current()->format('F Y');
    }

    public static function set(string $value): void
    {
        session([self::SESSION_KEY => $value]);
    }

    /**
     * The last 12 months (this one included), newest first, for populating
     * the month picker.
     *
     * @return Collection<string, string>
     */
    public static function available(): Collection
    {
        $months = collect();

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months->put($date->format('Y-m'), $date->format('F Y'));
        }

        return $months;
    }

    /**
     * The day of the month a billing cycle starts on, 1-28. Configurable in
     * Admin Settings; 1 means an ordinary calendar month (the default).
     */
    public static function cycleStartDay(): int
    {
        $day = (int) Setting::get(self::CYCLE_START_DAY_SETTING, 1);

        return max(1, min(28, $day));
    }

    /**
     * The real date range covered by the cycle labelled by $anchor (any
     * Carbon within the labelled calendar month; defaults to the globally
     * selected month). With a start day of 26, the cycle labelled "August"
     * runs from July 26 through August 25 — named for the month it closes
     * in, same as the calendar month it mostly overlaps with by default.
     *
     * @return array{0: Carbon, 1: Carbon} [start of day, end of day]
     */
    public static function periodFor(?Carbon $anchor = null): array
    {
        $anchor = ($anchor ?? self::current())->copy()->startOfMonth();
        $startDay = self::cycleStartDay();

        if ($startDay <= 1) {
            return [$anchor->copy()->startOfDay(), $anchor->copy()->endOfMonth()->endOfDay()];
        }

        $previousMonth = $anchor->copy()->subMonth();
        $start = $previousMonth->copy()->day(min($startDay, $previousMonth->daysInMonth))->startOfDay();
        $end = $anchor->copy()->day($startDay - 1)->endOfDay();

        return [$start, $end];
    }

    public static function periodStart(?Carbon $anchor = null): Carbon
    {
        return self::periodFor($anchor)[0];
    }

    public static function periodEnd(?Carbon $anchor = null): Carbon
    {
        return self::periodFor($anchor)[1];
    }
}
