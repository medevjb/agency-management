@extends('layouts.dashboard')

@section('title', 'Create Order')

@push('styles')
<style>
    .form-card {
        overflow: visible
    }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create New Order</h1>
                        <p class="mt-1 text-sm text-gray-600">Fill in the details to create a new order</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 font-semibold transition-all duration-200 self-start">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Orders
            </a>
        </div>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf

        <!-- Basic Information Section -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-info-circle bg-gradient-to-br from-purple-500 to-indigo-600 text-white"></i>
                    Basic Information
                </h2>
            </div>
            <div class="form-card-body space-y-4">

                <!-- Order Name & Order ID on same line -->
                <div class="form-grid">
                    <!-- Order Name -->
                    <div class="form-field">
                        <label class="form-field-label" for="name">
                            <i class="field-icon fas fa-file-alt bg-purple-100 text-purple-600"></i>
                            Client Name <span class="required-indicator">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               class="form-field-input"
                               placeholder="Doe, John"
                               maxlength="255">
                        @error('name')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order ID -->
                    <div class="form-field">
                        <label class="form-field-label" for="order_id">
                            <i class="field-icon fas fa-hashtag bg-purple-100 text-purple-600"></i>
                            Order ID <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="text" name="order_id" id="order_id"
                               class="form-field-input"
                               placeholder="ORD-2024-001"
                               maxlength="255">
                        @error('order_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-field">
                    <label class="form-field-label" for="description">
                        <i class="field-icon fas fa-align-left bg-purple-100 text-purple-600"></i>
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="form-field-textarea"
                              placeholder="Describe the order details, requirements, and specifications..."
                              oninput="updateCharCount(this)"></textarea>
                    <div class="flex justify-between items-center">
                        <p class="form-field-hint">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                            Include project scope, deliverables, and special requirements
                        </p>
                        <span id="charCount" class="text-xs text-gray-400">0 characters</span>
                    </div>
                    @error('description')
                        <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status & Priority Section -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-flag bg-gradient-to-br from-blue-500 to-cyan-600 text-white"></i>
                    Status & Priority
                </h2>
            </div>
            <div class="form-card-body">
                <div class="form-grid">
                    <!-- Status -->
                    <div class="form-field">
                        <label class="form-field-label" for="status">
                            <i class="field-icon fas fa-spinner bg-blue-100 text-blue-600"></i>
                            Status <span class="required-indicator">*</span>
                        </label>
                        <select name="status" id="status" required class="form-field-select">
                            <option value="">Select Status</option>
                            <option value="nra" selected> 🤷‍♀️ NRA</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="in-progress">🔄 In Progress</option>
                            <option value="completed">✅ Completed</option>
                            <option value="on-hold">⏸️ On Hold</option>
                            <option value="cancelled">❌ Cancelled</option>
                        </select>
                        @error('status')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="form-field">
                        <label class="form-field-label" for="priority">
                            <i class="field-icon fas fa-exclamation-circle bg-orange-100 text-orange-600"></i>
                            Priority <span class="required-indicator">*</span>
                        </label>
                        <select name="priority" id="priority" required class="form-field-select">
                            <option value="low">🟢 Low Priority</option>
                            <option value="medium" selected>🟡 Medium Priority</option>
                            <option value="high">🔴 High Priority</option>
                        </select>
                        @error('priority')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule & Amount Section -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-calendar-alt bg-gradient-to-br from-green-500 to-emerald-600 text-white"></i>
                    Schedule & Amount
                </h2>
            </div>
            <div class="form-card-body space-y-4">
                <div class="form-grid">
                    <!-- Start Date -->
                    <div class="form-field">
                        <label class="form-field-label" for="start_date">
                            <i class="field-icon fas fa-play-circle bg-green-100 text-green-600"></i>
                            Start Date <span class="text-gray-400 font-normal">(when project begins)</span>
                        </label>
                        <input type="text" name="start_date" id="start_date" class="form-field-input datepicker" placeholder="Select start date" readonly onchange="calculateDuration()" value="{{ \Carbon\Carbon::today()->format('m/d/Y') }}">
                        @error('start_date')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date (Deadline) -->
                    <div class="form-field">
                        <label class="form-field-label" for="end_date">
                            <i class="field-icon fas fa-flag-checkered bg-red-100 text-red-600"></i>
                            Deadline <span class="text-gray-400 font-normal">(project completion date)</span>
                        </label>
                        <input type="text" name="end_date" id="end_date" class="form-field-input datepicker" placeholder="Select deadline" readonly onchange="calculateDuration()">
                        @error('end_date')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Duration Display -->
                <div id="durationDisplay" class="hidden">
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                        <i class="fas fa-clock text-2xl text-blue-600"></i>
                        <div>
                            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Project Duration</p>
                            <p class="text-lg font-bold text-blue-900" id="durationText">--</p>
                        </div>
                    </div>
                </div>

                <!-- Amount fields on same line -->
                <div class="form-grid">
                    <!-- Value (Before 20% Split) -->
                    <div class="form-field">
                        <label class="form-field-label" for="value">
                            <i class="field-icon fas fa-coins bg-green-100 text-green-600"></i>
                            Amount (Before Split)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                            <input type="number" name="value" id="value" step="0.01" min="0"
                                   class="form-field-input pl-10 text-lg font-semibold"
                                   placeholder="0.00"
                                   oninput="calculateRevenue(this.value)">
                        </div>
                        @error('value')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount (After 20% Split) -->
                    <div class="form-field">
                        <label class="form-field-label" for="actual_revenue">
                            <i class="field-icon fas fa-dollar-sign bg-purple-100 text-purple-600"></i>
                            Value (After Split) <span class="text-gray-400 font-normal">(auto)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                            <input type="number" name="actual_revenue" id="actual_revenue" step="0.01" min="0"
                                   class="form-field-input pl-10 text-lg font-semibold bg-gray-50"
                                   placeholder="0.00"
                                   readonly>
                        </div>
                        @error('actual_revenue')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Revenue Preview - show below both fields -->
                <div id="revenuePreview" class="revenue-preview hidden">
                    <div class="revenue-preview-label">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Amount Overview
                    </div>
                    <div class="revenue-preview-value" id="revenueTotal">$0.00</div>
                    <div class="revenue-preview-split">
                        <i class="fas fa-coins mr-2"></i>
                        <span>Value: <strong id="revenueValue">$0.00</strong></span>
                    </div>
                    <div class="revenue-preview-split">
                        <i class="fas fa-percentage mr-2"></i>
                        <span>20% Fee: <strong id="revenueFee">$0.00</strong></span>
                    </div>
                    <div class="revenue-preview-split">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        <span>Amount (After Split): <strong class="text-green-600" id="revenueSplit">$0.00</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-users bg-gradient-to-br from-indigo-500 to-purple-600 text-white"></i>
                    Assignments
                </h2>
            </div>
            <div class="form-card-body space-y-4">
                <!-- Team, Tech Stack, Freelance Profile on same line (3 columns) -->
                @if(!$isTeamLeader ?? false)
                <!-- For admins/managers: 3 columns -->
                <div class="grid grid-cols-3 gap-4">
                    <!-- Team -->
                    <div class="form-field">
                        <label class="form-field-label" for="team_id">
                            <i class="field-icon fas fa-users bg-indigo-100 text-indigo-600"></i>
                            Team
                        </label>
                        <select name="team_id" id="team_id" class="form-field-select">
                            <option value="">Select Team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('team_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tech Stack -->
                    <div class="form-field">
                        <label class="form-field-label" for="tech_stack_id">
                            <i class="field-icon fas fa-layer-group bg-indigo-100 text-indigo-600"></i>
                            Tech Stack
                        </label>
                        <select name="tech_stack_id" id="tech_stack_id" class="form-field-select">
                            <option value="">Select Tech Stack</option>
                            @foreach($techStacks as $stack)
                                <option value="{{ $stack->id }}">{{ $stack->name }}</option>
                            @endforeach
                        </select>
                        @error('tech_stack_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Freelance Profile -->
                    <div class="form-field">
                        <label class="form-field-label" for="freelance_profile_id">
                            <i class="field-icon fas fa-address-card bg-purple-100 text-purple-600"></i>
                            Freelancer
                        </label>
                        <select name="freelance_profile_id" id="freelance_profile_id" class="form-field-select">
                            <option value="">Select Profile</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}">{{ $profile->profile_name }}</option>
                            @endforeach
                        </select>
                        @error('freelance_profile_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @else
                <!-- For team leaders: 2 columns (no team field) -->
                <input type="hidden" name="team_id" value="{{ auth()->user()->team_id }}">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Tech Stack -->
                    <div class="form-field">
                        <label class="form-field-label" for="tech_stack_id">
                            <i class="field-icon fas fa-layer-group bg-indigo-100 text-indigo-600"></i>
                            Tech Stack
                        </label>
                        <select name="tech_stack_id" id="tech_stack_id" class="form-field-select">
                            <option value="">Select Tech Stack</option>
                            @foreach($techStacks as $stack)
                                <option value="{{ $stack->id }}">{{ $stack->name }}</option>
                            @endforeach
                        </select>
                        @error('tech_stack_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Freelance Profile -->
                    <div class="form-field">
                        <label class="form-field-label" for="freelance_profile_id">
                            <i class="field-icon fas fa-address-card bg-purple-100 text-purple-600"></i>
                            Freelancer
                        </label>
                        <select name="freelance_profile_id" id="freelance_profile_id" class="form-field-select">
                            <option value="">Select Profile</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}">{{ $profile->profile_name }}</option>
                            @endforeach
                        </select>
                        @error('freelance_profile_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endif

                <!-- Team Members -->
                <div class="form-field">
                    <label class="form-field-label">
                        <i class="field-icon fas fa-user-friends bg-indigo-100 text-indigo-600"></i>
                        Assign Team Members
                        <span class="ml-auto text-xs font-normal text-gray-500">
                            <span id="selectedCount">0</span> selected
                        </span>
                    </label>
                    <p class="form-field-hint mb-3">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        @if($isTeamLeader ?? false)
                            Search to find team members (initially showing latest 10)
                        @else
                            Optional - You can assign members from multiple teams
                        @endif
                    </p>

                    <!-- Search Input -->
                    <div class="relative mb-3">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="userSearch" placeholder="Search by name or email..."
                               class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                               autocomplete="off">
                        <div id="searchSpinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </div>
                    </div>

                    <div class="checkbox-list" id="userList">
                        @forelse($users as $user)
                            <label class="checkbox-item-card mb-2 user-item" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->employee_id ?? '')) }}" data-user-id="{{ $user->id }}">
                                <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                       onchange="updateSelectedCount()">
                                <div class="flex items-center justify-between w-full">
                                    <div class="ml-3 flex-1 flex items-center">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']))
                                    <div class="flex items-center gap-4 text-right text-sm text-gray-600 ml-4">
                                        @if($user->employee_id)
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-id-badge text-gray-400"></i>
                                            <span class="font-mono text-xs">{{ $user->employee_id }}</span>
                                        </div>
                                        @endif
                                        @if($user->team)
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-users text-gray-400"></i>
                                            <span class="text-xs">{{ $user->team->name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">
                                <i class="fas fa-users-slash mr-2"></i>
                                No team members available
                            </p>
                        @endforelse
                        <p id="noResults" class="text-sm text-gray-500 text-center py-4 hidden">
                            <i class="fas fa-search mr-2"></i>
                            No users found matching your search
                        </p>
                        <p id="searchMore" class="text-sm text-gray-500 text-center py-4 hidden">
                            <i class="fas fa-info-circle mr-2"></i>
                            Search for more team members...
                        </p>
                    </div>
                    @error('users')
                        <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-bar">
            <div class="flex items-center justify-between">
                <a href="{{ route('orders.index') }}" class="btn-cancel">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" id="submitBtn" class="btn-submit">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Create Order
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Character counter for description
function updateCharCount(textarea) {
    const count = textarea.value.length;
    const counter = document.getElementById('charCount');
    counter.textContent = count + ' character' + (count !== 1 ? 's' : '');
}

// Revenue calculation
function calculateRevenue(value) {
    const revenue = parseFloat(value) || 0;
    const preview = document.getElementById('revenuePreview');
    const total = document.getElementById('revenueTotal');
    const valueDisplay = document.getElementById('revenueValue');
    const fee = document.getElementById('revenueFee');
    const split = document.getElementById('revenueSplit');
    const actualRevenueInput = document.getElementById('actual_revenue');

    if (revenue > 0) {
        preview.classList.remove('hidden');
        const feeAmount = revenue * 0.20;
        const afterSplit = revenue * 0.80;
        total.textContent = '$' + revenue.toFixed(2);
        valueDisplay.textContent = '$' + revenue.toFixed(2);
        fee.textContent = '$' + feeAmount.toFixed(2);
        split.textContent = '$' + afterSplit.toFixed(2);
        actualRevenueInput.value = afterSplit.toFixed(2);
    } else {
        preview.classList.add('hidden');
        actualRevenueInput.value = '';
    }
}

// Calculate project duration
function calculateDuration() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const durationDisplay = document.getElementById('durationDisplay');
    const durationText = document.getElementById('durationText');

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 0) {
            durationDisplay.classList.remove('hidden');

            let durationString = '';
            if (diffDays === 1) {
                durationString = '1 day';
            } else if (diffDays < 7) {
                durationString = diffDays + ' days';
            } else if (diffDays < 30) {
                const weeks = Math.floor(diffDays / 7);
                const remainingDays = diffDays % 7;
                durationString = weeks + ' week' + (weeks > 1 ? 's' : '');
                if (remainingDays > 0) {
                    durationString += ' ' + remainingDays + ' day' + (remainingDays > 1 ? 's' : '');
                }
            } else {
                const months = Math.floor(diffDays / 30);
                const remainingDays = diffDays % 30;
                durationString = months + ' month' + (months > 1 ? 's' : '');
                if (remainingDays > 0 && remainingDays < 7) {
                    durationString += ' ' + remainingDays + ' day' + (remainingDays > 1 ? 's' : '');
                } else if (remainingDays >= 7) {
                    const weeks = Math.floor(remainingDays / 7);
                    durationString += ' ' + weeks + ' week' + (weeks > 1 ? 's' : '');
                }
            }

            durationText.textContent = durationString + ' (' + diffDays + ' days total)';
        } else {
            durationDisplay.classList.add('hidden');
        }
    } else {
        durationDisplay.classList.add('hidden');
    }
}

