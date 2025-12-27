@extends('layouts.dashboard')

@section('title', 'Orders')

@push('styles')
    <style>
        /* DataTables Pagination Styling matching users module */
        #ordersTable_wrapper .dataTables_length select {
            border-radius: 10px;
            padding: 5px 25px;
            border: 2px solid #e5e7eb;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        #ordersTable_wrapper .dataTables_length select:hover {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        #ordersTable_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        #ordersTable_wrapper .dataTables_info {
            padding: 15px 20px;
            margin: 0;
            background: #ffffff;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        #ordersTable_wrapper .dataTables_paginate {
            padding: 15px 20px;
            margin: 0;
            background: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 5px;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            margin: 0 2px !important;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%) !important;
            color: #374151 !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%) !important;
            border-color: #d1d5db !important;
            color: #374151 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #6366f1 0%, #5558dd 100%) !important;
            border-color: #6366f1 !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
            transform: translateY(-1px);
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #5558dd 0%, #4c51bf 100%) !important;
            border-color: #4c51bf !important;
            box-shadow: 0 6px 16px rgba(85, 83, 221, 0.4) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.first,
        #ordersTable_wrapper .dataTables_paginate .paginate_button.last {
            font-weight: 600;
            padding: 0.75rem 1.5rem !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.previous,
        #ordersTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 0.75rem 1.25rem !important;
            font-weight: 600;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.ellipsis {
            padding: 0.75rem 1rem !important;
            background: transparent !important;
            border: none !important;
            color: #6b7280 !important;
            cursor: default !important;
            box-shadow: none !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.ellipsis:hover {
            background: transparent !important;
            border: none !important;
            color: #6b7280 !important;
            cursor: default !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%) !important;
            border-color: #d1d5db !important;
            transform: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        
    </style>
@endpush

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage orders and track their status</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium mb-1">Total Orders</p>
                        <p class="text-2xl font-bold" id="totalOrders">0</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium mb-1">Completed</p>
                        <p class="text-2xl font-bold" id="completedOrders">0</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium mb-1">In Progress</p>
                        <p class="text-2xl font-bold" id="inProgressOrders">0</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-spinner text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium mb-1">Total Revenue</p>
                        <p class="text-xl font-bold" id="totalRevenue">$0</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters (One Line) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-3 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-filter text-indigo-500 mr-2"></i>
                    Filters:
                </span>

                <!-- Status Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterStatus"
                        class="px-3 py-1.5 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                        <option value="">All Statuses</option>
                        <option value="nra">NRA</option>
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="on-hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- Team Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterTeam"
                        class="px-3 py-1.5 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                        <option value="">All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- Tech Stack Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterTechStack"
                        class="px-3 py-1.5 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                        <option value="">All Tech Stacks</option>
                        @foreach ($techStacks as $stack)
                            <option value="{{ $stack->id }}">{{ $stack->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- Priority Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterPriority"
                        class="px-3 py-1.5 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- Spacer -->
                <div class="flex-1"></div>

                <!-- Action Buttons -->
                <button id="clearFilters"
                    class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-times mr-1"></i>
                    Clear
                </button>
                <button id="applyFilters"
                    class="px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-md">
                    <i class="fas fa-filter mr-1"></i>
                    Apply
                </button>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActions"
            class="hidden bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-md border-2 border-indigo-200 p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="selectAll"
                        class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">
                        <span id="selectedCount">0</span> orders selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <select id="bulkAction"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-sm">
                        <option value="">Bulk Actions</option>
                        <option value="mark-completed">Mark as Completed</option>
                        <option value="mark-in-progress">Mark as In Progress</option>
                        <option value="mark-pending">Mark as Pending</option>
                        <option value="mark-on-hold">Mark as On Hold</option>
                        <option value="mark-cancelled">Mark as Cancelled</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button id="applyBulkAction"
                        class="px-4 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-md">
                        <i class="fas fa-check mr-1"></i>
                        Apply
                    </button>
                    <button id="cancelSelection"
                        class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden datatableprimary mx-0">
            <div class="overflow-x-auto">
                <table id="ordersTable" class="display w-full" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" id="checkAll"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actual Revenue</th>
                            <th>Team</th>
                            <th>Tech Stack</th>
                            <th>Users</th>
                            <th>Start Date</th>
                            <th>Deadline</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // DataTables initialization matching users module
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 Initializing orders DataTable...');

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
                const table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('orders.data') }}',
                        type: 'GET',
                        data: function(d) {
                            // Add filter parameters
                            d.status = $('#filterStatus').val();
                            d.team_id = $('#filterTeam').val();
                            d.tech_stack_id = $('#filterTechStack').val();
                            d.priority = $('#filterPriority').val();
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                return `<input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" data-id="${data.id}">`;
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data) {
                                return `<span class="font-mono text-sm font-semibold text-gray-600">#${data}</span>`;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data, type, row) {
                                return `<a href="{{ route('orders.show', ':id') }}".replace(/:id/g, row.id) class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">${data}</a>`;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data) {
                                const statusConfig = {
                                    'nra': {
                                        icon: '🤷‍♀️',
                                        color: 'bg-gray-100 text-gray-800',
                                        label: 'NRA'
                                    },
                                    'pending': {
                                        icon: '⏳',
                                        color: 'bg-yellow-100 text-yellow-800',
                                        label: 'Pending'
                                    },
                                    'in-progress': {
                                        icon: '🔄',
                                        color: 'bg-blue-100 text-blue-800',
                                        label: 'In Progress'
                                    },
                                    'completed': {
                                        icon: '✅',
                                        color: 'bg-green-100 text-green-800',
                                        label: 'Completed'
                                    },
                                    'on-hold': {
                                        icon: '⏸️',
                                        color: 'bg-orange-100 text-orange-800',
                                        label: 'On Hold'
                                    },
                                    'cancelled': {
                                        icon: '❌',
                                        color: 'bg-red-100 text-red-800',
                                        label: 'Cancelled'
                                    }
                                };
                                const config = statusConfig[data] || statusConfig['nra'];
                                return `<span class="px-3 py-1.5 rounded-full text-xs font-semibold ${config.color}">${config.icon} ${config.label}</span>`;
                            }
                        },
                        {
                            data: 'priority',
                            name: 'priority',
                            render: function(data) {
                                const config = {
                                    'low': {
                                        icon: '🟢',
                                        color: 'bg-green-100 text-green-800',
                                        label: 'Low'
                                    },
                                    'medium': {
                                        icon: '🟡',
                                        color: 'bg-yellow-100 text-yellow-800',
                                        label: 'Medium'
                                    },
                                    'high': {
                                        icon: '🔴',
                                        color: 'bg-red-100 text-red-800',
                                        label: 'High'
                                    }
                                };
                                const p = config[data] || config['medium'];
                                return `<span class="px-3 py-1.5 rounded-full text-xs font-semibold ${p.color}">${p.icon} ${p.label}</span>`;
                            }
                        },
                        {
                            data: 'actual_revenue',
                            name: 'actual_revenue',
                            render: function(data) {
                                return data ? '<span class="font-semibold text-green-600">$' + parseFloat(data)
                                    .toFixed(2) + '</span>' : '<span class="text-gray-400">N/A</span>';
                            }
                        },
                        {
                            data: 'team',
                            name: 'team_id',
                            render: function(data) {
                                if (!data || data === 'N/A')
                                return '<span class="text-gray-400 text-xs">No team</span>';
                                return `<span class="px-2 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">${data}</span>`;
                            }
                        },
                        {
                            data: 'tech_stack',
                            name: 'tech_stack_id',
                            render: function(data) {
                                if (!data || data === 'N/A')
                                return '<span class="text-gray-400 text-xs">No stack</span>';
                                return `<span class="px-2 py-1 rounded-lg text-xs font-medium bg-cyan-50 text-cyan-700">${data}</span>`;
                            }
                        },
                        {
                            data: 'users',
                            name: 'users',
                            render: function(data) {
                                if (!data || data.length === 0) {
                                    return '<span class="text-gray-400 text-xs">No users</span>';
                                }
                                if (data.length === 1) {
                                    return `<span class="px-2 py-1 rounded-lg text-xs font-medium bg-purple-50 text-purple-700">${data[0].name}</span>`;
                                }
                                return `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">${data.length} users</span>`;
                            }
                        },
                        {
                            data: 'start_date',
                            name: 'start_date',
                            render: function(data) {
                                if (!data) return '<span class="text-gray-400 text-xs">Not set</span>';
                                return `<span class="text-xs font-medium text-gray-700">${data}</span>`;
                            }
                        },
                        {
                            data: 'end_date',
                            name: 'end_date',
                            render: function(data) {
                                if (!data) return '<span class="text-gray-400 text-xs">Not set</span>';

                                const deadline = new Date(data);
                                const now = new Date();
                                const diff = deadline - now;

                                if (diff <= 0) {
                                    return '<span class="px-2 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700">⚠️ Overdue</span>';
                                }

                                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                                let countdown = '';
                                if (days > 0) {
                                    countdown += days + ' Day' + (days > 1 ? 's' : '') + ' ';
                                }
                                if (hours > 0 || days > 0) {
                                    countdown += hours + ' Hour' + (hours > 1 ? 's' : '') + ' ';
                                }
                                countdown += minutes + ' Min';

                                let colorClass = 'bg-green-100 text-green-700';
                                if (days <= 1) {
                                    colorClass = 'bg-red-100 text-red-700';
                                } else if (days <= 7) {
                                    colorClass = 'bg-yellow-100 text-yellow-700';
                                } else if (days <= 30) {
                                    colorClass = 'bg-blue-100 text-blue-700';
                                }

                                return `<span class="px-2 py-1 rounded-lg text-xs font-semibold ${colorClass}">⏱️ ${countdown}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function(data) {
                                return `
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('orders.show', ':id') }}".replace(/:id/g, data.id) class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition-all" title="View">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('orders.edit', ':id') }}".replace(/:id/g, data.id) class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button onclick="deleteOrder(${data.id})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    `;
                            }
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    responsive: true,
                    language: {
                        processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...',
                        emptyTable: 'No orders found',
                        zeroRecords: 'No matching orders found',
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
                    <a href="{{ route('orders.create') }}"
                        class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                        <i class="fas fa-plus mr-1.5 text-xs"></i>
                        Add New Order
                    </a>
                </div>
            `;

                        // Insert buttons before the table container
                        $('#ordersTable_wrapper').prepend(actionButtons);

                        // Filter form handlers - real-time filter changes
                        $('#filterStatus, #filterTeam, #filterTechStack, #filterPriority').on('change', function() {
                            table.ajax.reload();
                        });
                    },
                    drawCallback: function(settings) {
                        // Update stats after each draw
                        updateStats();
                    }
                });
            }

            // Update statistics function
            function updateStats() {
                const table = $('#ordersTable').DataTable();
                const allData = table.rows({
                    search: 'applied'
                }).data().toArray();

                let total = allData.length;
                let completed = 0;
                let inProgress = 0;
                let totalRevenue = 0;

                allData.forEach(function(row) {
                    if (row.status === 'completed') completed++;
                    if (row.status === 'in-progress') inProgress++;
                    if (row.actual_revenue) totalRevenue += parseFloat(row.actual_revenue);
                });

                $('#totalOrders').text(total);
                $('#completedOrders').text(completed);
                $('#inProgressOrders').text(inProgress);
                $('#totalRevenue').text('$' + totalRevenue.toFixed(2));
            }

            // Apply filters button - wait for document ready
            document.addEventListener('DOMContentLoaded', function() {
                // Check if jQuery is loaded
                if (typeof $ !== 'undefined') {
                    $('#applyFilters').on('click', function() {
                        $('#ordersTable').DataTable().ajax.reload();
                    });

                    $('#clearFilters').on('click', function() {
                        $('#filterStatus').val('');
                        $('#filterTeam').val('');
                        $('#filterTechStack').val('');
                        $('#filterPriority').val('');
                        $('#ordersTable').DataTable().ajax.reload();
                    });

                    // Bulk Actions
                    let selectedIds = [];

                    // Check all functionality
                    $('#checkAll').on('change', function() {
                        const isChecked = $(this).prop('checked');
                        $('.row-checkbox').prop('checked', isChecked).trigger('change');
                    });

                    $('#selectAll').on('change', function() {
                        const isChecked = $(this).prop('checked');
                        $('#checkAll').prop('checked', isChecked);
                        $('.row-checkbox').prop('checked', isChecked).trigger('change');
                    });

                    // Individual checkbox change
                    $(document).on('change', '.row-checkbox', function() {
                        const id = $(this).data('id');

                        if ($(this).prop('checked')) {
                            if (!selectedIds.includes(id)) {
                                selectedIds.push(id);
                            }
                        } else {
                            selectedIds = selectedIds.filter(item => item !== id);
                        }

                        updateBulkActionsUI();
                    });

                    // Update bulk actions UI
                    function updateBulkActionsUI() {
                        const count = selectedIds.length;
                        $('#selectedCount').text(count);

                        if (count > 0) {
                            $('#bulkActions').removeClass('hidden');
                        } else {
                            $('#bulkActions').addClass('hidden');
                        }

                        // Update check all checkbox state
                        const totalCheckboxes = $('.row-checkbox').length;
                        const checkedCheckboxes = $('.row-checkbox:checked').length;
                        $('#checkAll, #selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes ===
                            checkedCheckboxes);
                    }

                    // Cancel selection
                    $('#cancelSelection').on('click', function() {
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        $('#checkAll, #selectAll').prop('checked', false);
                        $('#bulkActions').addClass('hidden');
                    });

                    // Apply bulk action
                    $('#applyBulkAction').on('click', function() {
                        const action = $('#bulkAction').val();

                        if (!action) {
                            Swal.fire({
                                title: 'Select Action',
                                text: 'Please select a bulk action to perform.',
                                icon: 'warning',
                                confirmButtonColor: '#9333ea'
                            });
                            return;
                        }

                        if (selectedIds.length === 0) {
                            Swal.fire({
                                title: 'No Selection',
                                text: 'Please select at least one order.',
                                icon: 'warning',
                                confirmButtonColor: '#9333ea'
                            });
                            return;
                        }

                        if (action === 'delete') {
                            Swal.fire({
                                title: 'Delete Selected Orders?',
                                text: `Are you sure you want to delete ${selectedIds.length} order(s)? This action cannot be undone.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, delete them!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    performBulkAction(action);
                                }
                            });
                        } else {
                            const actionNames = {
                                'mark-completed': 'Completed',
                                'mark-in-progress': 'In Progress',
                                'mark-pending': 'Pending',
                                'mark-on-hold': 'On Hold',
                                'mark-cancelled': 'Cancelled'
                            };

                            Swal.fire({
                                title: 'Update Status?',
                                text: `Mark ${selectedIds.length} order(s) as ${actionNames[action]}?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#9333ea',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, update!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    performBulkAction(action);
                                }
                            });
                        }
                    });

                    // Perform bulk action
                    function performBulkAction(action) {
                        Swal.showLoading();

                        const data = {
                            ids: selectedIds,
                            action: action
                        };

                        axios.post('{{ route('orders.bulk-action') }}', data)
                            .then(response => {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message || 'Bulk action completed successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea'
                                }).then(() => {
                                    selectedIds = [];
                                    $('#checkAll, #selectAll').prop('checked', false);
                                    $('#bulkActions').addClass('hidden');
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#9333ea'
                                });
                            });
                    }

                    // Re-bind checkboxes after DataTable reload
                    $('#ordersTable').on('draw.dt', function() {
                        selectedIds = [];
                        $('#checkAll, #selectAll').prop('checked', false);
                        $('#bulkActions').addClass('hidden');
                    });
                }
            });

            // Delete order function
            function deleteOrder(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();
                        axios.delete(`/orders/${id}`, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.data.message || 'Order has been deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea'
                                }).then(() => {
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
