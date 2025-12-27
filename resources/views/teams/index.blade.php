@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6">
            <div class="flex-1">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Teams Management</h1>
                <p class="text-gray-600 text-lg">Organize and manage your development teams efficiently</p>
            </div>

            @can('manage teams')
            <div class="flex-shrink-0">
                <a href="{{ route('teams.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Team
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Teams DataTable -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Enhanced Filter Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Title Section -->
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-100 rounded-lg p-2">
                        <i class="fas fa-users text-indigo-600 text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Teams Directory</h3>
                </div>

                <!-- Filters and Actions in One Row -->
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-end gap-4 xl:gap-3 w-full xl:w-auto">
                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 xl:gap-2">
                        <div class="relative">
                            <select id="leaderFilter" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm appearance-none">
                                <option value="">All Leaders</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-tie text-indigo-500 text-xs"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <div class="relative">
                            <select id="memberCountFilter" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm appearance-none">
                                <option value="">All Sizes</option>
                                <option value="1-3">1-3 Members</option>
                                <option value="4-6">4-6 Members</option>
                                <option value="7+">7+ Members</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-green-500 text-xs"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search teams..."
                                   class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-purple-500 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button id="resetFilters" class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg shadow-sm hover:shadow-md border border-gray-300 transition-all duration-200 hover:scale-105">
                            <i class="fas fa-redo mr-2"></i>
                            
                        </button>
                        <button id="refreshBtn" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <i class="fas fa-sync-alt mr-2"></i>
                            
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Table with Better UX -->
        <div class="overflow-x-auto bg-white">
            <table id="teams-table" class="w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-users text-indigo-600"></i>
                                <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Team</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-tie text-green-600"></i>
                                <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Leader</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-users text-blue-600"></i>
                                <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Members</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-project-diagram text-purple-600"></i>
                                <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Projects</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-cog text-gray-600"></i>
                                <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Actions</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="teams-tbody">
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="relative">
                                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                                    </div>
                                </div>
                                <p class="mt-4 text-gray-600 font-medium">Loading teams...</p>
                                <p class="text-sm text-gray-400 mt-1">Please wait while we fetch your data</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Footer with Compact Stats -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Showing Info -->
                <div class="text-sm text-gray-600">
                    <span id="showingInfo">
                        Showing <span id="showingCount">0</span> of <span id="totalCount">0</span> teams
                    </span>
                </div>

                <!-- Compact Stats -->
                <div class="flex items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <div class="bg-blue-500 rounded p-1.5">
                            <i class="fas fa-users text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">
                            <span id="totalTeams">0</span> Teams
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="bg-green-500 rounded p-1.5">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">
                            <span id="activeMembers">0</span> Members
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="bg-purple-500 rounded p-1.5">
                            <i class="fas fa-project-diagram text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">
                            <span id="activeProjects">0</span> Projects
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #teams-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    #teams-table th {
        border-top: none !important;
        border-bottom: 2px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        font-weight: 600 !important;
        color: #374151 !important;
    }

    #teams-table tbody tr {
        transition: all 0.15s ease-in-out;
    }

    #teams-table tbody tr:hover {
        background-color: #f3f4f6;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    #teams-table tbody tr:last-child td {
        border-bottom: none;
    }

    #teams-table td {
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery;

    if (!$) {
        console.error('jQuery not loaded');
        return;
    }

    let teamsData = [];
    let filteredData = [];
    let allLeaders = [];

    // Load initial data
    loadTeams();

    // Event listeners
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTeams();
    });

    
    document.getElementById('resetFilters').addEventListener('click', function() {
        resetFilters();
    });

    document.getElementById('leaderFilter').addEventListener('change', function() {
        filterTeams();
    });

    document.getElementById('memberCountFilter').addEventListener('change', function() {
        filterTeams();
    });

    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadTeams();
    });

    function loadTeams() {
        const tbody = document.getElementById('teams-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading teams...
                    </div>
                </td>
            </tr>
        `;

        axios.get('{{ route('teams.data') }}')
            .then(response => {
                console.log('Response from server:', response);
                if (response.data.data && Array.isArray(response.data.data)) {
                    teamsData = response.data.data;
                    filteredData = [...teamsData];
                    console.log('Teams data loaded:', teamsData);
                    populateLeaderFilter();
                    updateStats();
                    renderTeams();
                } else {
                    console.log('Invalid response format:', response);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-red-100 rounded-full p-4 mb-4">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                                    </div>
                                    <p class="text-red-600 font-semibold">Failed to load teams data</p>
                                    <p class="text-sm text-gray-400 mt-1">Please refresh the page and try again</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading teams:', error);
                const tbody = document.getElementById('teams-tbody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-red-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-wifi-slash text-red-600 text-2xl"></i>
                                </div>
                                <p class="text-red-600 font-semibold">Connection Error</p>
                                <p class="text-sm text-gray-400 mt-1">Unable to load teams. Please check your connection.</p>
                                <button onclick="loadTeams()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                                    <i class="fas fa-redo mr-2"></i>Try Again
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        });
    }

    function renderTeams() {
        const tbody = document.getElementById('teams-tbody');

        if (filteredData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-6 mb-4">
                                <i class="fas fa-users-slash text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 font-semibold text-lg">No teams found</p>
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your search filters or create a new team</p>
                            @can('manage teams')
                            <a href="{{ route('teams.create') }}" class="mt-4 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <i class="fas fa-plus mr-2"></i>
                                Create Your First Team
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filteredData.map(team => {
            console.log('Rendering team:', team);

            // Handle team leader data structure
            let teamLeader = null;
            if (team.team_leader) {
                if (typeof team.team_leader === 'object') {
                    teamLeader = team.team_leader;
                } else if (typeof team.team_leader === 'string') {
                    // Parse the string data if needed
                    const parts = team.team_leader.split(' ');
                    teamLeader = {
                        name: parts[0] || 'Unknown',
                        employee_id: parts[1] || 'N/A'
                    };
                }
            }

            // Handle member and project counts
            const membersParts = team.members_count ? team.members_count.split(' ') : ['0', 'active'];
            const projectsParts = team.projects_count ? team.projects_count.split(' ') : ['0', 'active'];

            const teamId = team.DT_RowId || team.id;

            return `
            <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                <td class="px-6 py-5">
                    <div class="flex items-center min-w-0">
                        <div class="flex-shrink-0">
                            <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <div class="text-base font-bold text-gray-900 truncate">${team.name || 'Untitled Team'}</div>
                            <div class="text-sm text-gray-500 truncate mt-1">${team.description || 'No description available'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    ${teamLeader && teamLeader.name ? `
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0">
                                <img class="h-12 w-12 rounded-xl object-cover shadow-md"
                                     src="https://ui-avatars.com/api/?name=${encodeURIComponent(teamLeader.name)}&background=10B981&color=fff"
                                     alt="${teamLeader.name}">
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">${teamLeader.name}</div>
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <i class="fas fa-id-badge mr-1"></i>
                                    ${teamLeader.employee_id || 'N/A'}
                                </div>
                            </div>
                        </div>
                    ` : `
                        <div class="flex items-center text-gray-400">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-xl bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user-slash text-gray-500"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium">No Leader</div>
                                <div class="text-xs text-gray-400">Assign a team leader</div>
                            </div>
                        </div>
                    `}
                </td>
                <td class="px-6 py-5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 shadow-sm">
                            <i class="fas fa-users mr-2"></i>
                            ${membersParts[0] || '0'}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 font-medium">${membersParts[1] || '0'} active</div>
                    </div>
                </td>
                <td class="px-6 py-5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 shadow-sm">
                            <i class="fas fa-project-diagram mr-2"></i>
                            ${projectsParts[0] || '0'}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 font-medium">${projectsParts[1] || '0'} active</div>
                    </div>
                </td>
                <td class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center space-x-1">
                        <a href="/teams/${teamId}"
                           class="group inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-100 to-indigo-200 text-indigo-600 hover:from-indigo-200 hover:to-indigo-300 transition-all duration-200 hover:scale-110 shadow-sm"
                           title="View Team Details">
                            <i class="fas fa-eye text-sm group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="/teams/${teamId}/edit"
                           class="group inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-emerald-100 to-emerald-200 text-emerald-600 hover:from-emerald-200 hover:to-emerald-300 transition-all duration-200 hover:scale-110 shadow-sm"
                           title="Edit Team">
                            <i class="fas fa-edit text-sm group-hover:scale-110 transition-transform"></i>
                        </a>
                        <button onclick="deleteTeam(${teamId})"
                                class="group inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-red-100 to-red-200 text-red-600 hover:from-red-200 hover:to-red-300 transition-all duration-200 hover:scale-110 shadow-sm"
                                title="Delete Team">
                            <i class="fas fa-trash text-sm group-hover:scale-110 transition-transform"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }

    function filterTeams() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const leaderFilter = document.getElementById('leaderFilter').value;
        const memberCountFilter = document.getElementById('memberCountFilter').value;

        filteredData = teamsData.filter(team => {
            // Search filter
            let matchesSearch = true;
            if (searchTerm !== '') {
                const searchFields = [
                    team.name || '',
                    team.description || '',
                    (team.team_leader && team.team_leader.name) ? team.team_leader.name : '',
                    (team.team_leader && team.team_leader.employee_id) ? team.team_leader.employee_id : ''
                ];

                matchesSearch = searchFields.some(field =>
                    field.toLowerCase().includes(searchTerm)
                );
            }

            // Leader filter
            let matchesLeader = true;
            if (leaderFilter !== '') {
                matchesLeader = team.team_leader && team.team_leader.name === leaderFilter;
            }

            // Member count filter
            let matchesMemberCount = true;
            if (memberCountFilter !== '') {
                const memberCount = team.members_count ? parseInt(team.members_count.split(' ')[0]) : 0;
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

        renderTeams();

        // Update showing count after filtering
        document.getElementById('showingCount').textContent = filteredData.length;
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('leaderFilter').value = '';
        document.getElementById('memberCountFilter').value = '';
        filteredData = [...teamsData];
        renderTeams();

        // Update showing count after reset
        document.getElementById('showingCount').textContent = filteredData.length;
    }

    function updateStats() {
        const totalTeams = teamsData.length;
        const filteredCount = filteredData.length;
        let totalMembers = 0;
        let activeProjects = 0;

        teamsData.forEach(team => {
            // Count members
            const membersCount = team.members_count ? parseInt(team.members_count.split(' ')[0]) : 0;
            totalMembers += membersCount;

            // Count active projects
            const projectsCount = team.projects_count ? parseInt(team.projects_count.split(' ')[0]) : 0;
            activeProjects += projectsCount;
        });

        // Update showing info
        document.getElementById('showingCount').textContent = filteredCount;
        document.getElementById('totalCount').textContent = totalTeams;

        // Update stats without animation (instant update for compact footer)
        document.getElementById('totalTeams').textContent = totalTeams;
        document.getElementById('activeMembers').textContent = totalMembers;
        document.getElementById('activeProjects').textContent = activeProjects;
    }

    
    function populateLeaderFilter() {
        const leaderFilter = document.getElementById('leaderFilter');
        const leaders = new Set();

        teamsData.forEach(team => {
            if (team.team_leader && team.team_leader.name) {
                leaders.add(team.team_leader.name);
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

    window.deleteTeam = function(teamId) {
        Swal.fire({
            title: 'Delete Team?',
            text: "Are you sure you want to delete this team? This action cannot be undone.",
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
                axios.delete('/teams/' + teamId)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.data.message || 'Team has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            loadTeams(); // Reload the data
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message || 'Failed to delete team',
                                icon: 'error',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.response?.data?.message || 'An error occurred while deleting the team.',
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    });
            }
        });
    };
});
</script>
@endpush
@endsection