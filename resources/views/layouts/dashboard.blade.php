<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex"
         x-data="{
             sidebarOpen: false,
             dashboardSection: true,
             ordersSection: true,
             tasksSection: true,
             profileSection: true,
             portfolioSection: true,
             teamSection: true,
             adminSection: true
         }">
        <!-- Sidebar Component -->
        @include('components.sidebar')

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 lg:hidden bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>

        <!-- Mobile Sidebar -->
        <aside x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300"
               x-transition:enter-start="transform -translate-x-full" x-transition:enter-end="transform translate-x-0"
               x-transition:leave="transition ease-in-out duration-300"
               x-transition:leave-start="transform translate-x-0" x-transition:leave-end="transform -translate-x-full"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-600 to-indigo-800 text-white lg:hidden">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-4 border-b border-indigo-700">
                    <h1 class="text-xl font-bold">{{ config('app.name', 'Dashboard') }}</h1>
                    <button @click="sidebarOpen = false" class="text-white hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-indigo-900 text-white">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>

                    @role('admin')
                    <div class="border-t border-indigo-700 pt-2 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Admin</p>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Admin Dashboard
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-users-cog mr-3"></i>
                            Manage Users
                        </a>
                        <a href="{{ route('admin.roles') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-shield-alt mr-3"></i>
                            Roles & Permissions
                        </a>
                        <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-cog mr-3"></i>
                            System Settings
                        </a>
                    </div>
                    @endrole

                    <div class="border-t border-indigo-700 pt-2 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Orders</p>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-shopping-cart mr-3"></i>
                            Orders
                        </a>
                    </div>

                    @canany(['manage teams', 'view teams'])
                    @can('manage teams')
                    <a href="{{ route('teams.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-users mr-3"></i>
                        Teams
                    </a>
                    @endif

                    @can('manage tech stacks')
                    <a href="{{ route('tech-stacks.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-layer-group mr-3"></i>
                        Tech Stacks
                    </a>
                    @endif
                    @endcan
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-calendar mr-3"></i>
                        Calendar
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-envelope mr-3"></i>
                        Messages
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            <!-- Header Component -->
            @include('components.header')

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ Vite::asset('resources/js/app.js') }}" type="module" defer></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- User Dropdown Script -->
    <script>
    // User dropdown functionality - load when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userDropdownBtn && userDropdown) {
            // Toggle dropdown when button is clicked
            userDropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for date fields
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all datepicker inputs
            const dateInputs = document.querySelectorAll('.datepicker');
            dateInputs.forEach(function(input) {
                const picker = flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    allowInput: false,
                    theme: 'light',
                    disableMobile: true,
                    clickOpens: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        // If end date exists and is before start date, update it
                        if (instance.element.id === 'start_date' && selectedDates.length > 0) {
                            const endDateInput = document.getElementById('end_date');
                            if (endDateInput) {
                                const endDate = flatpickr(endDateInput);
                                endDate.set('minDate', dateStr);
                                if (endDate.selectedDates.length > 0 && endDate.selectedDates[0] < selectedDates[0]) {
                                    endDate.setDate(selectedDates[0]);
                                }
                            }
                        }

                        // Calculate duration if function exists
                        if (typeof calculateDuration === 'function') {
                            calculateDuration();
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>