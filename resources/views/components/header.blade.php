<header class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-4">
                <div class="relative">
                    <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                </div>
            </div>

            <!-- Right side items -->
            <div class="flex items-center space-x-4">
                <!-- Global Month Picker -->
                @isset($globalSelectedMonth)
                    <div class="relative month-picker" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button"
                                class="month-picker-trigger" aria-haspopup="true" :aria-expanded="open">
                            <i class="fas fa-calendar-days"></i>
                            <span>{{ $globalAvailableMonths[$globalSelectedMonth] ?? $globalSelectedMonth }}</span>
                            <i class="fas fa-chevron-down month-picker-chevron" :class="{ 'is-open': open }"></i>
                        </button>

                        <div x-show="open" x-transition x-cloak @click.away="open = false"
                             class="month-picker-panel">
                            <p class="month-picker-heading">Viewing data for</p>
                            <div class="month-picker-list">
                                @foreach($globalAvailableMonths as $value => $label)
                                    <form method="POST" action="{{ route('selected-month.update') }}">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $value }}">
                                        <button type="submit"
                                                class="month-picker-option {{ $value === $globalSelectedMonth ? 'is-active' : '' }}">
                                            {{ $label }}
                                            @if($value === $globalSelectedMonth)
                                                <i class="fas fa-check"></i>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            <p class="month-picker-footnote">Applies to every dashboard until you change it again.</p>
                        </div>
                    </div>
                @endisset

                <!-- Theme Toggle -->
                <button id="themeToggle" type="button" title="Toggle dark mode" aria-label="Toggle dark mode"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 dark:text-amber-300 hover:text-indigo-600 dark:hover:text-amber-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <span class="dark:hidden"><i class="fas fa-moon text-sm"></i></span>
                    <span class="hidden dark:inline-block"><i class="fas fa-sun text-base"></i></span>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="relative text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50" style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                @foreach(auth()->user()->unreadNotifications()->take(5)->get() as $notification)
                                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No new notifications
                                </div>
                            @endif
                        </div>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">View all notifications</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Messages Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="relative text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                        <i class="fas fa-envelope text-xl"></i>
                    </button>

                    <!-- Messages Dropdown Panel -->
                    <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50" style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Messages</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                No new messages
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button id="user-dropdown-btn"
                            class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4F46E5&color=fff" alt="{{ Auth::user()->name }}">
                        <span class="hidden md:block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>

                    <div id="user-dropdown"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 hidden">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
