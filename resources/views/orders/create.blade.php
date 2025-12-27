@extends('layouts.dashboard')

@section('title', 'Create Order')

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
        <div class="form-card mb-6">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-info-circle bg-gradient-to-br from-purple-500 to-indigo-600 text-white"></i>
                    Basic Information
                </h2>
            </div>
            <div class="form-card-body space-y-6">
                <!-- Order Name -->
                <div class="form-field">
                    <label class="form-field-label" for="name">
                        <i class="field-icon fas fa-file-alt bg-purple-100 text-purple-600"></i>
                        Order Name <span class="required-indicator">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="form-field-input"
                           placeholder="Enter order name (e.g., Website Development Project)"
                           maxlength="255">
                    @error('name')
                        <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-field">
                    <label class="form-field-label" for="description">
                        <i class="field-icon fas fa-align-left bg-purple-100 text-purple-600"></i>
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
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
        <div class="form-card mb-6">
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

        <!-- Schedule & Revenue Section -->
        <div class="form-card mb-6">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-calendar-alt bg-gradient-to-br from-green-500 to-emerald-600 text-white"></i>
                    Schedule & Revenue
                </h2>
            </div>
            <div class="form-card-body space-y-6">
                <div class="form-grid">
                    <!-- Start Date -->
                    <div class="form-field">
                        <label class="form-field-label" for="start_date">
                            <i class="field-icon fas fa-play-circle bg-green-100 text-green-600"></i>
                            Start Date <span class="text-gray-400 font-normal">(when project begins)</span>
                        </label>
                        <input type="text" name="start_date" id="start_date" class="form-field-input datepicker" placeholder="Select start date" readonly onchange="calculateDuration()">
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

                <!-- Revenue -->
                <div class="form-field">
                    <label class="form-field-label" for="actual_revenue">
                        <i class="field-icon fas fa-dollar-sign bg-green-100 text-green-600"></i>
                        Revenue (Before 20% Split)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                        <input type="number" name="actual_revenue" id="actual_revenue" step="0.01" min="0"
                               class="form-field-input pl-10 text-lg font-semibold"
                               placeholder="0.00"
                               oninput="calculateRevenue(this.value)">
                    </div>

                    <!-- Revenue Preview -->
                    <div id="revenuePreview" class="revenue-preview hidden">
                        <div class="revenue-preview-label">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Revenue Overview
                        </div>
                        <div class="revenue-preview-value" id="revenueTotal">$0.00</div>
                        <div class="revenue-preview-split">
                            <i class="fas fa-coins mr-2"></i>
                            <span>After 20% Split (Will be stored): <strong id="revenueSplit">$0.00</strong></span>
                        </div>
                    </div>

                    @error('actual_revenue')
                        <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="form-card mb-6">
            <div class="form-card-header">
                <h2 class="form-card-title">
                    <i class="fas fa-users bg-gradient-to-br from-indigo-500 to-purple-600 text-white"></i>
                    Assignments
                </h2>
            </div>
            <div class="form-card-body space-y-6">
                <div class="form-grid">
                    <!-- Team -->
                    <div class="form-field">
                        <label class="form-field-label" for="team_id">
                            <i class="field-icon fas fa-users bg-indigo-100 text-indigo-600"></i>
                            Assign to Team
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
                </div>

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
                        Optional - Team leader can assign members later if needed
                    </p>

                    <!-- Search Input -->
                    <div class="relative mb-3">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="userSearch" placeholder="Search by name or email..."
                               class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                    </div>

                    <div class="checkbox-list" id="userList">
                        @forelse($users as $user)
                            <label class="checkbox-item-card mb-2" data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                                <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                       onchange="updateSelectedCount()">
                                <div class="ml-3 flex-1 flex items-center">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
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
    const split = document.getElementById('revenueSplit');

    if (revenue > 0) {
        preview.classList.remove('hidden');
        total.textContent = '$' + revenue.toFixed(2);
        split.textContent = '$' + (revenue * 0.80).toFixed(2);
    } else {
        preview.classList.add('hidden');
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

// Search functionality for users
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const userList = document.getElementById('userList');
    const noResults = document.getElementById('noResults');
    const userItems = userList.querySelectorAll('.checkbox-item-card');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

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
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });
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
