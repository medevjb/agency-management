{{--
    Who gets credited for this order's amount.
    Expects: $salesPeople (Collection<User>), $order (Order|null).

    The surrounding "Sales Credit" section header owns the label and the
    remaining-balance badge; this partial renders the rows, the split
    actions and their styles/scripts only.

    Distribution always happens against the NET amount (the order value
    after the freelance platform's fee) — the backend validates shares
    against net revenue, so the frontend must split the same number.
--}}
@php
    $existingShares = $order?->salesShares ?? collect();
@endphp

@if($salesPeople->isNotEmpty())
    <div id="salesCreditField">
        <div id="salesCreditRows" class="space-y-2">
            @forelse($existingShares as $share)
                <div class="credit-row" data-credit-row>
                    <select name="sales_shares[{{ $loop->index }}][user_id]" class="credit-person">
                        @foreach($salesPeople as $person)
                            <option value="{{ $person->id }}" {{ $share->user_id === $person->id ? 'selected' : '' }}>{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <div class="credit-amount-wrap">
                        <span class="credit-currency">$</span>
                        <input type="number" step="0.01" min="0" name="sales_shares[{{ $loop->index }}][amount]"
                               value="{{ number_format((float) $share->amount, 2, '.', '') }}"
                               class="credit-amount" placeholder="0.00">
                    </div>
                    <button type="button" class="credit-remove" data-credit-remove aria-label="Remove this person">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            @empty
                <div class="credit-row" data-credit-row>
                    <select name="sales_shares[0][user_id]" class="credit-person">
                        @foreach($salesPeople as $person)
                            <option value="{{ $person->id }}" {{ $person->id === auth()->id() ? 'selected' : '' }}>{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <div class="credit-amount-wrap">
                        <span class="credit-currency">$</span>
                        <input type="number" step="0.01" min="0" name="sales_shares[0][amount]"
                               value="" class="credit-amount" placeholder="0.00">
                    </div>
                    <button type="button" class="credit-remove" data-credit-remove aria-label="Remove this person">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            @endforelse
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-3">
            <button type="button" id="salesCreditAdd" class="credit-add">
                <i class="fas fa-plus mr-1.5"></i>Split with someone else
            </button>
            <button type="button" id="salesCreditBalance" class="credit-balance">
                Give the rest to the first person
            </button>
        </div>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-1"></i>
            Leave one person to credit the whole net amount to them. Everyone here counts this order towards their own target.
        </p>

        @error('sales_shares')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <template id="salesCreditTemplate">
        <div class="credit-row" data-credit-row>
            <select name="sales_shares[__INDEX__][user_id]" class="credit-person">
                @foreach($salesPeople as $person)
                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                @endforeach
            </select>
            <div class="credit-amount-wrap">
                <span class="credit-currency">$</span>
                <input type="number" step="0.01" min="0" name="sales_shares[__INDEX__][amount]" class="credit-amount" placeholder="0.00">
            </div>
            <button type="button" class="credit-remove" data-credit-remove aria-label="Remove this person">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </template>

    @push('styles')
    <style>
        .credit-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 10rem 2.25rem;
            gap: 0.5rem;
            align-items: center;
        }

        .credit-person,
        .credit-amount {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: #ffffff;
            color: #111827;
            font-size: 0.875rem;
        }

        .dark .credit-person,
        .dark .credit-amount {
            background-color: #1f2937;
            border-color: #374151;
            color: #f3f4f6;
        }

        .credit-amount-wrap {
            position: relative;
        }

        .credit-amount {
            padding-left: 1.75rem;
            font-variant-numeric: tabular-nums;
        }

        .credit-currency {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.875rem;
            color: #9ca3af;
        }

        .credit-remove {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 2.25rem;
            border-radius: 0.5rem;
            color: #9ca3af;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .credit-remove:hover {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .dark .credit-remove:hover {
            background-color: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }

        .credit-add,
        .credit-balance {
            font-size: 0.75rem;
            font-weight: 700;
            color: #F0430A;
        }

        .credit-add:hover,
        .credit-balance:hover {
            text-decoration: underline;
        }

        .dark .credit-add,
        .dark .credit-balance {
            color: #FFA26E;
        }

        .credit-remaining {
            font-size: 0.75rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
        }

        .credit-remaining.is-balanced { background-color: #d1fae5; color: #047857; }
        .credit-remaining.is-left { background-color: #fef3c7; color: #b45309; }
        .credit-remaining.is-over { background-color: #fee2e2; color: #b91c1c; }

        .dark .credit-remaining.is-balanced { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
        .dark .credit-remaining.is-left { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
        .dark .credit-remaining.is-over { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const field = document.getElementById('salesCreditField');

            if (!field) {
                return;
            }

            const rows = document.getElementById('salesCreditRows');
            const template = document.getElementById('salesCreditTemplate');
            const remaining = document.getElementById('salesCreditRemaining');
            const netInput = document.getElementById('actualRevenue');

            // Sales credit is split against the NET amount (after the platform
            // fee). #actualRevenue is kept in sync by the amount-split helper
            // and carries the same number the backend validates against.
            function orderValue() {
                const net = netInput ? parseFloat(netInput.value) : NaN;

                return Number.isFinite(net) && net > 0
                    ? net
                    : parseFloat(remaining?.dataset.orderValue) || 0;
            }

            function creditedTotal() {
                return Array.from(rows.querySelectorAll('.credit-amount'))
                    .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            }

            function money(value) {
                return '$' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function refresh() {
                const left = Math.round((orderValue() - creditedTotal()) * 100) / 100;

                remaining.classList.remove('is-balanced', 'is-left', 'is-over');

                if (left === 0) {
                    remaining.classList.add('is-balanced');
                    remaining.textContent = 'Fully credited';
                } else if (left > 0) {
                    remaining.classList.add('is-left');
                    remaining.textContent = money(left) + ' not credited';
                } else {
                    remaining.classList.add('is-over');
                    remaining.textContent = money(Math.abs(left)) + ' over the net amount';
                }
            }

            function reindex() {
                rows.querySelectorAll('[data-credit-row]').forEach((row, index) => {
                    row.querySelector('.credit-person').name = `sales_shares[${index}][user_id]`;
                    row.querySelector('.credit-amount').name = `sales_shares[${index}][amount]`;
                });
            }

            document.getElementById('salesCreditAdd').addEventListener('click', function() {
                const markup = template.innerHTML.replace(/__INDEX__/g, rows.children.length);
                rows.insertAdjacentHTML('beforeend', markup);
                reindex();
                refresh();
            });

            document.getElementById('salesCreditBalance').addEventListener('click', function() {
                const first = rows.querySelector('.credit-amount');

                if (!first) {
                    return;
                }

                const others = Array.from(rows.querySelectorAll('.credit-amount')).slice(1)
                    .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

                first.value = Math.max(0, Math.round((orderValue() - others) * 100) / 100).toFixed(2);
                refresh();
            });

            rows.addEventListener('click', function(event) {
                if (!event.target.closest('[data-credit-remove]')) {
                    return;
                }

                if (rows.children.length > 1) {
                    event.target.closest('[data-credit-row]').remove();
                    reindex();
                    refresh();
                }
            });

            rows.addEventListener('input', refresh);
            netInput?.addEventListener('input', refresh);
            refresh();
        });
    </script>
    @endpush
@endif