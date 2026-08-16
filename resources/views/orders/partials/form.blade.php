{{--
    Shared order create/edit form body.
    Expects: $salesPeople (Collection<User>), $order (Order|null).
    The surrounding <form> and CSRF live in the calling view.
--}}
@php
    $isSalesUser = auth()->user()->isSales();
    $order = $order ?? null;
    $existingShares = $order?->salesShares ?? collect();
    // Net revenue (after platform fees) is what gets distributed as sales credit.
    $netAmount = (float) ($order?->actual_revenue ?? $order?->value ?? 0);
@endphp

<!-- Section: Client & Order -->
<x-form-section icon="fa-circle-user" title="Client & Order"
                description="Who the order is for, what is being delivered and any login access the team needs.">
    <div class="flex flex-col md:flex-row md:items-start gap-4">
        <div id="repeatControl" class="relative flex-1 min-w-0"
             data-exclude-id="{{ $order?->id ?? '' }}"
             data-is-repeat="{{ old('is_repeat', $order?->is_repeat ?? 0) }}"
             data-repeat-name="{{ $order?->is_repeat ? $order?->name : '' }}">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Client Name <span class="text-red-500">*</span>
            </label>

            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="name" id="clientNameInput" required value="{{ old('name', $order?->name) }}"
                           class="w-full px-4 py-2.5 pr-9 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                           placeholder="Enter client name"
                           autocomplete="off">
                </div>
                <label for="repeatCheckbox"
                       class="shrink-0 inline-flex items-center gap-2 cursor-pointer select-none"
                       title="Mark this as a repeat order from an existing client">
                    <input type="checkbox" id="repeatCheckbox" {{ old('is_repeat', $order?->is_repeat ?? 0) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500/30 cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Repeat</span>
                </label>
            </div>

            <input type="hidden" name="is_repeat" id="isRepeatInput" value="{{ old('is_repeat', $order?->is_repeat ? 1 : 0) }}">
            <input type="hidden" name="repeat_client_key" id="repeatClientKey"
                   value="{{ old('repeat_client_key', $order?->repeat_client_key ?? ($order?->is_repeat ? $order?->client_key : '')) }}">

            <p id="repeatHint" class="hidden mt-2 text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Type to search your existing clients, then pick one to link this order to that client.
            </p>

            <div id="repeatConfirmation"
                 class="hidden mt-2 inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                Repeat for <span id="repeatClientName"></span>
            </div>

            <div id="existingClientDropdown"
                 class="hidden absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            </div>
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:w-44">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Order ID
            </label>
            <input type="text" name="order_id" value="{{ old('order_id', $order?->order_id) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                   placeholder="ORD-2024-001">
            @error('order_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @unless($isSalesUser)
            <div class="md:w-36">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Priority <span class="text-red-500">*</span>
                </label>
                <select name="priority" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                    <option value="low" {{ old('priority', $order?->priority ?? 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $order?->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $order?->priority ?? 'medium') === 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endunless
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Description
        </label>
        <textarea name="description" rows="2"
                  class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm resize-none"
                  placeholder="Brief description of the order...">{{ old('description', $order?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Notes
        </label>
        <textarea name="notes" rows="4"
                  class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                  placeholder="Anything the team should know about this order...">{{ old('notes', $order?->notes) }}</textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-1"></i>
            Visible to everyone who can see this order.
        </p>
        @error('notes')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @include('orders.partials.credentials-editor', ['value' => old('credentials', $order?->credentials ?? '')])
</x-form-section>

@if($order && !$isSalesUser)
    <!-- Section: Workflow (edit only — new orders start as NRA at 0% progress) -->
    <x-form-section icon="fa-diagram-project" title="Workflow"
                    description="Status and delivery progress.">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Status <span class="text-red-500">*</span>
            </label>
            <select name="status" required
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                <option value="">Select status</option>
                @foreach(['nra' => 'NRA', 'pending' => 'Pending', 'in-progress' => 'In Progress', 'delivered' => 'Delivered', 'completed' => 'Completed', 'on-hold' => 'On Hold', 'cancelled' => 'Cancelled'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $order?->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @include('orders.partials.progress-field', ['value' => (int) old('progress', $order?->progress ?? 0)])
    </x-form-section>
@endif

<!-- Section: Freelance & Amount -->
<x-form-section icon="fa-handshake" title="Freelance & Amount"
                description="The freelance profile that took the order. The platform fee is deducted here, so sales credit is split against the net amount.">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Freelance Profile</label>
        <x-ajax-select name="freelance_profile_id"
                       :url="route('search.profiles')"
                       :value="old('freelance_profile_id', $order?->freelance_profile_id)"
                       :label="$order?->freelanceProfile ? $order->freelanceProfile->username.' ('.ucfirst($order->freelanceProfile->platform).')' : null"
                       placeholder="Search profiles..." />
        @error('freelance_profile_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Order Amount ($) <span class="text-gray-400 dark:text-gray-500 font-normal" id="amountLabel">— select a profile</span>
            </label>
            <input type="number" name="value" id="orderAmount" step="0.01" min="0"
                   value="{{ old('value', $order?->value) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                   placeholder="0.00">
            @error('value')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <!-- Visual Split -->
            <div id="amountSplit" class="mt-3 hidden">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
                            Order Amount
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100" id="splitAmount">$0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                            <span id="feeLabel">Platform Fee</span>
                        </span>
                        <span class="font-semibold text-red-500 dark:text-red-400" id="splitFee">-$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex items-center justify-between text-sm">
                        <span class="text-gray-900 dark:text-gray-100 font-semibold flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                            <span id="netLabel">Net Value</span>
                        </span>
                        <span class="font-bold text-green-600 dark:text-green-400 text-base" id="splitNet">$0.00</span>
                    </div>
                    <!-- Progress bar -->
                    <div class="flex h-2 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 mt-1">
                        <div id="progressNet" class="bg-green-500 transition-all duration-300" style="width: 80%"></div>
                        <div id="progressFee" class="bg-red-400 transition-all duration-300" style="width: 20%"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="actual_revenue" id="actualRevenue" value="{{ old('actual_revenue', $order?->actual_revenue) }}">
        </div>

        @unless($isSalesUser)
            <div class="grid grid-cols-2 gap-4 content-start">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Start Date
                    </label>
                    <input type="text" name="start_date" id="start_date" readonly
                           value="{{ old('start_date', $order?->start_date?->format('Y-m-d')) }}"
                           class="datepicker w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm cursor-pointer"
                           placeholder="Select start date">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        End Date
                    </label>
                    <input type="text" name="end_date" id="end_date" readonly
                           value="{{ old('end_date', $order?->end_date?->format('Y-m-d')) }}"
                           class="datepicker w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm cursor-pointer"
                           placeholder="Select end date">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endunless
    </div>
</x-form-section>

<!-- Section: Sales credit -->
@if($salesPeople->isNotEmpty())
    <x-form-section icon="fa-coins" title="Sales Credit"
                    description="Who earns the credit for this order. The split always adds up to the net amount — the order value after the platform's fee.">
        <x-slot:header>
            <span id="salesCreditRemaining" class="credit-remaining" data-order-value="{{ $netAmount }}"
                  data-net-revenue="{{ $netAmount }}"></span>
        </x-slot:header>

        @include('orders.partials.sales-credit-field', ['salesPeople' => $salesPeople, 'order' => $order])
    </x-form-section>
@endif

@unless($isSalesUser)
    <!-- Section: Team & Tech Stack -->
    <x-form-section icon="fa-layer-group" title="Team & Tech Stack"
                    description="Which team owns the order and which stack it runs on.">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team</label>
                <x-ajax-select name="team_id"
                               :url="route('search.teams')"
                               :value="old('team_id', $order?->team_id)"
                               :label="$order?->team?->name"
                               placeholder="Search teams..." />
                @error('team_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tech Stack</label>
                <x-ajax-select name="tech_stack_id"
                               :url="route('search.tech-stacks')"
                               :value="old('tech_stack_id', $order?->tech_stack_id)"
                               :label="$order?->techStack?->name"
                               placeholder="Search tech stacks..." />
                @error('tech_stack_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </x-form-section>

    <!-- Section: Assign Users -->
    <x-form-section icon="fa-user-plus" title="Assign Users"
                    description="People who will deliver this order. They get notified on assignment.">
        <div id="userSelectorContainer">
            <div id="selectedUsersDisplay"
                 class="border border-gray-300 dark:border-gray-700 rounded-lg p-3 min-h-[60px] bg-gray-50 dark:bg-gray-800 mb-2">
                <span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>
            </div>

            <div class="relative">
                <input type="text" id="userSearchInput"
                       placeholder="Search users by name or email..."
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">

                <div id="userDropdown"
                     class="hidden absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                </div>
            </div>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Start typing to search and add users
            </p>
            @error('users')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </x-form-section>
@endunless

<!-- Section: Documents -->
<x-form-section icon="fa-folder-open" title="Documents"
                description="Initial client documents to attach to the order.">
    @include('orders.partials.documents-field', ['order' => $order])
</x-form-section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Fresh / Repeat client control ---
        const control = document.getElementById('repeatControl');

        if (!control) {
            return;
        }

        const isRepeatInput = document.getElementById('isRepeatInput');
        const repeatClientKeyInput = document.getElementById('repeatClientKey');
        const checkbox = document.getElementById('repeatCheckbox');
        const hint = document.getElementById('repeatHint');
        const confirmation = document.getElementById('repeatConfirmation');
        const repeatClientName = document.getElementById('repeatClientName');
        const nameInput = document.getElementById('clientNameInput');
        const dropdown = document.getElementById('existingClientDropdown');
        const excludeId = control.dataset.excludeId || '';

        let picked = control.dataset.isRepeat === '1';
        let pickedClientName = control.dataset.repeatName || '';
        let searchTimeout = null;

        function isRepeatMode() {
            return isRepeatInput.value === '1';
        }

        function setRepeatVisuals() {
            const active = isRepeatMode();
            checkbox.checked = active;
            hint.classList.toggle('hidden', !active);
            confirmation.classList.toggle('hidden', !(active && picked));
            if (active && picked) {
                repeatClientName.textContent = pickedClientName;
            }
        }

        function hideDropdown() {
            dropdown.classList.add('hidden');
            dropdown.innerHTML = '';
        }

        function renderClients(clients) {
            if (clients.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No existing clients found</div>';
            } else {
                dropdown.innerHTML = clients.map(client => `
                    <button type="button" data-key="${client.key}" class="client-option w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span><span class="truncate">${client.name}</span>
                    </button>
                `).join('');

                dropdown.querySelectorAll('.client-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const client = clients.find(c => c.key === this.dataset.key);
                        if (!client) {
                            return;
                        }
                        nameInput.value = client.name;
                        repeatClientKeyInput.value = client.key;
                        isRepeatInput.value = '1';
                        picked = true;
                        pickedClientName = client.name;
                        fillProfileFromClient(client);
                        hideDropdown();
                        setRepeatVisuals();
                    });
                });
            }
            dropdown.classList.remove('hidden');
        }

        async function fetchClients(query) {
            try {
                const params = new URLSearchParams();
                if (query) params.append('q', query);
                if (excludeId) params.append('exclude', excludeId);

                const response = await axios.get(`{{ route('orders.search-clients') }}?${params}`);
                renderClients(response.data.clients);
            } catch (error) {
                renderClients([]);
            }
        }

        function fillProfileFromClient(client) {
            if (!client.profile_id) return;
            const hidden = document.querySelector('[data-ajax-select] input[name="freelance_profile_id"]');
            if (!hidden || hidden.value) return;
            const wrapper = hidden.closest('[data-ajax-select]');
            const uid = wrapper.dataset.uid;
            const search = document.getElementById(uid + '_s');
            const clear = document.getElementById(uid + '_c');
            hidden.value = client.profile_id;
            search.value = client.profile_name || '';
            search.classList.add('border-indigo-400', 'bg-indigo-50');
            clear.classList.remove('hidden');
            hidden.dispatchEvent(new Event('change', {bubbles: true}));
        }

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                isRepeatInput.value = '1';
                picked = false;
                pickedClientName = '';
                setRepeatVisuals();
                fetchClients(nameInput.value.trim());
            } else {
                isRepeatInput.value = '0';
                picked = false;
                pickedClientName = '';
                repeatClientKeyInput.value = '';
                hideDropdown();
                setRepeatVisuals();
            }
        });

        nameInput.addEventListener('input', function() {
            if (!isRepeatMode()) {
                return;
            }
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchClients(this.value.trim()), 300);
        });

        nameInput.addEventListener('focus', function() {
            if (isRepeatMode()) {
                dropdown.classList.remove('hidden');
                fetchClients(this.value.trim());
            }
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== nameInput) {
                hideDropdown();
            }
        });

        control.closest('form').addEventListener('submit', function() {
            // Repeat only sticks when an existing client was actually picked;
            // a blank/new name counts as fresh sales.
            if (isRepeatMode() && !picked) {
                isRepeatInput.value = '0';
                repeatClientKeyInput.value = '';
            }
        });

        setRepeatVisuals();
        if (!isRepeatMode()) {
            hideDropdown();
        }
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Amount Split Auto-Calculation ---
        const orderAmountInput = document.getElementById('orderAmount');
        const actualRevenueInput = document.getElementById('actualRevenue');
        const splitContainer = document.getElementById('amountSplit');
        const splitAmount = document.getElementById('splitAmount');
        const splitFee = document.getElementById('splitFee');
        const splitNet = document.getElementById('splitNet');
        const feeLabel = document.getElementById('feeLabel');
        const netLabel = document.getElementById('netLabel');
        const amountLabel = document.getElementById('amountLabel');
        const progressNet = document.getElementById('progressNet');
        const progressFee = document.getElementById('progressFee');
        const platformFees = {fiverr: 0.20, upwork: 0.10};

        if (!orderAmountInput) {
            return;
        }

        const platformNames = {fiverr: 'Fiverr', upwork: 'Upwork'};

        function getProfileLabelInput() {
            const hidden = document.querySelector('[data-ajax-select] input[name="freelance_profile_id"]');
            if (!hidden) return null;
            const uid = hidden.closest('[data-ajax-select]').dataset.uid;
            return document.getElementById(uid + '_s');
        }

        function getSelectedPlatform() {
            const labelInput = getProfileLabelInput();
            if (!labelInput || !labelInput.value) return 'fiverr';
            const match = labelInput.value.match(/\((\w+)\)\s*$/);
            return match ? match[1].toLowerCase() : 'fiverr';
        }

        function updatePlatformLabels() {
            const platform = getSelectedPlatform();
            const feeRate = platformFees[platform] ?? 0.20;
            const netRate = 1 - feeRate;
            const name = platformNames[platform] ?? 'Platform';
            amountLabel.textContent = '— ' + name + ' price';
            feeLabel.textContent = name + ' Fee (' + Math.round(feeRate * 100) + '%)';
            netLabel.textContent = 'Net Value (' + Math.round(netRate * 100) + '%)';
        }

        function calculateSplit() {
            const amount = parseFloat(orderAmountInput.value) || 0;
            if (amount > 0) {
                const platform = getSelectedPlatform();
                const feeRate = platformFees[platform] ?? 0.20;
                const netRate = 1 - feeRate;
                const fee = amount * feeRate;
                const net = amount * netRate;

                splitAmount.textContent = '$' + amount.toFixed(2);
                splitFee.textContent = '-$' + fee.toFixed(2);
                splitNet.textContent = '$' + net.toFixed(2);
                actualRevenueInput.value = net.toFixed(2);
                progressNet.style.width = Math.round(netRate * 100) + '%';
                progressFee.style.width = Math.round(feeRate * 100) + '%';

                splitContainer.classList.remove('hidden');
            } else {
                splitContainer.classList.add('hidden');
                actualRevenueInput.value = '';
            }

            // Keep the sales credit distribution in sync with the net amount.
            actualRevenueInput.dispatchEvent(new Event('input', {bubbles: true}));
        }

        // Listen for profile selection changes via the change event dispatched by x-ajax-select
        document.addEventListener('change', function(e) {
            const hiddenInput = e.target;
            if (hiddenInput.name === 'freelance_profile_id') {
                updatePlatformLabels();
                calculateSplit();
            }
        });

        orderAmountInput.addEventListener('input', calculateSplit);
        updatePlatformLabels();
        calculateSplit();
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- User assignment ---
        const container = document.getElementById('userSelectorContainer');

        if (!container) {
            return;
        }

        let selectedUsers = @js($order?->users ?? []);
        let searchTimeout = null;
        let currentUsers = [];

        const searchInput = document.getElementById('userSearchInput');
        const dropdown = document.getElementById('userDropdown');
        const selectedDisplay = document.getElementById('selectedUsersDisplay');

        async function fetchUsers(search = '') {
            try {
                const excludeIds = selectedUsers.map(u => u.id);
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (excludeIds.length > 0) params.append('exclude', excludeIds.join(','));

                const response = await axios.get(`{{ route('orders.search-users') }}?${params}`);
                currentUsers = response.data.users;
                return currentUsers;
            } catch (error) {
                console.error('Error fetching users:', error);
                return [];
            }
        }

        function renderDropdown(users) {
            if (users.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No users found</div>';
            } else {
                dropdown.innerHTML = users.map(user => `
                    <div class="user-option flex items-center gap-3 p-3 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                         data-user-id="${user.id}"
                         data-user-name="${user.name}"
                         data-user-email="${user.email}">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                <div class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">${user.name}</div>
                                <span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full flex-shrink-0">${user.employee_id || 'N/A'}</span>
                                ${user.team ? `<span class="text-xs text-white bg-purple-500 px-2 py-0.5 rounded-full flex-shrink-0"><i class="fas fa-users mr-1"></i>${user.team.name}</span>` : ''}
                                ${user.tech_stack ? `<span class="text-xs text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded-full flex-shrink-0"><i class="fas fa-layer-group mr-1"></i>${user.tech_stack.name}</span>` : ''}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                ${user.position ? `<span class="font-medium text-gray-600 dark:text-gray-300">${user.position}</span> &bull; ` : ''}${user.email}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 hover:bg-indigo-200 dark:hover:bg-indigo-500/20 flex items-center justify-center transition-colors">
                                <i class="fas fa-plus text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.user-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.stopPropagation();
                        addUser({
                            id: parseInt(this.dataset.userId),
                            name: this.dataset.userName,
                            email: this.dataset.userEmail,
                        });
                    });
                });
            }
        }

        function searchUsers(query) {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                renderDropdown(await fetchUsers(query));
            }, 300);
        }

        function addUser(user) {
            if (selectedUsers.find(s => s.id === user.id)) return;
            selectedUsers.push(user);
            updateSelectedDisplay();
            if (searchInput.value.trim()) {
                searchUsers(searchInput.value);
            } else {
                renderDropdown(currentUsers.filter(u => !selectedUsers.find(s => s.id === u.id)));
            }
        }

        function removeUser(userId) {
            selectedUsers = selectedUsers.filter(u => u.id !== userId);
            updateSelectedDisplay();
            if (searchInput.value.trim()) {
                searchUsers(searchInput.value);
            } else {
                fetchUsers('').then(users => renderDropdown(users));
            }
        }

        function updateSelectedDisplay() {
            if (selectedUsers.length === 0) {
                selectedDisplay.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>';
            } else {
                selectedDisplay.innerHTML = selectedUsers.map(user => `
                    <span class="inline-flex items-center gap-1 px-3 py-1 m-1 bg-indigo-100 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-400 rounded-lg text-sm">
                        <span>${user.name}</span>
                        <button type="button" class="remove-user-btn hover:text-red-600 dark:hover:text-red-400 transition-colors" data-user-id="${user.id}">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `).join('');

                selectedDisplay.querySelectorAll('.remove-user-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        removeUser(parseInt(this.dataset.userId));
                    });
                });
            }

            container.querySelectorAll('input[name="users[]"]').forEach(input => input.remove());
            selectedUsers.forEach(user => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'users[]';
                input.value = user.id;
                container.appendChild(input);
            });
        }

        searchInput.addEventListener('input', function() {
            dropdown.classList.remove('hidden');
            searchUsers(this.value.trim());
        });

        searchInput.addEventListener('focus', async function() {
            dropdown.classList.remove('hidden');
            renderDropdown(await fetchUsers(''));
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== searchInput) {
                dropdown.classList.add('hidden');
            }
        });

        updateSelectedDisplay();
    });
</script>
@endpush