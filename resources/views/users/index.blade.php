@extends('layouts.dashboard')

@push('styles')
    <style>
        /* Custom DataTables Styling */
        .dataTables_wrapper {
            padding: 1.5rem;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_length label {
            color: #374151;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_length select:hover {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            display: flex;
            justify-content: flex-end;
        }

        .dataTables_wrapper .dataTables_filter label {
            color: #374151;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            width: 300px;
            transition: all 0.2s;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3e%3ccircle cx='11' cy='11' r='8' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3cpath d='m21 21-4.35-4.35' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 0.75rem center;
            background-size: 1rem 1rem;
            padding-left: 2.5rem;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3e%3ccircle cx='11' cy='11' r='8' stroke='%23636f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3cpath d='m21 21-4.35-4.35' stroke='%23636f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-color: #f9fafb;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.75rem 1rem;
            margin: 0;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-color: #d1d5db;
            color: #374151;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #6366f1 0%, #5558dd 100%);
            border-color: #6366f1;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #5558dd 0%, #4c51bf 100%);
            border-color: #4c51bf;
            box-shadow: 0 6px 16px rgba(85, 83, 221, 0.4);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.first,
        .dataTables_wrapper .dataTables_paginate .paginate_button.last {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            padding: 0.75rem 1.25rem;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.ellipsis {
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: #6b7280;
            cursor: default;
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.ellipsis:hover {
            background: transparent;
            border: none;
            color: #6b7280;
            transform: none;
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: white;
            border-color: #e5e7eb;
            color: #6b7280;
            transform: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Pagination icons for previous/next buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous::before,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next::before {
            display: inline-block;
            margin-right: 0.25rem;
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous::before {
            content: '\f053';
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.next::before {
            content: '\f054';
        }

        /* Table styling */
        .display.dataTable {
            border-collapse: collapse;
        }

        .display.dataTable th {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            color: #374151;
            font-weight: 600;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
        }

        .display.dataTable td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-size: 0.875rem;
        }

        .display.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Processing overlay */
        .dataTables_processing {
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem 2rem;
            font-weight: 600;
            color: #374151;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* ===== Dark Mode: DataTables chrome ===== */
        /* This build is DataTables 2 + the Bootstrap 5 integration (dt-length,
           dt-search, dt-info, dt-paging / .page-link), not the classic 1.x
           dataTables_* class names the rules above target — those rules are
           inert on this table. Dark mode is added against the real classes,
           keyed off the `dark` class on <html>. */
        .dark #usersTable_wrapper {
            color: #e5e7eb;
        }

        .dark #usersTable_wrapper .dt-length select,
        .dark #usersTable_wrapper .dt-search input {
            border-color: #374151;
            color: #e5e7eb;
            background-color: #1f2937;
        }

        .dark #usersTable_wrapper .dt-length select:hover,
        .dark #usersTable_wrapper .dt-length select:focus,
        .dark #usersTable_wrapper .dt-search input:hover,
        .dark #usersTable_wrapper .dt-search input:focus {
            border-color: #818cf8;
        }

        .dark #usersTable_wrapper .dt-length label,
        .dark #usersTable_wrapper .dt-search label,
        .dark #usersTable_wrapper .dt-info {
            color: #9ca3af;
        }

        .dark #usersTable_wrapper .dt-paging {
            background: transparent !important;
        }

        .dark #usersTable_wrapper .dt-paging .page-link {
            background-color: #1f2937;
            border-color: #374151;
            color: #e5e7eb;
        }

        .dark #usersTable_wrapper .dt-paging .page-link:hover {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .dark #usersTable_wrapper .dt-paging .page-item.active .page-link {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

        .dark #usersTable_wrapper .dt-paging .page-item.disabled .page-link {
            background-color: #111827;
            border-color: #374151;
            color: #4b5563;
        }

        .dark #usersTable.dataTable {
            color: #e5e7eb;
            border-color: #374151;
        }

        .dark #usersTable.dataTable thead th {
            background-color: #1f2937;
            color: #9ca3af;
            border-bottom-color: #374151;
        }

        .dark #usersTable.dataTable tbody tr {
            background-color: #111827;
        }

        .dark #usersTable.dataTable tbody tr:hover {
            background-color: #1f2937;
        }

        .dark #usersTable.dataTable tbody td {
            border-bottom-color: #1f2937;
            color: #e5e7eb;
        }

        .dark .dataTables_processing {
            background-color: rgba(31, 41, 55, 0.95);
            border-color: #374151;
            color: #e5e7eb;
        }
    </style>
