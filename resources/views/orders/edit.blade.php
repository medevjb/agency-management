@extends('layouts.dashboard')

@section('title', 'Edit Order')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Edit Order</h1>
                            <p class="mt-1 text-sm text-gray-600">Update order information and track changes</p>
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

        <!-- Current Status Display -->
        <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-3 flex items-center">
                        <i class="fas fa-shopping-cart mr-3 opacity-90"></i>
                        {{ $order->name }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="status-badge bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-flag mr-1"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="status-badge bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ ucfirst($order->priority) }} Priority
                        </span>
                        @if ($order->actual_revenue)
                            <span class="status-badge bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                ${{ number_format($order->actual_revenue, 2) }}
                            </span>
                        @endif
                        @if ($order->team)
                            <span class="status-badge bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-users mr-1"></i>
                                {{ $order->team->name }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right ml-6">
                    <div class="text-sm opacity-80 mb-1">Created by</div>
                    <div class="font-semibold text-lg">{{ $order->creator?->name ?? 'N/A' }}</div>
                    <div class="text-xs opacity-70 mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
            @csrf
            @method('PATCH')

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
                            Client Name <span class="required-indicator">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ $order->name }}" required
                            class="form-field-input" placeholder="Enter Client name" maxlength="255">
                        @error('name')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order ID -->
                    <div class="form-field">
                        <label class="form-field-label" for="order_id">
                            <i class="field-icon fas fa-hashtag bg-purple-100 text-purple-600"></i>
                            Order ID <span class="text-gray-400 font-normal">(optional custom identifier)</span>
                        </label>
                        <input type="text" name="order_id" id="order_id"
                            value="{{ old('order_id', $order->order_id) }}" class="form-field-input"
                            placeholder="Enter custom order ID (e.g., ORD-2024-001)" maxlength="255">
                        @error('order_id')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Description -->
                    <div class="form-field">
                        <label class="form-field-label" for="description">
                            <i class="field-icon fas fa-align-left bg-purple-100 text-purple-600"></i>
                            Description
                        </label>
                        <textarea name="description" id="description" rows="4" class="form-field-textarea"
                            placeholder="Describe the order details, requirements, and specifications..." oninput="updateCharCount(this)">{{ $order->description }}</textarea>
                        <div class="flex justify-between items-center">
                            <p class="form-field-hint">
                                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                                Include project scope, deliverables, and special requirements
                            </p>
                            <span id="charCount" class="text-xs text-gray-400">{{ strlen($order->description) }}
                                character{{ strlen($order->description) !== 1 ? 's' : '' }}</span>
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
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending
                                </option>
                                <option value="in-progress" {{ $order->status === 'in-progress' ? 'selected' : '' }}>🔄 In
                                    Progress</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅
                                    Completed</option>
                                <option value="on-hold" {{ $order->status === 'on-hold' ? 'selected' : '' }}>⏸️ On Hold
                                </option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌
                                    Cancelled</option>
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
                                <option value="low" {{ $order->priority === 'low' ? 'selected' : '' }}>🟢 Low Priority
                                </option>
                                <option value="medium" {{ $order->priority === 'medium' ? 'selected' : '' }}>🟡 Medium
                                    Priority</option>
                                <option value="high" {{ $order->priority === 'high' ? 'selected' : '' }}>🔴 High
                                    Priority</option>
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
                        Schedule & Amount
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
                            <input type="text" name="start_date" id="start_date"
                                value="{{ $order->start_date ? $order->start_date->format('Y-m-d') : '' }}"
                                class="form-field-input datepicker" placeholder="Select start date" readonly>
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
                            <input type="text" name="end_date" id="end_date"
                                value="{{ $order->end_date ? $order->end_date->format('Y-m-d') : '' }}"
                                class="form-field-input datepicker" placeholder="Select deadline" readonly>
                            @error('end_date')
                                <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Duration Display -->
                    @if ($order->start_date && $order->end_date)
                        <div
                            class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                            <i class="fas fa-clock text-2xl text-blue-600"></i>
                            <div>
                                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Project Duration</p>
                                <p class="text-lg font-bold text-blue-900">
                                    @php
                                        $start = $order->start_date;
                                        $end = $order->end_date;
                                        $diffDays = $start->diffInDays($end);
                                        if ($diffDays == 1) {
                                            echo '1 day';
                                        } elseif ($diffDays < 7) {
                                            echo $diffDays . ' days';
                                        } elseif ($diffDays < 30) {
                                            $weeks = floor($diffDays / 7);
                                            $remainingDays = $diffDays % 7;
                                            echo $weeks . ' week' . ($weeks > 1 ? 's' : '');
                                            if ($remainingDays > 0) {
                                                echo ' ' . $remainingDays . ' day' . ($remainingDays > 1 ? 's' : '');
                                            }
                                        } else {
                                            $months = floor($diffDays / 30);
                                            $remainingDays = $diffDays % 30;
                                            echo $months . ' month' . ($months > 1 ? 's' : '');
                                            if ($remainingDays >= 7) {
                                                $weeks = floor($remainingDays / 7);
                                                echo ' ' . $weeks . ' week' . ($weeks > 1 ? 's' : '');
                                            }
                                        }
                                        echo ' (' . $diffDays . ' days total)';
                                    @endphp
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Amount (Before 20% Split) -->
                    <div class="form-field">
                        <label class="form-field-label" for="value">
                            <i class="field-icon fas fa-coins bg-green-100 text-green-600"></i>
                            Amount (Before 20% Split)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                            <input type="number" name="value" id="value" step="0.01" min="0"
                                value="{{ old('value', $order->value) }}"
                                class="form-field-input pl-10 text-lg font-semibold" placeholder="0.00"
                                oninput="calculateRevenue(this.value)">
                        </div>
                        @error('value')
                            <p class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Value (After 20% Split) -->
                    <div class="form-field">
                        <label class="form-field-label" for="actual_revenue">
                            <i class="field-icon fas fa-dollar-sign bg-purple-100 text-purple-600"></i>
                            Value (After 20% Split) <span class="text-gray-400 font-normal">(will be calculated
                                automatically)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                            <input type="number" name="actual_revenue" id="actual_revenue" step="0.01"
                                min="0" value="{{ old('actual_revenue', $order->value) }}"
                                class="form-field-input pl-10 text-lg font-semibold bg-gray-50" placeholder="0.00"
                                oninput="calculateAmountReverse(this.value)" readonly>
                        </div>

                        <!-- Revenue Preview -->
                        <div id="revenuePreview" class="revenue-preview @if (!$order->value && !$order->actual_revenue) hidden @endif">
                            <div class="revenue-preview-label">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Amount Overview
                            </div>
                            <div class="revenue-preview-value" id="revenueTotal">
                                ${{ number_format($order->value ?: $order->value / 0.8, 2) }}</div>
                            <div class="revenue-preview-split">
                                <i class="fas fa-coins mr-2"></i>
                                <span>Value: <strong
                                        id="revenueValue">${{ number_format($order->value ?: $order->actual_revenue / 0.8, 2) }}</strong></span>
                            </div>
                            <div class="revenue-preview-split">
                                <i class="fas fa-percentage mr-2"></i>
                                <span>20% Fee: <strong
                                        id="revenueFee">${{ number_format(($order->value ?: $order->actual_revenue / 0.8) * 0.2, 2) }}</strong></span>
                            </div>
                            <div class="revenue-preview-split">
                                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                <span>Amount (After Split): <strong class="text-green-600"
                                        id="revenueSplit">${{ number_format($order->actual_revenue, 2) }}</strong></span>
                            </div>
                        </div>

                        @if ($order->actual_revenue)
                            <p class="form-field-hint mt-2">
                                <i class="fas fa-info-circle"></i>
                                Currently stored in database (after 20% split):
                                <strong>${{ number_format($order->actual_revenue, 2) }}</strong>
                            </p>
                        @else
                            <p class="form-field-hint mt-2">
                                <i class="fas fa-info-circle"></i>
                                20% split will be calculated automatically (e.g., $1000 value → $800 amount)
                            </p>
                        @endif

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
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}"
                                        {{ $order->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
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
                                @foreach ($techStacks as $stack)
                                    <option value="{{ $stack->id }}"
                                        {{ $order->tech_stack_id == $stack->id ? 'selected' : '' }}>{{ $stack->name }}
                                    </option>
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
                                Freelance Profile
                            </label>
                            <select name="freelance_profile_id" id="freelance_profile_id" class="form-field-select">
                                <option value="">Select Freelance Profile</option>
                                @foreach ($profiles as $profile)
                                    <option value="{{ $profile->id }}"
                                        {{ $order->freelance_profile_id == $profile->id ? 'selected' : '' }}>
                                        {{ $profile->profile_name }}</option>
                                @endforeach
                            </select>
                            @error('freelance_profile_id')
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
                                <span id="selectedCount">{{ $order->users->count() }}</span> selected
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
                                <label class="checkbox-item-card mb-2"
                                    data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                        @if ($order->users->contains($user->id)) checked @endif onchange="updateSelectedCount()">
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

            <!-- Change Notes Section -->
            <div class="form-card mb-6">
                <div class="form-card-header">
                    <h2 class="form-card-title">
                        <i class="fas fa-sticky-note bg-gradient-to-br from-yellow-500 to-orange-600 text-white"></i>
                        Change Notes
                    </h2>
                </div>
                <div class="form-card-body">
                    <div class="form-field">
                        <label class="form-field-label" for="notes">
                            <i class="field-icon fas fa-edit bg-yellow-100 text-yellow-600"></i>
                            Notes for this Update
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="form-field-textarea"
                            placeholder="Describe what you changed and why... (Optional)"></textarea>
                        <p class="form-field-hint">
                            <i class="fas fa-info-circle mr-1"></i>
                            Notes will be saved in the order history for tracking purposes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-bar">
                <div class="flex items-center justify-between">
                    <div class="flex gap-3">
                        <a href="{{ route('orders.show', $order) }}"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 font-semibold transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            View
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn-cancel">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                    <button type="submit" id="submitBtn" class="btn-submit">
                        <i class="fas fa-save mr-2"></i>
                        Update Order
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

            // Reverse calculation (from amount to value)
            function calculateAmountReverse(amount) {
                const amt = parseFloat(amount) || 0;
                if (amt > 0) {
                    const value = amt / 0.80;
                    calculateRevenue(value);
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

                // Initialize revenue preview on page load
                const revenueInput = document.getElementById('actual_revenue');
                if (revenueInput && revenueInput.value) {
                    calculateRevenue(revenueInput.value);
                }
            });

            // Form validation feedback
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating Order...';
                submitBtn.disabled = true;
            });
        </script>
    @endpush
@endsection