// Update selected count for team members
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="users[]"]:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
}

// Search functionality for users with AJAX support
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const userList = document.getElementById('userList');
    const noResults = document.getElementById('noResults');
    const searchMore = document.getElementById('searchMore');
    const searchSpinner = document.getElementById('searchSpinner');
    const isTeamLeader = {{ $isTeamLeader ?? 'false' }};
    let searchTimeout;

    // Store original users for client-side filtering (non-team-leaders)
    const originalUserItems = Array.from(userList.querySelectorAll('.user-item'));
    const loadedUserIds = new Set(Array.from(originalUserItems).map(item => item.getAttribute('data-user-id')));

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (searchTerm === '') {
            // Show original users when search is cleared
            userList.querySelectorAll('.user-item').forEach(item => item.remove());
            originalUserItems.forEach(item => {
                if (!loadedUserIds.has(item.getAttribute('data-user-id'))) {
                    userList.appendChild(item);
                }
            });
            originalUserItems.forEach(item => {
                if (item.parentNode !== userList) {
                    userList.appendChild(item);
                }
            });
            noResults.classList.add('hidden');
            searchMore.classList.add('hidden');
            return;
        }

        // For team leaders, use AJAX search
        if (isTeamLeader) {
            searchSpinner.classList.remove('hidden');
            searchMore.classList.add('hidden');

            searchTimeout = setTimeout(function() {
                fetch(`{{ route('orders.search-members') }}?q=${encodeURIComponent(searchTerm)}&team_id={{ auth()->user()->team_id }}`)
                    .then(response => response.json())
                    .then(data => {
                        searchSpinner.classList.add('hidden');

                        // Remove all current user items
                        userList.querySelectorAll('.user-item').forEach(item => item.remove());

                        if (data.results.length > 0) {
                            // Add searched users
                            data.results.forEach(user => {
                                // Check if user is already loaded
                                if (!loadedUserIds.has(user.id.toString())) {
                                    const userItem = createUserItem(user);
                                    userList.appendChild(userItem);
                                    loadedUserIds.add(user.id.toString());
                                }
                            });

                            // Show all matching users
                            const allUserItems = userList.querySelectorAll('.user-item');
                            let visibleCount = 0;
                            allUserItems.forEach(item => {
                                const searchData = item.getAttribute('data-search');
                                if (searchData.includes(searchTerm)) {
                                    item.style.display = '';
                                    visibleCount++;
                                } else {
                                    item.style.display = 'none';
                                }
                            });

                            if (visibleCount === 0) {
                                noResults.classList.remove('hidden');
                            } else {
                                noResults.classList.add('hidden');
                            }
                        } else {
                            noResults.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error searching users:', error);
                        searchSpinner.classList.add('hidden');
                    });
            }, 300); // Debounce for 300ms
        } else {
            // For managers, use client-side filtering
            let visibleCount = 0;
            const userItems = userList.querySelectorAll('.user-item');

            userItems.forEach(function(item) {
                const searchData = item.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    });

    // Helper function to create user item
    function createUserItem(user) {
        const label = document.createElement('label');
        label.className = 'checkbox-item-card mb-2 user-item';
        const searchTerm = (user.text + ' ' + (user.employee_id || '')).toLowerCase();
        label.setAttribute('data-search', searchTerm);
        label.setAttribute('data-user-id', user.id);

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'users[]';
        checkbox.value = user.id;
        checkbox.onchange = updateSelectedCount;

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center justify-between w-full';

        const leftContainer = document.createElement('div');
        leftContainer.className = 'ml-3 flex-1 flex items-center';

        const avatar = document.createElement('div');
        avatar.className = 'user-avatar';
        avatar.textContent = user.text.charAt(0).toUpperCase();

        const info = document.createElement('div');
        info.className = 'user-info';

        const name = document.createElement('div');
        name.className = 'user-name';
        name.textContent = user.text.split(' (')[0];

        const email = document.createElement('div');
        email.className = 'user-email';
        email.textContent = user.text.match(/\(([^)]+)\)/)?.[1] || '';

        info.appendChild(name);
        info.appendChild(email);
        leftContainer.appendChild(avatar);
        leftContainer.appendChild(info);
        wrapper.appendChild(leftContainer);

        // Add right-side info for admins/managers
        @if(auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']))
        if (user.employee_id || user.team_name) {
            const rightInfo = document.createElement('div');
            rightInfo.className = 'flex items-center gap-4 text-right text-sm text-gray-600 ml-4';

            if (user.employee_id) {
                const empIdDiv = document.createElement('div');
                empIdDiv.className = 'flex items-center gap-1';
                empIdDiv.innerHTML = '<i class="fas fa-id-badge text-gray-400"></i><span class="font-mono text-xs">' + user.employee_id + '</span>';
                rightInfo.appendChild(empIdDiv);
            }

            if (user.team_name) {
                const teamDiv = document.createElement('div');
                teamDiv.className = 'flex items-center gap-1';
                teamDiv.innerHTML = '<i class="fas fa-users text-gray-400"></i><span class="text-xs">' + user.team_name + '</span>';
                rightInfo.appendChild(teamDiv);
            }

            wrapper.appendChild(rightInfo);
        }
        @endif

        label.appendChild(checkbox);
        label.appendChild(wrapper);

        return label;
    }
});

// Form validation feedback
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Order...';
    submitBtn.disabled = true;
});
</script>
@endpush
@endsection