@endpush

@section('content')
    <div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
        <!-- Page Header -->
        <div class="mb-4">
            <div class="mb-4">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">User Management</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Manage users, roles, and permissions</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-4 ">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class="fas fa-filter text-indigo-500 mr-2"></i>
                    Filters
                </h2>
                <!-- Toggle Switch -->
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Basic</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="advancedToggle" class="sr-only peer" onchange="toggleFilters()">
                        <div
                            class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                        </div>
                    </label>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Advanced</span>
                </div>
            </div>

            <!-- Basic Search (Always Visible) -->
            <form id="filterForm" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-search mr-1 text-indigo-500"></i>Search Users
                        </label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Search by name, email, employee ID..."
                                class="w-full px-3 py-2 pl-10 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all duration-200 text-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Status Filter -->
                    <div>
                        <label for="quick_status" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-toggle-on mr-1 text-indigo-500"></i>Status
                        </label>
                        <div class="relative">
                            <select id="quick_status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex items-end space-x-2">
                        <button type="button" onclick="applyFilters()"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-search mr-1"></i>
                            Search
                        </button>
                        <button type="button" onclick="clearFilters()"
                            class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters (Hidden by default) -->
                <div id="advancedFilters"
                    class="overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0">
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Employee ID Filter -->
                            <div>
                                <label for="employee_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-id-badge mr-1 text-indigo-500"></i>Employee ID
                                </label>
                                <div class="relative">
                                    <input type="text" id="employee_id" name="employee_id"
                                        value="{{ request('employee_id') }}" placeholder="Search by employee ID..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all duration-200 text-sm">
                                </div>
                            </div>

                            <!-- Role Filter -->
                            <div>
                                <label for="role" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-user-tag mr-1 text-indigo-500"></i>Role
                                </label>
                                <div class="relative">
                                    <select id="role" name="role"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                                        <option value="">All Roles</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ request('role') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Filter -->
                            <div>
                                <label for="team_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-users mr-1 text-indigo-500"></i>Team
                                </label>
                                <div class="relative">
                                    <select id="team_id" name="team_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                                        <option value="">All Teams</option>
                                        @foreach ($teams as $team)
                                            <option value="{{ $team->id }}"
                                                {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Tech Stack Filter -->
                            <div>
                                <label for="tech_stack_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-code mr-1 text-indigo-500"></i>Tech Stack
                                </label>
                                <div class="relative">
                                    <select id="tech_stack_id" name="tech_stack_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                                        <option value="">All Tech Stacks</option>
                                        @foreach ($techStacks as $techStack)
                                            <option value="{{ $techStack->id }}"
                                                {{ request('tech_stack_id') == $techStack->id ? 'selected' : '' }}>
                                                {{ $techStack->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label for="department" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-building mr-1 text-indigo-500"></i>Department
                                </label>
                                <div class="relative">
                                    <select id="department" name="department"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department }}"
                                                {{ request('department') == $department ? 'selected' : '' }}>
                                                {{ $department }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>

    <!-- Bulk Operations Section -->
    <div id="bulkOperations"
        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-500/10 dark:to-indigo-500/10 border border-blue-200 dark:border-blue-900 rounded-xl p-4 mb-6 hidden">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-blue-900 dark:text-blue-300">
                    <span id="selectedCount">0</span> users selected
                </span>
                <button onclick="clearSelection()" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    Clear Selection
                </button>
            </div>
            <div class="flex flex-wrap gap-3">
                <!-- Bulk Role Assignment -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-blue-900 dark:text-blue-300">Assign Role:</label>
                    <select id="bulkRole"
                        class="px-3 py-1 text-sm border border-blue-300 dark:border-blue-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulk Team Assignment -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-blue-900 dark:text-blue-300">Assign Team:</label>
                    <select id="bulkTeam"
                        class="px-3 py-1 text-sm border border-blue-300 dark:border-blue-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <option value="">Select Team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Apply Bulk Changes Button -->
                <button onclick="applyBulkOperations()"
                    class="px-4 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-check mr-1"></i> Apply
                </button>

                <!-- Delete Selected Button -->
                <button onclick="deleteSelectedUsers()"
                    class="px-4 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>

    
    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden datatableprimary mb-5">
        <div class="overflow-x-auto">
            <table class="display w-full" id="usersTable" style="width:100%">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" id="selectAll"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee
                            ID</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Position
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Team
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tech
                            Stack</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created
                        </th>
                        <th class="px-4 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Table rows will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple DataTables initialization with jQuery from CDN
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 Initializing users DataTable...');

                // Check if jQuery is loaded, if not load from CDN
                if (typeof $ === 'undefined') {
                    console.log('Loading jQuery from CDN...');
                    const script = document.createElement('script');
                    script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                    script.onload = initializeDataTable;
                    document.head.appendChild(script);
                } else {
                    initializeDataTable();
                }
            });

            function initializeDataTable() {
                // Load DataTables CSS if not already loaded
                if (!document.querySelector('link[href*="dataTables"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css';
                    document.head.appendChild(link);
                }

                // Load DataTables JS if not available
                if (typeof $.fn.DataTable === 'undefined') {
                    console.log('Loading DataTables from CDN...');
                    const script = document.createElement('script');
                    script.src = 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js';
                    script.onload = createDataTable;
                    document.head.appendChild(script);
                } else {
                    createDataTable();
                }
            }

            function createDataTable() {
                console.log('Creating DataTable...');

                // Initialize DataTable
                $('#usersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('users.data') }}',
                        type: 'GET',
                        data: function(d) {
                            // Add custom filters from form
                            d.search = $('#search').val();
                            d.employee_id = $('#employee_id').val();
                            d.role = $('#role').val();
                            d.team_id = $('#team_id').val();
                            d.tech_stack_id = $('#tech_stack_id').val();
                            d.status = $('#quick_status').val();
                            d.department = $('#department').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row, meta) {
                                return '<input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' +
                                    data + '">';
                            }
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'employee_id',
                            name: 'employee_id'
                        },
                        {
                            data: 'position',
                            name: 'position'
                        },
                        {
                            data: 'team',
                            name: 'team.name',
                            orderable: false
                        },
                        {
                            data: 'tech_stack',
                            name: 'techStack.name',
                            orderable: false
                        },
                        {
                            data: 'role',
                            name: 'roles.name',
                            orderable: false
                        },
                        {
                            data: 'status',
                            name: 'is_active',
                            orderable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ], // Sort by name by default
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    responsive: true,
                    language: {
                        processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...',
                        emptyTable: 'No users found',
                        zeroRecords: 'No matching users found',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        search: 'Search:',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        }
                    },
                    dom: 'lfrtip',
                    initComplete: function() {
                        console.log('✅ DataTable initialized successfully');

                        // Add action buttons above the table
                        const actionButtons = `
                            <div class="flex flex-wrap items-center justify-end gap-2 mb-4 createbutton">
                                <a href="{{ route('users.bulk-import.form') }}"
                                    class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                    <i class="fas fa-file-import mr-1.5 text-xs"></i>
                                    Bulk Import
                                </a>
                                <button onclick="exportSelectedUsers()"
                                    class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                    <i class="fas fa-file-export mr-1.5 text-xs"></i>
                                    Export
                                </button>
                                <a href="{{ route('users.create') }}"
                                    class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                    <i class="fas fa-user-plus mr-1.5 text-xs"></i>
                                    Add New User
                                </a>
                            </div>
                        `;

                        // Insert buttons before the table container
                        $('#usersTable_wrapper').prepend(actionButtons);

                        // Filter form handlers - real-time search
                        $('#filterForm input[type="text"], #filterForm select').on('input change', function() {
                            if ($(this).attr('id') === 'search') {
                                // For search input, add a small delay to prevent too many requests
                                clearTimeout(window.searchTimeout);
                                window.searchTimeout = setTimeout(function() {
                                    $('#usersTable').DataTable().ajax.reload();
                                }, 500);
                            } else {
                                // For other filters, reload immediately
                                $('#usersTable').DataTable().ajax.reload();
                            }
                        });

                        // Handle Enter key on search input
                        $('#search').on('keypress', function(e) {
                            if (e.which === 13) {
                                clearTimeout(window.searchTimeout);
                                $('#usersTable').DataTable().ajax.reload();
                            }
                        });

                        // Select all functionality
                        $('#selectAll').change(function() {
                            const isChecked = $(this).prop('checked');
                            $('input[type="checkbox"][value]').prop('checked', isChecked);
                            updateBulkSelection();
                        });

                        // Individual checkbox handlers
                        $(document).on('change', 'input[type="checkbox"][value]', function() {
                            updateBulkSelection();
                            // Update select all checkbox state
                            const allCheckboxes = $('input[type="checkbox"][value]');
                            const checkedCheckboxes = $('input[type="checkbox"][value]:checked');
                            $('#selectAll').prop('checked', allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0);
                        });

                        // Status switcher functionality
                        $(document).on('change', '.status-switcher', function() {
                            const checkbox = $(this);
                            const userId = checkbox.data('user-id');
                            const userName = checkbox.data('user-name');
                            const isActive = checkbox.is(':checked');

                            if (userId === '{{ auth()->id() }}') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cannot Change Status',
                                    text: 'You cannot change your own account status.',
                                    confirmButtonColor: '#ef4444'
                                });
                                checkbox.prop('checked', !isActive); // Revert the change
                                return;
                            }

                            Swal.fire({
                                title: 'Change User Status',
                                html: `Are you sure you want to <strong>${isActive ? 'activate' : 'deactivate'}</strong> <br><strong>${userName}</strong>?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: isActive ? '#10b981' : '#ef4444',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: `Yes, ${isActive ? 'activate' : 'deactivate'}!`,
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    axios.post(`/users/${userId}/toggle-status`)
                                        .then(response => {
                                            if (response.data.success) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    text: response.data.message,
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                });
                                                // Reload table to update the status display
                                                $('#usersTable').DataTable().ajax.reload();
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: response.data.message ||
                                                        'Failed to change user status.',
                                                    confirmButtonColor: '#ef4444'
                                                });
                                                checkbox.prop('checked', !
                                                    isActive); // Revert the change
                                            }
                                        })
                                        .catch(error => {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'An error occurred while changing user status.',
                                                confirmButtonColor: '#ef4444'
                                            });
                                            checkbox.prop('checked', !
                                                isActive); // Revert the change
                                        });
                                } else {
                                    checkbox.prop('checked', !
                                        isActive); // Revert the change if cancelled
                                }
                            });
                        });
                    }
                });
            }

            // Global functions for onclick handlers
            function toggleFilters() {
                const advancedFilters = $('#advancedFilters');
                const toggle = $('#advancedToggle');

                if (toggle.prop('checked')) {
                    // Show advanced filters with smooth animation
                    advancedFilters.removeClass('max-h-0 opacity-0');
                    advancedFilters.addClass('max-h-96 opacity-100');
                } else {
                    // Hide advanced filters with smooth animation
                    advancedFilters.removeClass('max-h-96 opacity-100');
                    advancedFilters.addClass('max-h-0 opacity-0');
                }
            }

            function updateBulkSelection() {
                const checkboxes = $('input[type="checkbox"][value]:checked');
                const count = checkboxes.length;
                const bulkOperations = $('#bulkOperations');

                if (count > 0) {
                    bulkOperations.removeClass('hidden');
                    $('#selectedCount').text(count);
                } else {
                    bulkOperations.addClass('hidden');
                }
            }

            function clearSelection() {
                $('input[type="checkbox"][value]').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateBulkSelection();
            }

            function applyFilters() {
                // Clear any existing search timeout
                if (window.searchTimeout) {
                    clearTimeout(window.searchTimeout);
                }

                // Reload the table with current filter values
                if (typeof $('#usersTable').DataTable() !== 'undefined') {
                    // Show loading state (optional)
                    console.log('Applying filters...');

                    // Reload table data
                    $('#usersTable').DataTable().ajax.reload(null, false); // false = no reset paging

                    console.log('Filters applied successfully');
                }
            }

            function clearFilters() {
                // Clear all filter values
                $('#filterForm')[0].reset();

                // Hide advanced filters if visible
                const advancedFilters = $('#advancedFilters');
                const toggle = $('#advancedToggle');

                if (toggle.prop('checked')) {
                    toggle.prop('checked', false);
                    advancedFilters.removeClass('max-h-96 opacity-100');
                    advancedFilters.addClass('max-h-0 opacity-0');
                }

                // Clear any existing search timeout
                if (window.searchTimeout) {
                    clearTimeout(window.searchTimeout);
                }

                // Reload the table
                if (typeof $('#usersTable').DataTable() !== 'undefined') {
                    $('#usersTable').DataTable().ajax.reload();
                }

                // Show success feedback
                console.log('Filters cleared successfully');
            }

            function deleteUser(userId, userName) {
                Swal.fire({
                    title: 'Delete User',
                    text: `Are you sure you want to delete ${userName}? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete('/users/' + userId, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.data.message,
                                    'success'
                                );
                                if (typeof $('#usersTable').DataTable() !== 'undefined') {
                                    $('#usersTable').DataTable().ajax.reload();
                                }
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            const errorMessage = error.response?.data?.message ||
                                                 error.response?.data?.error ||
                                                 error.message ||
                                                 'An error occurred while deleting the user';
                            Swal.fire(
                                'Error',
                                errorMessage,
                                'error'
                            );
                        });
                    }
                });
            }

            function toggleUserStatus(userId, userName, status) {
                const action = status ? 'activate' : 'deactivate';
                Swal.fire({
                    title: `${action.charAt(0).toUpperCase() + action.slice(1)} User`,
                    text: `Are you sure you want to ${action} ${userName}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: status ? '#3085d6' : '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Yes, ${action}!`,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/users/' + userId + '/toggle-status', {}, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire(
                                    'Success!',
                                    response.data.message,
                                    'success'
                                );
                                if (typeof $('#usersTable').DataTable() !== 'undefined') {
                                    $('#usersTable').DataTable().ajax.reload();
                                }
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Toggle status error:', error);
                            const errorMessage = error.response?.data?.message ||
                                                 error.response?.data?.error ||
                                                 error.message ||
                                                 'An error occurred while updating user status';
                            Swal.fire(
                                'Error',
                                errorMessage,
                                'error'
                            );
                        });
                    }
                });
            }

            function applyBulkOperations() {
                const selectedUsers = [];
                $('input[type="checkbox"][value]:checked').each(function() {
                    selectedUsers.push($(this).val());
                });

                if (selectedUsers.length === 0) {
                    Swal.fire(
                        'No Selection',
                        'Please select at least one user.',
                        'warning'
                    );
                    return;
                }

                const role = $('#bulkRole').val();
                const team = $('#bulkTeam').val();

                if (!role && !team) {
                    Swal.fire(
                        'No Selection',
                        'Please select a role or team to assign.',
                        'warning'
                    );
                    return;
                }

                const operations = [];
                if (role) operations.push('assign_role');
                if (team) operations.push('assign_team');

                operations.forEach(operation => {
                    const data = {
                        user_ids: selectedUsers,
                        operation: operation
                    };

                    if (operation === 'assign_role') {
                        data.role = role;
                    } else if (operation === 'assign_team') {
                        data.team_id = team;
                    }

                    axios.post('/users/bulk-operation', data, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire(
                                'Success!',
                                response.data.message,
                                'success'
                            );
                            clearSelection();
                            if (typeof $('#usersTable').DataTable() !== 'undefined') {
                                $('#usersTable').DataTable().ajax.reload();
                            }
                        } else {
                            Swal.fire(
                                'Error',
                                response.data.message,
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Bulk operation error:', error);
                        const errorMessage = error.response?.data?.message ||
                                             error.response?.data?.error ||
                                             error.message ||
                                             'An error occurred while applying bulk operations';
                        Swal.fire(
                            'Error',
                            errorMessage,
                            'error'
                        );
                    });
                });
            }

            function deleteSelectedUsers() {
                const selectedUsers = [];
                $('input[type="checkbox"][value]:checked').each(function() {
                    selectedUsers.push($(this).val());
                });

                if (selectedUsers.length === 0) {
                    Swal.fire(
                        'No Selection',
                        'Please select at least one user.',
                        'warning'
                    );
                    return;
                }

                Swal.fire({
                    title: 'Delete Multiple Users',
                    text: `Are you sure you want to delete ${selectedUsers.length} user(s)? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/users/bulk-operation', {
                            user_ids: selectedUsers,
                            operation: 'delete'
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.data.message,
                                    'success'
                                );
                                clearSelection();
                                if (typeof $('#usersTable').DataTable() !== 'undefined') {
                                    $('#usersTable').DataTable().ajax.reload();
                                }
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Bulk delete error:', error);
                            const errorMessage = error.response?.data?.message ||
                                                 error.response?.data?.error ||
                                                 error.message ||
                                                 'An error occurred while deleting users';
                            Swal.fire(
                                'Error',
                                errorMessage,
                                'error'
                            );
                        });
                    }
                });
            }

            function exportSelectedUsers() {
                const selectedUsers = [];
                $('input[type="checkbox"][value]:checked').each(function() {
                    selectedUsers.push($(this).val());
                });

                const url = selectedUsers.length > 0
                    ? '/users/export?user_ids=' + selectedUsers.join(',')
                    : '/users/export';

                window.open(url, '_blank');
            }
        </script>
    @endpush
@endsection
