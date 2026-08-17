<?php

namespace App\Services;

use App\Models\BonusFinancialSetting;
use App\Models\Order;
use App\Models\QuarterlyBonus;
use App\Models\SalesTarget;
use App\Models\User;
use App\Support\GlobalMonth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesBonusService
{
    /** @return array<string, string|int|bool> */
    public function calculate(User $salesperson, Carbon $date): array
    {
        $quarter = (int) ceil($date->month / 3);
        $existing = QuarterlyBonus::query()->where('salesperson_id', $salesperson->id)->where('year', $date->year)->where('quarter', $quarter)->whereIn('status', ['FINALIZED', 'PAID'])->first();
        if ($existing) {
            return $this->fromSnapshot($existing);
        }
        $setting = BonusFinancialSetting::applicableOn($date);
        $months = collect(range(($quarter - 1) * 3 + 1, $quarter * 3))->map(fn (int $month) => Carbon::create($date->year, $month, 1));
        [$sales, $targets, $extra] = $this->monthlyTotals($salesperson, $months);
        $rate = $setting?->bonus_percentage ?? '0';
        $exchangeRate = $setting?->usd_to_bdt_rate ?? '0';
        $source = bcmul($extra, $rate, 6);
        $bdt = $setting ? $this->round(bcmul($source, $exchangeRate, 6), 2) : '0.00';

        return ['year' => $date->year, 'quarter' => $quarter, 'quarter_sales' => $sales, 'quarter_target' => $targets, 'qualified_extra_sales' => $extra, 'bonus_percentage' => $rate, 'exchange_rate' => $exchangeRate, 'bonus_source_amount' => $source, 'bonus_payment_amount' => $bdt, 'status' => 'ACCRUING', 'configuration_available' => $setting !== null];
    }

    public function finalize(User $salesperson, Carbon $date): QuarterlyBonus
    {
        $calculation = $this->calculate($salesperson, $date);

        return QuarterlyBonus::updateOrCreate(['salesperson_id' => $salesperson->id, 'year' => $calculation['year'], 'quarter' => $calculation['quarter']], ['quarter_sales' => $calculation['quarter_sales'], 'quarter_target' => $calculation['quarter_target'], 'qualified_extra_sales' => $calculation['qualified_extra_sales'], 'bonus_percentage_applied' => $calculation['bonus_percentage'], 'bonus_source_currency' => 'USD', 'bonus_source_amount' => $calculation['bonus_source_amount'], 'exchange_rate_applied' => $calculation['exchange_rate'], 'payment_currency' => 'BDT', 'bonus_payment_amount' => $calculation['bonus_payment_amount'], 'status' => 'FINALIZED', 'calculated_at' => now(), 'finalized_at' => now()]);
    }

    /** @param Collection<int, Carbon> $months @return array{string, string, string} */
    private function monthlyTotals(User $salesperson, Collection $months): array
    {
        $sales = '0.00';
        $targets = '0.00';
        $extra = '0.00';
        foreach ($months as $month) {
            [$start, $end] = GlobalMonth::periodFor($month);
            $amount = (string) Order::query()->whereHas('salesShares', fn ($shares) => $shares->where('user_id', $salesperson->id))->whereBetween('created_at', [$start, $end])->with(['salesShares' => fn ($shares) => $shares->where('user_id', $salesperson->id)])->get()->reduce(fn (string $total, Order $order) => bcadd($total, (string) $order->salesShareFor($salesperson->id), 2), '0.00');
            $target = number_format(SalesTarget::amountFor($salesperson->id, $month) ?? 0, 2, '.', '');
            $sales = bcadd($sales, $amount, 2);
            $targets = bcadd($targets, $target, 2);
            $extra = bcadd($extra, bccomp($amount, $target, 2) === 1 ? bcsub($amount, $target, 2) : '0.00', 2);
        }

        return [$sales, $targets, $extra];
    }

    /** @return array<string, string|int|bool> */
    private function fromSnapshot(QuarterlyBonus $bonus): array
    {
        return ['year' => $bonus->year, 'quarter' => $bonus->quarter, 'quarter_sales' => $bonus->quarter_sales, 'quarter_target' => $bonus->quarter_target, 'qualified_extra_sales' => $bonus->qualified_extra_sales, 'bonus_percentage' => $bonus->bonus_percentage_applied, 'exchange_rate' => $bonus->exchange_rate_applied, 'bonus_source_amount' => $bonus->bonus_source_amount, 'bonus_payment_amount' => $bonus->bonus_payment_amount, 'status' => $bonus->status, 'configuration_available' => true];
    }

    private function round(string $value, int $precision): string
    {
        return bcadd($value, '0.'.str_repeat('0', $precision).'5', $precision);
    }
}
