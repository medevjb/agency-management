@extends('layouts.dashboard')

@section('title', 'Create Order')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-shopping-cart text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
                    <p class="text-sm text-gray-500">Add a new order to the system</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <!-- Main Form Container -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 ">
            <div class="p-6 space-y-6">
                <!-- Row 1: Client Name & Order ID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Client Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
                               placeholder="Enter client name">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Order ID
                        </label>
                        <input type="text" name="order_id"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
                               placeholder="ORD-2024-001">
                        @error('order_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm resize-none"
                              placeholder="Brief description of the order..."></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 3: Status & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-sm">
                            <option value="">Select status</option>
                            <option value="nra">NRA</option>
                            <option value="pending">Pending</option>
                            <option value="in-progress" selected>In Progress</option>
                            <option value="delivered">Delivered</option>
                            <option value="on-hold">On Hold</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-sm">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 4: Amount Split & Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Amount Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Order Amount ($) <span class="text-gray-400 font-normal" id="amountLabel">— select a profile</span>
                        </label>
                        <input type="number" name="value" id="orderAmount" step="0.01" min="0"
                               value="{{ old('value') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
                               placeholder="0.00">
                        @error('value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Visual Split -->
                        <div id="amountSplit" class="mt-3 hidden">
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
                                        Order Amount
                                    </span>
                                    <span class="font-semibold text-gray-900" id="splitAmount">$0.00</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                                        <span id="feeLabel">Platform Fee</span>
                                    </span>
                                    <span class="font-semibold text-red-500" id="splitFee">-$0.00</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2 flex items-center justify-between text-sm">
                                    <span class="text-gray-900 font-semibold flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                                        <span id="netLabel">Net Value</span>
                                    </span>
                                    <span class="font-bold text-green-600 text-base" id="splitNet">$0.00</span>
                                </div>
                                <!-- Progress bar -->
                                <div class="flex h-2 rounded-full overflow-hidden bg-gray-200 mt-1">
                                    <div id="progressNet" class="bg-green-500 transition-all duration-300" style="width: 80%"></div>
                                    <div id="progressFee" class="bg-red-400 transition-all duration-300" style="width: 20%"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="actual_revenue" id="actualRevenue" value="{{ old('actual_revenue') }}">
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date
                            </label>
                            <input type="text" name="start_date" id="start_date" readonly
                                   class="datepicker w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm cursor-pointer bg-white"
                                   placeholder="Select start date">
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                End Date
                            </label>
                            <input type="text" name="end_date" id="end_date" readonly
                                   class="datepicker w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm cursor-pointer bg-white"
                                   placeholder="Select end date">
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Row 5: Team & Tech Stack -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                        <x-ajax-select name="team_id"
                                       :url="route('search.teams')"
                                       placeholder="Search teams..." />
                        @error('team_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tech Stack</label>
                        <x-ajax-select name="tech_stack_id"
                                       :url="route('search.tech-stacks')"
                                       placeholder="Search tech stacks..." />
                        @error('tech_stack_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 6: Freelance Profile -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Freelance Profile</label>
                    <x-ajax-select name="freelance_profile_id"
                                   :url="route('search.profiles')"
                                   placeholder="Search profiles..." />
                    @error('freelance_profile_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 7: Assign Users -->
                <div id="userSelectorContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Assign Users
                    </label>

                    <!-- Selected Users Display -->
                    <div id="selectedUsersDisplay" class="border border-gray-300 rounded-lg p-3 min-h-[60px] bg-gray-50 mb-2">
                        <span class="text-gray-400 text-sm">No users assigned</span>
                    </div>

                    <!-- Hidden inputs for selected users will be added dynamically -->

                    <!-- Search and Add Users -->
                    <div class="relative">
                        <input type="text" id="userSearchInput"
                               placeholder="Search users by name or email..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">

                        <!-- Dropdown List -->
                        <div id="userDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- Users will be populated here by JavaScript -->
                        </div>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Start typing to search and add users
                    </p>
                    @error('users')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    (function() {
                        // State
                        let selectedUsers = [];
                        let searchTimeout = null;
                        let currentUsers = []; // Store currently displayed users

                        // DOM Elements
                        const searchInput = document.getElementById('userSearchInput');
                        const dropdown = document.getElementById('userDropdown');
                        const selectedDisplay = document.getElementById('selectedUsersDisplay');

                        // Fetch users from server with search
                        async function fetchUsers(search = '') {
                            try {
                                const excludeIds = selectedUsers.map(u => u.id);
                                const params = new URLSearchParams();
                                if (search) {
                                    params.append('search', search);
                                }
                                if (excludeIds.length > 0) {
                                    params.append('exclude', excludeIds.join(','));
                                }

                                console.log('Fetching users with params:', params.toString());
                                const response = await axios.get(`/orders/search-users?${params}`);
                                console.log('Users fetched:', response.data.users);
                                currentUsers = response.data.users;
                                return currentUsers;
                            } catch (error) {
                                console.error('Error fetching users:', error);
                                return [];
                            }
                        }

                        // Render dropdown with users
                        function renderDropdown(users) {
                            if (users.length === 0) {
                                dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No users found</div>';
                            } else {
                                dropdown.innerHTML = users.map(user => `
                                    <div class="user-option flex items-center gap-3 p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors"
                                         data-user-id="${user.id}"
                                         data-user-name="${user.name}"
                                         data-user-email="${user.email}">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                            ${user.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                                <div class="font-semibold text-sm text-gray-900 truncate">${user.name}</div>
                                                <span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full flex-shrink-0">${user.employee_id || 'N/A'}</span>
                                                ${user.team ? `<span class="text-xs text-white bg-purple-500 px-2 py-0.5 rounded-full flex-shrink-0"><i class="fas fa-users mr-1"></i>${user.team.name}</span>` : ''}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">
                                                ${user.position ? `<span class="font-medium text-gray-600">${user.position}</span> &bull; ` : ''}${user.email}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center transition-colors">
                                                <i class="fas fa-plus text-indigo-600 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                `).join('');

                                // Add click handlers
                                dropdown.querySelectorAll('.user-option').forEach(option => {
                                    option.addEventListener('click', function(e) {
                                        e.stopPropagation(); // Prevent closing dropdown
                                        const user = {
                                            id: parseInt(this.dataset.userId),
                                            name: this.dataset.userName,
                                            email: this.dataset.userEmail
                                        };
                                        addUser(user);
                                    });
                                });
                            }
                        }

                        // Search and display users (debounced)
                        function searchUsers(query) {
                            // Clear previous timeout
                            if (searchTimeout) {
                                clearTimeout(searchTimeout);
                            }

                            // Debounce search
                            searchTimeout = setTimeout(async () => {
                                const users = await fetchUsers(query);
                                renderDropdown(users);
                            }, 300);
                        }

                        // Add user to selected list
                        function addUser(user) {
                            if (!selectedUsers.find(s => s.id === user.id)) {
                                selectedUsers.push(user);
                                updateSelectedDisplay();

                                // Refresh search results
                                if (searchInput.value.trim()) {
                                    searchUsers(searchInput.value);
                                } else {
                                    // Just re-render current users excluding selected
                                    const availableUsers = currentUsers.filter(u => !selectedUsers.find(s => s.id === u.id));
                                    renderDropdown(availableUsers);
                                }
                            }
                        }

                        // Remove user from selected list
                        function removeUser(userId) {
                            selectedUsers = selectedUsers.filter(u => u.id !== userId);
                            updateSelectedDisplay();

                            // Refresh search results
                            if (searchInput.value.trim()) {
                                searchUsers(searchInput.value);
            } else {
                // Fetch fresh list
                fetchUsers('').then(users => renderDropdown(users));
            }
                        }

                        // Update selected users display
                        function updateSelectedDisplay() {
                            if (selectedUsers.length === 0) {
                                selectedDisplay.innerHTML = '<span class="text-gray-400 text-sm">No users assigned</span>';
                            } else {
                                selectedDisplay.innerHTML = selectedUsers.map(user => `
                                    <span class="inline-flex items-center gap-1 px-3 py-1 m-1 bg-indigo-100 text-indigo-800 rounded-lg text-sm">
                                        <span>${user.name}</span>
                                        <button type="button" class="remove-user-btn hover:text-red-600 transition-colors" data-user-id="${user.id}">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </span>
                                `).join('');

                                // Add click handlers for remove buttons
                                selectedDisplay.querySelectorAll('.remove-user-btn').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const userId = parseInt(this.dataset.userId);
                                        removeUser(userId);
                                    });
                                });
                            }

                            // Update hidden inputs - create multiple inputs for array submission
                            // Remove old inputs
                            const container = document.getElementById('userSelectorContainer');
                            const oldInputs = container.querySelectorAll('input[name="users[]"]');
                            oldInputs.forEach(input => input.remove());

                            // Add new inputs
                            selectedUsers.forEach(user => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'users[]';
                                input.value = user.id;
                                container.appendChild(input);
                            });
                        }

                        // Event Listeners
                        searchInput.addEventListener('input', function() {
                            const query = this.value.trim();

                            // Always show dropdown when typing or focused
                            dropdown.classList.remove('hidden');

                            // Clear previous timeout
                            if (searchTimeout) {
                                clearTimeout(searchTimeout);
                            }

                            // Debounce and fetch users
                            searchTimeout = setTimeout(async () => {
                                const users = await fetchUsers(query);
                                renderDropdown(users);
                            }, 300);
                        });

                        searchInput.addEventListener('focus', async function() {
                            // Show dropdown when focused - load default users
                            dropdown.classList.remove('hidden');
                            const users = await fetchUsers('');
                            renderDropdown(users);
                        });

                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(e) {
                            if (!dropdown.contains(e.target) && e.target !== searchInput) {
                                dropdown.classList.add('hidden');
                            }
                        });

                        // Initialize
                        console.log('UserSelector initialized');
                        updateSelectedDisplay();
                    })();
                </script>

                <!-- Row 8: Order Sheet URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Order Sheet URL
                    </label>
                    <input type="url" name="order_sheet_url"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
                           placeholder="https://docs.google.com/document/d/...">
                    @error('order_sheet_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 8: Delivered Date (Auto-filled when status is delivered) -->
                <div id="deliveredDateContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivered Date
                    </label>
                    <input type="text" name="delivered_at" id="delivered_at" readonly
                           class="datetimepicker w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm cursor-pointer bg-white"
                           placeholder="Select delivered date and time">
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Automatically set when status changes to delivered
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('orders.index') }}"
                   class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium transition-all">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg shadow-md hover:shadow-lg text-sm font-semibold transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Create Order
                </button>
            </div>
        </div>
    </form>
