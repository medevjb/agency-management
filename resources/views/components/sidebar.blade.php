<!-- Desktop Sidebar -->
<aside class="fixed top-0 left-0 h-screen w-64 bg-indigo-900 text-white hidden lg:flex flex-col shadow-2xl z-50">
    <!-- Logo Section -->
    <div class="flex items-center justify-center gap-2.5 h-16 px-6 border-b border-indigo-700/50 flex-shrink-0">
        <x-site-logo
            img-class="h-auto w-auto max-h-12 max-w-[170px] object-contain"
            badge-class="h-10 w-10 text-lg"
            :show-name="true"
            name-class="font-bold text-lg tracking-tight text-white" />
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">

        @php
            $currentSection = '';
            if (request()->routeIs('dashboard')) {
                $currentSection = 'dashboard';
            } elseif (request()->routeIs('orders.*') || request()->routeIs('value-distribution.*')) {
                $currentSection = 'orders';
            } elseif (request()->routeIs('tasks.*')) {
                $currentSection = 'tasks';
            } elseif (request()->routeIs('bug-reports.*')) {
                $currentSection = 'bug-reports';
            } elseif (request()->routeIs('freelance-profiles.*')) {
                $currentSection = 'profile';
            } elseif (request()->routeIs('portfolios.*')) {
                $currentSection = 'portfolio';
            } elseif (request()->routeIs('teams.*') || request()->routeIs('tech-stacks.*')) {
                $currentSection = 'team';
            } elseif (
                request()->routeIs('admin.*') ||
                request()->routeIs('users.*') ||
                request()->routeIs('settings.*')
            ) {
                $currentSection = 'admin';
            }
        @endphp

        <!-- Dashboard Section - Only for admin/OM/PM -->
        @role(['admin', 'operation-manager', 'project-manager'])
            <div class="mb-2 sidebar-section" data-section="dashboard">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-th-large w-5 text-indigo-300 group-hover:text-white transition-colors"></i>
                        <span class="ml-3">Dashboard</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'dashboard' ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'dashboard' ? 'hidden' : '' }}">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-chart-line w-5 mr-2"></i>
                        Overview
                    </a>
                </div>
            </div>
        @endrole

        <!-- Sales Section -->
        @role(['sales-leader', 'sales-member', 'admin', 'operation-manager', 'project-manager'])
            <div class="mb-2 sidebar-section" data-section="sales">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line w-5 text-emerald-400 group-hover:text-emerald-300 transition-colors"></i>
                        <span class="ml-3">Sales</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ request()->routeIs('sales.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ ! request()->routeIs('sales.*') ? 'hidden' : '' }}">
                    @if(auth()->user()->isSales())
                        <a href="{{ route('sales.dashboard') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('sales.dashboard') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-sack-dollar w-5 mr-2"></i>
                            My Sales
                        </a>
                        <a href="{{ route('orders.create') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-plus w-5 mr-2"></i>
                            Add Order
                        </a>
                    @endif
                    @role(['admin', 'operation-manager', 'project-manager', 'sales-leader'])
                        <a href="{{ route('sales.leadership') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('sales.leadership') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-trophy w-5 mr-2"></i>
                            Leadership Overview
                        </a>
                    @endrole
                    @role(['admin', 'operation-manager', 'project-manager'])
                        <a href="{{ route('sales.dashboard.preview') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('sales.dashboard.preview') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-eye w-5 mr-2"></i>
                            Member Dashboard Preview
                            <span class="ml-auto text-[10px] font-bold uppercase tracking-wide text-indigo-300">Preview</span>
                        </a>
                    @endrole
                </div>
            </div>
        @endrole

        <!-- Orders Section -->
        <div class="mb-2 sidebar-section" data-section="orders">
            <button
                class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-shopping-cart w-5 text-indigo-300 group-hover:text-white transition-colors"></i>
                    <span class="ml-3">Orders</span>
                </div>
                <i
                    class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'orders' ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'orders' ? 'hidden' : '' }}">
                @unless(auth()->user()->isSales())
                    <a href="{{ route('orders.dashboard') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.dashboard') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-chart-line w-5 mr-2"></i>
                        Dashboard
                    </a>
                @endunless
                <a href="{{ route('orders.index') }}"
                    class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                    <i class="fas fa-list w-5 mr-2"></i>
                    {{ auth()->user()->isSales() ? 'My Orders' : 'All Orders' }}
                </a>
                @can('create', \App\Models\Order::class)
                    <a href="{{ route('orders.create') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-plus w-5 mr-2"></i>
                        Add Order
                    </a>
                @endcan
                @role(['admin', 'operation-manager', 'project-manager', 'team-leader'])
                    @unless(auth()->user()->isSales())
                        <a href="{{ route('value-distribution.index') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('value-distribution.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-sitemap w-5 mr-2"></i>
                            Value Distribution
                        </a>
                    @endunless
                @endrole
            </div>
        </div>

        <!-- Tasks Section -->
        <div class="mb-2 sidebar-section" data-section="tasks">
            <button
                class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-tasks w-5 text-cyan-400 group-hover:text-cyan-300 transition-colors"></i>
                    <span class="ml-3">Tasks</span>
                </div>
                <i
                    class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'tasks' ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'tasks' ? 'hidden' : '' }}">
                <a href="{{ route('tasks.index') }}"
                    class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tasks.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                    <i class="fas fa-list w-5 mr-2"></i>
                    All Tasks
                </a>
            </div>
        </div>

        <!-- Bug Reports Section -->
        <div class="mb-2 sidebar-section" data-section="bug-reports">
            <button
                class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-bug w-5 text-orange-400 group-hover:text-orange-300 transition-colors"></i>
                    <span class="ml-3">Bug Reports</span>
                </div>
                <i
                    class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'bug-reports' ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'bug-reports' ? 'hidden' : '' }}">
                <a href="{{ route('bug-reports.index') }}"
                    class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('bug-reports.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                    <i class="fas fa-list w-5 mr-2"></i>
                    All Bugs
                </a>
                @can('create', \App\Models\BugReport::class)
                    <a href="{{ route('bug-reports.create') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('bug-reports.create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-plus w-5 mr-2"></i>
                        Report Bug
                    </a>
                @endcan
            </div>
        </div>

        <!-- Profile Management Section -->
        @role(['admin', 'operation-manager', 'project-manager'])
            <div class="mb-2 sidebar-section" data-section="profile">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i
                            class="fas fa-address-card w-5 text-purple-400 group-hover:text-purple-300 transition-colors"></i>
                        <span class="ml-3">Profiles</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'profile' ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'profile' ? 'hidden' : '' }}">
                    <a href="{{ route('freelance-profiles.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('freelance-profiles.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-list w-5 mr-2"></i>
                        All Profiles
                    </a>
                    @can('create freelance profile')
                        <a href="{{ route('freelance-profiles.index', ['create' => 1]) }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('freelance-profiles.index') && request('create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-plus w-5 mr-2"></i>
                            Add Profile
                        </a>
                    @endcan
                </div>
            </div>
        @else
            @can('view freelance profiles')
                <div class="mb-2 sidebar-section" data-section="profile">
                    <button
                        class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i
                                class="fas fa-address-card w-5 text-purple-400 group-hover:text-purple-300 transition-colors"></i>
                            <span class="ml-3">Profiles</span>
                        </div>
                        <i
                            class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'profile' ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'profile' ? 'hidden' : '' }}">
                        <a href="{{ route('freelance-profiles.index') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('freelance-profiles.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-list w-5 mr-2"></i>
                            View Profiles
                        </a>
                    </div>
                </div>
            @endcan
        @endrole

        {{-- <!-- Portfolio Section -->
        @canany(['view any portfolio', 'view own portfolio'])
            <div class="mb-2 sidebar-section" data-section="portfolio">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-star w-5 text-yellow-400 group-hover:text-yellow-300 transition-colors"></i>
                        <span class="ml-3">Portfolio</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'portfolio' ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'portfolio' ? 'hidden' : '' }}">
                    @can('view any portfolio')
                        <a href="{{ route('portfolios.index') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('portfolios.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-globe w-5 mr-2"></i>
                            All Portfolios
                        </a>
                    @endcan
                    @can('view own portfolio')
                        <a href="{{ route('portfolios.my') }}"
                            class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('portfolios.my') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-user-circle w-5 mr-2"></i>
                            My Portfolio
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany --}}

        <!-- Team Management Section -->
        <div class="mb-2 sidebar-section" data-section="team">
            <button
                class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-users w-5 text-green-400 group-hover:text-green-300 transition-colors"></i>
                    <span class="ml-3">Team</span>
                </div>
                <i
                    class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'team' ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'team' ? 'hidden' : '' }}">
                <a href="{{ route('teams.index') }}"
                    class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('teams.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                    <i class="fas fa-users w-5 mr-2"></i>
                    Teams
                </a>
                @unlessrole(['sales-leader', 'sales-member'])
                    <a href="{{ route('tech-stacks.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tech-stacks.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-layer-group w-5 mr-2"></i>
                        Tech Stacks
                    </a>
                @endunlessrole
            </div>
        </div>

        <!-- Admin Section - Only for admin -->
        @role('admin')
            <div class="mb-2 sidebar-section" data-section="admin">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-red-600/30 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt w-5 text-red-400 group-hover:text-red-300 transition-colors"></i>
                        <span class="ml-3">Admin</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'admin' ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'admin' ? 'hidden' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-tachometer-alt w-5 mr-2"></i>
                        Admin Dashboard
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('users.index') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-users-cog w-5 mr-2"></i>
                        Manage All Users
                    </a>
                    <a href="{{ route('admin.roles') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.roles*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-key w-5 mr-2"></i>
                        Roles & Permissions
                    </a>
                    <a href="{{ route('admin.github.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.github.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-code-branch w-5 mr-2"></i>
                        GitHub Commits
                    </a>
                    <a href="{{ route('settings.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('settings.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-cog w-5 mr-2"></i>
                        System Settings
                    </a>
                </div>
            </div>
        @endrole

        <!-- User Management for OM/PM (not admin section, separate) -->
        @role(['operation-manager', 'project-manager'])
            <div class="mb-2 sidebar-section" data-section="admin">
                <button
                    class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-users-cog w-5 text-pink-400 group-hover:text-pink-300 transition-colors"></i>
                        <span class="ml-3">User Management</span>
                    </div>
                    <i
                        class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron {{ $currentSection === 'admin' ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="sidebar-content mt-1 space-y-1 ml-4 {{ $currentSection !== 'admin' ? 'hidden' : '' }}">
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('users.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-users-cog w-5 mr-2"></i>
                        Manage All Users
                    </a>
                    <a href="{{ route('admin.github.index') }}"
                        class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('admin.github.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-code-branch w-5 mr-2"></i>
                        GitHub Commits
                    </a>
                </div>
            </div>
        @endrole
    </nav>

    <!-- User Section -->
    <div class="px-4 py-4 border-t border-indigo-700/50 flex-shrink-0">
        <div class="flex items-center mb-3">
            <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full ring-2 ring-indigo-400"
                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4F46E5&color=fff&size=128"
                    alt="{{ Auth::user()->name }}">
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-indigo-300">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}"
            class="flex items-center justify-center w-full px-3 py-2 text-xs font-medium rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 transition-colors">
            <i class="fas fa-user-edit mr-2"></i>
            Edit Profile
        </a>
    </div>
</aside>
