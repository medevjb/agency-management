<!-- User Assignment Modal for Tasks -->
<div id="taskUserAssignmentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeTaskUserAssignmentModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform rounded-xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">
                            <i class="fas fa-users mr-2"></i>
                            Assign Users to Task
                        </h3>
                        <button type="button" onclick="closeTaskUserAssignmentModal()"
                            class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-indigo-100" id="modal-task-name"></p>
                </div>

                <!-- Modal Body -->
                <div class="px-4 py-4 sm:px-6">
                    <!-- Selected Users Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assigned Users
                        </label>
                        <div id="taskModalSelectedUsersDisplay"
                            class="border border-gray-300 dark:border-gray-700 rounded-lg p-3 min-h-[60px] bg-gray-50 dark:bg-gray-800 mb-3">
                            <span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>
                        </div>
                    </div>

                    <!-- Search and Add Users -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search & Add Users
                        </label>
                        <div class="relative">
                            <input type="text" id="taskModalUserSearchInput"
                                placeholder="Search users by name or email..."
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 text-sm">

                            <!-- Dropdown List -->
                            <div id="taskModalUserDropdown"
                                class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Users will be populated here by JavaScript -->
                            </div>
                        </div>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Start typing to search and add users
                        </p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="saveTaskUserAssignments()"
                        class="inline-flex w-full justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-purple-700 sm:ml-3 sm:w-auto transition-all">
                        <i class="fas fa-save mr-2"></i>
                        Save Assignments
                    </button>
                    <button type="button" onclick="closeTaskUserAssignmentModal()"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Task Assignment Modal Functionality
    let currentTaskId = null;
    let currentTaskUsers = [];
    let taskUserSearchTimeout = null;

    function openTaskUserAssignmentModal(taskId, taskName) {
        currentTaskId = taskId;

        // Set modal title
        document.getElementById('modal-task-name').textContent = taskName;

        // Load current users
        fetchTaskUsers(taskId);

        // Show modal
        document.getElementById('taskUserAssignmentModal').classList.remove('hidden');

        // Focus search input
        setTimeout(() => {
            document.getElementById('taskModalUserSearchInput').focus();
        }, 100);
    }

    function closeTaskUserAssignmentModal() {
        document.getElementById('taskUserAssignmentModal').classList.add('hidden');
        currentTaskId = null;
        currentTaskUsers = [];
        document.getElementById('taskModalUserSearchInput').value = '';
        document.getElementById('taskModalUserDropdown').classList.add('hidden');
    }

    async function fetchTaskUsers(taskId) {
        try {
            const response = await axios.get(`{{ route('tasks.get-users', ['task' => ':taskId']) }}`.replace(':taskId', taskId));
            currentTaskUsers = response.data.users;
            updateTaskModalSelectedDisplay();
        } catch (error) {
            console.error('Error fetching task users:', error);
        }
    }

    function updateTaskModalSelectedDisplay() {
        const display = document.getElementById('taskModalSelectedUsersDisplay');

        if (currentTaskUsers.length === 0) {
            display.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>';
            return;
        }

        display.innerHTML = currentTaskUsers.map(user => `
            <div class="inline-flex items-center gap-2 bg-white dark:bg-gray-700 rounded-lg px-3 py-1.5 m-1 shadow-sm">
                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${user.name}</span>
                <button onclick="removeTaskModalUser(${user.id})" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `).join('');
    }

    async function fetchTaskModalUsers(search) {
        try {
            const excludeIds = currentTaskUsers.map(u => u.id).join(',');
            const params = new URLSearchParams({
                search: search,
                exclude: excludeIds
            });

            const response = await axios.get(`{{ route('tasks.search-users') }}?${params}`);
            return response.data.users;
        } catch (error) {
            console.error('Error fetching task users:', error);
            return [];
        }
    }

    function renderTaskModalDropdown(users) {
        const dropdown = document.getElementById('taskModalUserDropdown');

        if (users.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No users found</div>';
        } else {
            dropdown.innerHTML = users.map(user => `
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                    onclick="addTaskModalUser(${JSON.stringify(user).replace(/"/g, '&quot;')})">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 dark:text-gray-100 text-sm">${user.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">${user.email}</div>
                            ${user.employee_id ? `<div class="text-xs text-gray-400 dark:text-gray-500">ID: ${user.employee_id}</div>` : ''}
                        </div>
                        <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        dropdown.classList.remove('hidden');
    }

    function addTaskModalUser(user) {
        if (!currentTaskUsers.find(u => u.id === user.id)) {
            currentTaskUsers.push(user);
            updateTaskModalSelectedDisplay();
            document.getElementById('taskModalUserSearchInput').value = '';
            document.getElementById('taskModalUserDropdown').classList.add('hidden');
        }
    }

    function removeTaskModalUser(userId) {
        currentTaskUsers = currentTaskUsers.filter(u => u.id !== userId);
        updateTaskModalSelectedDisplay();
    }

    async function saveTaskUserAssignments() {
        if (!currentTaskId) return;

        try {
            const userIds = currentTaskUsers.map(u => u.id);

            await axios.post(`{{ route('tasks.assign-users', ['task' => ':taskId']) }}`.replace(':taskId', currentTaskId), {
                users: userIds
            });

            // Show success message
            showNotification('Task assignments updated successfully!', 'success');

            // Close modal
            closeTaskUserAssignmentModal();

            // Refresh task list if on index page
            if (typeof window.DataTable !== 'undefined') {
                window.DataTable.ajax.reload(null, false);
            }
        } catch (error) {
            console.error('Error saving task assignments:', error);
            showNotification('Error saving task assignments. Please try again.', 'error');
        }
    }

    // Setup search functionality with debouncing
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('taskModalUserSearchInput');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const search = this.value.trim();

                // Clear previous timeout
                if (taskUserSearchTimeout) {
                    clearTimeout(taskUserSearchTimeout);
                }

                // Hide dropdown if search is empty
                if (search === '') {
                    document.getElementById('taskModalUserDropdown').classList.add('hidden');
                    return;
                }

                // Debounce search
                taskUserSearchTimeout = setTimeout(async function() {
                    const users = await fetchTaskModalUsers(search);
                    renderTaskModalDropdown(users);
                }, 300);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('taskModalUserDropdown');
                const searchInput = document.getElementById('taskModalUserSearchInput');

                if (dropdown && !dropdown.contains(event.target) && event.target !== searchInput) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });

    // Helper function for notifications
    function showNotification(message, type) {
        // Simple notification implementation
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endpush