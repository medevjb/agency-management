@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tech Stacks</h1>
            <p class="text-gray-600 mt-2">Manage and track all your technology stacks</p>
        </div>
        @can('manage tech stacks')
            <a href="{{ route('tech-stacks.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>New Tech Stack
            </a>
        @endcan
    </div>

    <!-- Tech Stacks DataTable -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
                <!-- Title Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tech Stacks List</h3>
                    <div class="flex flex-wrap gap-4 items-center mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stack Leader</label>
                            <select id="leaderFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Leaders</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Member Count</label>
                            <select id="memberCountFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Counts</option>
                                <option value="1-3">1-3 Members</option>
                                <option value="4-6">4-6 Members</option>
                                <option value="7+">7+ Members</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-64">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search tech stacks by name or leader..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 lg:mt-6">
                    <button id="resetFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                    <button id="refreshBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="tech-stacks-table" class="table table-striped" style="width:100%">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tech Stack</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stack Leader</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projects</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="tech-stacks-tbody">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading tech stacks...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    #tech-stacks-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    #tech-stacks-table th {
        border-top: none !important;
        border-bottom: 2px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        font-weight: 600 !important;
        color: #374151 !important;
    }

    #tech-stacks-table tbody tr {
        transition: all 0.15s ease-in-out;
    }

    #tech-stacks-table tbody tr:hover {
        background-color: #f3f4f6;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    #tech-stacks-table tbody tr:last-child td {
        border-bottom: none;
    }

    #tech-stacks-table td {
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .min-w-0 {
        min-width: 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery;

    if (!$) {
        console.error('jQuery not loaded');
        return;
    }

    let techStacksData = [];
    let filteredData = [];
    let allLeaders = [];

    // Load initial data
    loadTechStacks();

    // Event listeners
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTechStacks();
    });

    
    document.getElementById('resetFilters').addEventListener('click', function() {
        resetFilters();
    });

    document.getElementById('leaderFilter').addEventListener('change', function() {
        filterTechStacks();
    });

    document.getElementById('memberCountFilter').addEventListener('change', function() {
        filterTechStacks();
    });

    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadTechStacks();
    });

    function loadTechStacks() {
        const tbody = document.getElementById('tech-stacks-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading tech stacks...
                    </div>
                </td>
            </tr>
        `;

        $.ajax({
            url: '{{ route('tech-stacks.data') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Response from server:', response);
                if (response.data && Array.isArray(response.data)) {
                    techStacksData = response.data;
                    filteredData = [...techStacksData];
                    console.log('Tech stacks data loaded:', techStacksData);
                    populateLeaderFilter();
                    renderTechStacks();
                } else {
                    console.log('Invalid response format:', response);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-red-500">
                                Failed to load tech stacks data
                            </td>
                        </tr>
                    `;
                }
            },
            error: function(xhr) {
                console.error('Error loading tech stacks:', xhr);
                const tbody = document.getElementById('tech-stacks-tbody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-red-500">
                            Error loading tech stacks. Please refresh the page.
                        </td>
                    </tr>
                `;
            }
        });
    }

    function renderTechStacks() {
        const tbody = document.getElementById('tech-stacks-tbody');

        if (filteredData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No tech stacks found
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filteredData.map(techStack => {
            console.log('Rendering tech stack:', techStack);

            // Handle stack leader data structure
            let stackLeader = null;
            if (techStack.stack_leader) {
                if (typeof techStack.stack_leader === 'object') {
                    stackLeader = techStack.stack_leader;
                } else if (typeof techStack.stack_leader === 'string') {
                    // Parse the string data if needed
                    const parts = techStack.stack_leader.split(' ');
                    stackLeader = {
                        name: parts[0] || 'Unknown',
                        employee_id: parts[1] || 'N/A'
                    };
                }
            }

            // Handle member and project counts
            const membersParts = techStack.members_count ? techStack.members_count.split(' ') : ['0', 'active'];
            const projectsParts = techStack.projects_count ? techStack.projects_count.split(' ') : ['0', 'active'];

            const techStackId = techStack.DT_RowId || techStack.id;

            return `
            <tr class="hover:bg-gray-50 border-b border-gray-100">
                <td class="px-6 py-4">
                    <div class="flex items-center min-w-0">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-layer-group text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 truncate">${techStack.name || 'No name'}</div>
                            <div class="text-sm text-gray-500 truncate">${techStack.description || 'No description'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    ${stackLeader && stackLeader.name ? `
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-user-tie text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">${stackLeader.name}</div>
                                <div class="text-xs text-gray-500">${stackLeader.employee_id || 'N/A'}</div>
                            </div>
                        </div>
                    ` : `
                        <div class="flex items-center text-gray-400">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user-slash text-gray-500 text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-2 text-sm">Not Assigned</div>
                        </div>
                    `}
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-code mr-1"></i>
                        ${membersParts[0] || '0'}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">${membersParts[2] || 'active'}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-folder-open mr-1"></i>
                        ${projectsParts[0] || '0'}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">${projectsParts[2] || 'active'}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <a href="/tech-stacks/${techStackId}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
                           title="View Tech Stack">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="/tech-stacks/${techStackId}/edit"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-800 transition-colors duration-200"
                           title="Edit Tech Stack">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <button onclick="deleteTechStack(${techStackId})"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-800 transition-colors duration-200"
                                title="Delete Tech Stack">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }

    function filterTechStacks() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const leaderFilter = document.getElementById('leaderFilter').value;
        const memberCountFilter = document.getElementById('memberCountFilter').value;

        filteredData = techStacksData.filter(techStack => {
            // Search filter
            let matchesSearch = true;
            if (searchTerm !== '') {
                const searchFields = [
                    techStack.name || '',
                    techStack.description || '',
                    (techStack.stack_leader && techStack.stack_leader.name) ? techStack.stack_leader.name : '',
                    (techStack.stack_leader && techStack.stack_leader.employee_id) ? techStack.stack_leader.employee_id : ''
                ];

                matchesSearch = searchFields.some(field =>
                    field.toLowerCase().includes(searchTerm)
                );
            }

            // Leader filter
            let matchesLeader = true;
            if (leaderFilter !== '') {
                matchesLeader = techStack.stack_leader && techStack.stack_leader.name === leaderFilter;
            }

            // Member count filter
            let matchesMemberCount = true;
            if (memberCountFilter !== '') {
                const memberCount = techStack.members_count ? parseInt(techStack.members_count.split(' ')[0]) : 0;
                switch (memberCountFilter) {
                    case '1-3':
                        matchesMemberCount = memberCount >= 1 && memberCount <= 3;
                        break;
                    case '4-6':
                        matchesMemberCount = memberCount >= 4 && memberCount <= 6;
                        break;
                    case '7+':
                        matchesMemberCount = memberCount >= 7;
                        break;
                }
            }

            return matchesSearch && matchesLeader && matchesMemberCount;
        });

        renderTechStacks();
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('leaderFilter').value = '';
        document.getElementById('memberCountFilter').value = '';
        filteredData = [...techStacksData];
        renderTechStacks();
    }

    function populateLeaderFilter() {
        const leaderFilter = document.getElementById('leaderFilter');
        const leaders = new Set();

        techStacksData.forEach(techStack => {
            if (techStack.stack_leader && techStack.stack_leader.name) {
                leaders.add(techStack.stack_leader.name);
            }
        });

        // Clear existing options except the first one
        leaderFilter.innerHTML = '<option value="">All Leaders</option>';

        // Add leader options
        Array.from(leaders).sort().forEach(leaderName => {
            const option = document.createElement('option');
            option.value = leaderName;
            option.textContent = leaderName;
            leaderFilter.appendChild(option);
        });
    }

    window.deleteTechStack = function(techStackId) {
        Swal.fire({
            title: 'Delete Tech Stack?',
            text: "Are you sure you want to delete this tech stack? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            showLoaderOnConfirm: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/tech-stacks/' + techStackId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Tech stack has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            loadTechStacks(); // Reload the data
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete tech stack',
                                icon: 'error',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'An error occurred while deleting the tech stack.',
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    }
                });
            }
        });
    };
});
</script>
@endpush
@endsection