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
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside x-data="{ sidebarOpen: false }" class="w-64 bg-gradient-to-b from-indigo-600 to-indigo-800 text-white flex-shrink-0 hidden lg:block">
            <div class="flex flex-col h-full">
                <!-- Logo Section -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-indigo-700">
                    <h1 class="text-xl font-bold">{{ config('app.name', 'Dashboard') }}</h1>
                </div>

                <!-- Navigation -->
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
                            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-users-cog mr-3"></i>
                                Manage Users
                            </a>
                            <a href="{{ route('admin.roles') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-shield-alt mr-3"></i>
                                Roles & Permissions
                            </a>
                        </div>
                    @endrole

                    <div class="border-t border-indigo-700 pt-2 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Project</p>
                        <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-folder mr-3"></i>
                            Projects
                        </a>
                    </div>

                    <!-- Team Management Section -->
                    @canany(['manage teams', 'view teams'])
                    <div class="border-t border-indigo-700 pt-2 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Team Management</p>

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
                    </div>
                    @endif

                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-calendar mr-3"></i>
                        Calendar
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-envelope mr-3"></i>
                        Messages
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </nav>

                <!-- User Section -->
                <div class="px-4 py-4 border-t border-indigo-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4F46E5&color=fff" alt="{{ Auth::user()->name }}">
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-indigo-300">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

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
                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    @endrole

                    <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-folder mr-3"></i>
                        Projects
                    </a>

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
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Search Bar -->
                        <div class="flex-1 max-w-2xl mx-4">
                            <div class="relative">
                                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Right side items -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>

                            <!-- Messages -->
                            <button class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-envelope text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
                            </button>

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open"
                                        data-user-dropdown-btn
                                        class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4F46E5&color=fff" alt="{{ Auth::user()->name }}">
                                    <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>

                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     data-user-dropdown
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ Vite::asset('resources/js/app.js') }}" defer></script>

    <!-- Fallback for dropdown if Alpine.js is not working -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Check if SweetAlert is available
        console.log('SweetAlert available:', typeof Swal !== 'undefined');
        if (typeof Swal !== 'undefined') {
            console.log('SweetAlert version:', Swal.version);
        }

        // Simple dropdown fallback
        const userDropdownBtn = document.querySelector('[data-user-dropdown-btn]');
        const userDropdown = document.querySelector('[data-user-dropdown]');

        if (userDropdownBtn && userDropdown) {
            userDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>