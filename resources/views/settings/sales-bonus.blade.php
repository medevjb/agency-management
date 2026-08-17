@extends('layouts.dashboard')

@section('title', 'Sales Bonus Configuration')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Sales Bonus Configuration</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Approved USD-to-BDT rates and bonus percentages used for employee compensation.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Back to settings</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 dark:bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">New effective-dated configuration</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Saving a later date closes the current open configuration the day before. Finalized bonuses never change.</p>
        <form method="POST" action="{{ route('settings.sales-bonus.store') }}" class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-3">
            @csrf
            <div>
                <label for="bonus_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bonus Percentage</label>
                <div class="mt-1 flex rounded-lg shadow-sm"><input id="bonus_percentage" name="bonus_percentage" value="{{ old('bonus_percentage') }}" inputmode="decimal" required class="block w-full rounded-l-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="0.01"><span class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 px-3 text-sm text-gray-500 dark:border-gray-700">decimal</span></div>
                <p class="mt-1 text-xs text-gray-500">1% = 0.01; maximum 25%.</p>
                @error('bonus_percentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="usd_to_bdt_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">USD to BDT Exchange Rate</label>
                <div class="mt-1 flex rounded-lg shadow-sm"><span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 px-3 text-sm text-gray-500 dark:border-gray-700">$1 = ৳</span><input id="usd_to_bdt_rate" name="usd_to_bdt_rate" value="{{ old('usd_to_bdt_rate') }}" inputmode="decimal" required class="block w-full rounded-r-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="122.50"></div>
                @error('usd_to_bdt_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="effective_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Effective From</label>
                <input id="effective_from" type="date" name="effective_from" value="{{ old('effective_from', now()->toDateString()) }}" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('effective_from')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-3 flex justify-end"><button type="submit" onclick="return confirm('Confirm the bonus percentage, USD to BDT rate, and effective date. This affects future compensation calculations.');" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Settings</button></div>
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700"><h2 class="font-semibold text-gray-900 dark:text-gray-100">Configuration history</h2></div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm"><thead class="bg-gray-50 dark:bg-gray-800"><tr class="text-left text-xs uppercase tracking-wide text-gray-500"><th class="px-6 py-3">Effective from</th><th class="px-6 py-3 text-right">Bonus %</th><th class="px-6 py-3 text-right">USD → BDT</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Changed by</th></tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">@forelse($settings as $setting)<tr><td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $setting->effective_from->format('d M Y') }}</td><td class="px-6 py-4 text-right">{{ number_format((float) $setting->bonus_percentage * 100, 2) }}%</td><td class="px-6 py-4 text-right">৳{{ number_format((float) $setting->usd_to_bdt_rate, 4) }}</td><td class="px-6 py-4"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $setting->effective_to ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' : 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-300' }}">{{ $setting->effective_to ? 'Expired' : 'Active' }}</span></td><td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $setting->createdBy?->name ?? 'System' }}</td></tr>@empty<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No bonus configuration has been added yet.</td></tr>@endforelse</tbody></table></div>
    </div>
</div>
@endsection