</div>

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

            const statusSelect = document.getElementById('status');
            const deliveredDateContainer = document.getElementById('deliveredDateContainer');
            const deliveredDateInput = document.getElementById('delivered_at');

            // Initialize datetime picker for delivered_at
            let deliveredPicker = null;

            statusSelect.addEventListener('change', function() {
                if (this.value === 'delivered') {
                    deliveredDateContainer.classList.remove('hidden');

                    // Initialize datetime picker if not already initialized
                    if (!deliveredPicker) {
                        deliveredPicker = flatpickr(deliveredDateInput, {
                            enableTime: true,
                            dateFormat: 'Y-m-d H:i:S',
                            allowInput: false,
                            disableMobile: true,
                            clickOpens: true,
                            inline: false,
                            static: true,
                            animate: true,
                            monthSelectorType: 'static',
                            time_24hr: true,
                            appendTo: document.body,
                            position: 'below',
                            prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>',
                            nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>'
                        });
                    }

                    // Auto-fill with current date/time if empty
                    if (!deliveredDateInput.value) {
                        const now = new Date();
                        deliveredPicker.setDate(now);
                    }
                } else {
                    deliveredDateContainer.classList.add('hidden');
                    if (deliveredPicker) {
                        deliveredPicker.clear();
                    }
                }
            });
        });
    </script>
@endpush
@endsection
