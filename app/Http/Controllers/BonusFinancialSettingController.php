<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBonusFinancialSettingRequest;
use App\Models\BonusFinancialSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BonusFinancialSettingController extends Controller
{
    public function index(): View
    {
        return view('settings.sales-bonus', ['settings' => BonusFinancialSetting::query()->with('createdBy')->orderByDesc('effective_from')->get()]);
    }

    public function store(StoreBonusFinancialSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $effectiveFrom = $data['effective_from'];
        $newerSettingExists = BonusFinancialSetting::query()->where('is_active', true)->whereDate('effective_from', '>=', $effectiveFrom)->exists();
        if ($newerSettingExists) {
            return back()->withInput()->withErrors(['effective_from' => 'An active bonus configuration already applies on this date.']);
        }
        DB::transaction(function () use ($data, $effectiveFrom, $request): void {
            BonusFinancialSetting::query()->where('is_active', true)->whereNull('effective_to')->whereDate('effective_from', '<', $effectiveFrom)->update(['effective_to' => Carbon::parse($effectiveFrom)->subDay()->toDateString()]);
            BonusFinancialSetting::create([...$data, 'is_active' => true, 'created_by' => $request->user()->id]);
        });

        return redirect()->route('settings.sales-bonus.index')->with('success', 'Sales bonus configuration saved.');
    }
}
