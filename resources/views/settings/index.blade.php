@extends('layouts.dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                <i class="fas fa-cog text-indigo-600 dark:text-indigo-400 mr-2"></i>
                System Settings
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your application settings and preferences</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-500/10 border-l-4 border-green-400 dark:border-green-600 p-4 rounded-md">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 dark:text-green-500 mt-0.5"></i>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar - Settings Groups -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Settings Groups</h2>
                    </div>
                    <nav class="p-2 space-y-1">
                        @foreach($groups as $groupItem)
                            @php
                                $icon = match($groupItem) {
                                    'general' => 'sliders-h',
                                    'company' => 'building',
                                    'currency' => 'dollar-sign',
                                    'email' => 'envelope',
                                    'security' => 'shield-alt',
                                    'notifications' => 'bell',
                                    'task' => 'tasks',
                                    'team' => 'users',
                                    'project' => 'project-diagram',
                                    'storage' => 'hdd',
                                    'maintenance' => 'tools',
                                    default => 'cog'
                                };
                            @endphp
                            <a href="{{ route('settings.group', ['group' => $groupItem]) }}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                                      {{ $group === $groupItem
                                          ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-l-4 border-indigo-600'
                                          : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <i class="fas fa-{{ $icon }} mr-3 w-5 text-center"></i>
                                {{ ucfirst($groupItem) }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Main Content - Settings Form -->
            <div class="flex-1">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        @php
                            $headerIcon = match($group) {
                                'general' => 'sliders-h',
                                'company' => 'building',
                                'currency' => 'dollar-sign',
                                'email' => 'envelope',
                                'security' => 'shield-alt',
                                'notifications' => 'bell',
                                'task' => 'tasks',
                                'team' => 'users',
                                'project' => 'project-diagram',
                                'storage' => 'hdd',
                                'maintenance' => 'tools',
                                default => 'cog'
                            };
                        @endphp
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-{{ $headerIcon }} text-indigo-600 dark:text-indigo-400 mr-2"></i>
                            {{ ucfirst($group) }} Settings
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure your {{ $group }} settings below</p>
                    </div>

                    @if($group === 'currency')
                        <!-- Currency Preview -->
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 p-6 border-b border-indigo-100 dark:border-indigo-900">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                <i class="fas fa-eye mr-2 text-indigo-600 dark:text-indigo-400"></i>Currency Preview
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Small Amount</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Helpers\CurrencyHelper::format(150.50) }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Medium Amount</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Helpers\CurrencyHelper::format(1500.75) }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Large Amount</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Helpers\CurrencyHelper::format(150000.00) }}</div>
                                </div>
                            </div>
                            <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                                <strong>Current Settings:</strong>
                                Code: <span class="font-mono">{{ \App\Helpers\CurrencyHelper::getCode() }}</span> |
                                Symbol: <span class="font-mono">{{ \App\Helpers\CurrencyHelper::getSymbol() }}</span> |
                                Position: <span class="font-mono">{{ \App\Helpers\CurrencyHelper::getPosition() }}</span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update', ['group' => $group]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @if($settings->isEmpty())
                            <!-- Empty State -->
                            <div class="p-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-500/10 mb-4">
                                    <i class="fas fa-cog text-2xl text-indigo-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Settings Available</h3>
                                <p class="text-gray-500 dark:text-gray-400">There are no settings configured for this group yet.</p>
                            </div>
                        @else
                            <div class="p-6">
                                <div class="space-y-6">
                                    @foreach($settings as $key => $setting)
                                <div class="setting-field bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    @if($setting->type === 'image')
                                        <!-- Image Upload Field -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ $setting->label }}
                                            </label>
                                            @if($setting->value)
                                                <div class="mb-3 relative inline-block">
                                                    <img src="{{ asset('storage/' . $setting->value) }}"
                                                         alt="{{ $setting->label }}"
                                                         class="h-32 rounded-lg border border-gray-300 dark:border-gray-700">
                                                    <button type="button"
                                                       onclick="removeSettingImage('{{ route('settings.delete-value', ['key' => $key]) }}')"
                                                       class="absolute -top-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-red-600 shadow-lg">
                                                        <i class="fas fa-times text-sm"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            <div class="mt-2 flex items-center gap-3">
                                                <label class="flex items-center px-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                                    <i class="fas fa-cloud-upload-alt text-indigo-600 dark:text-indigo-400 mr-2"></i>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">Choose File</span>
                                                    <input type="file" name="{{ $key }}" class="hidden" accept="image/*">
                                                </label>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">JPG, PNG, GIF up to 2MB</span>
                                            </div>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'textarea')
                                        <!-- Textarea Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <textarea name="{{ $key }}"
                                                      id="{{ $key }}"
                                                      rows="3"
                                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">{{ old($key, $setting->value) }}</textarea>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'checkbox' || $setting->type === 'boolean')
                                        <!-- Checkbox Field -->
                                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                            <div>
                                                <label for="{{ $key }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                                    {{ $setting->label }}
                                                </label>
                                                @if($setting->description)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $setting->description }}</p>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <input type="checkbox"
                                                       name="{{ $key }}"
                                                       id="{{ $key }}"
                                                       value="1"
                                                       {{ $setting->getValue() ? 'checked' : '' }}
                                                       class="w-5 h-5 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                            </div>
                                        </div>

                                    @elseif($setting->type === 'select')
                                        <!-- Select Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <select name="{{ $key }}"
                                                    id="{{ $key }}"
                                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                                @if($key === 'timezone')
                                                    @foreach(['UTC', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Tokyo', 'Asia/Shanghai', 'Australia/Sydney'] as $tz)
                                                        <option value="{{ $tz }}" {{ $setting->value === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                                    @endforeach
                                                @elseif($key === 'date_format')
                                                    @foreach(['M j, Y' => 'Dec 23, 2025', 'F j, Y' => 'December 23, 2025', 'Y-m-d' => '2025-12-23', 'd/m/Y' => '23/12/2025', 'm/d/Y' => '12/23/2025'] as $value => $label)
                                                        <option value="{{ $value }}" {{ $setting->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                @elseif($key === 'time_format')
                                                    <option value="12" {{ $setting->value === '12' ? 'selected' : '' }}>12-hour</option>
                                                    <option value="24" {{ $setting->value === '24' ? 'selected' : '' }}>24-hour</option>
                                                @elseif($key === 'language')
                                                    @foreach(['en' => 'English', 'es' => 'Spanish', 'fr' => 'French', 'de' => 'German', 'ar' => 'Arabic'] as $code => $name)
                                                        <option value="{{ $code }}" {{ $setting->value === $code ? 'selected' : '' }}>{{ $name }}</option>
                                                    @endforeach
                                                @elseif($key === 'currency_code')
                                                    @foreach(['USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound', 'JPY' => 'Japanese Yen', 'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar', 'BDT' => 'Bangladeshi Taka', 'INR' => 'Indian Rupee', 'CNY' => 'Chinese Yuan', 'SAR' => 'Saudi Riyal', 'AED' => 'UAE Dirham'] as $code => $name)
                                                        <option value="{{ $code }}" {{ $setting->value === $code ? 'selected' : '' }}>{{ $code }} - {{ $name }}</option>
                                                    @endforeach
                                                @elseif($key === 'currency_position')
                                                    <option value="before" {{ $setting->value === 'before' ? 'selected' : '' }}>Before Amount ($100)</option>
                                                    <option value="after" {{ $setting->value === 'after' ? 'selected' : '' }}>After Amount (100$)</option>
                                                @elseif($key === 'mail_encryption')
                                                    <option value="tls" {{ $setting->value === 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ $setting->value === 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    <option value="" {{ $setting->value === '' ? 'selected' : '' }}>None</option>
                                                @elseif($key === 'mail_mailer')
                                                    <option value="smtp" {{ $setting->value === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                    <option value="sendmail" {{ $setting->value === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                                    <option value="mailgun" {{ $setting->value === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                    <option value="ses" {{ $setting->value === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                                    <option value="postmark" {{ $setting->value === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                                    <option value="log" {{ $setting->value === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                                @elseif($key === 'default_task_priority')
                                                    @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $priority => $label)
                                                        <option value="{{ $priority }}" {{ $setting->value === $priority ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                @elseif($key === 'default_theme')
                                                    <option value="light" {{ $setting->value === 'light' ? 'selected' : '' }}>Light</option>
                                                    <option value="dark" {{ $setting->value === 'dark' ? 'selected' : '' }}>Dark</option>
                                                    <option value="auto" {{ $setting->value === 'auto' ? 'selected' : '' }}>Auto (System)</option>
                                                @elseif($key === 'sidebar_position')
                                                    <option value="left" {{ $setting->value === 'left' ? 'selected' : '' }}>Left</option>
                                                    <option value="right" {{ $setting->value === 'right' ? 'selected' : '' }}>Right</option>
                                                @else
                                                    <option value="">Select...</option>
                                                @endif
                                            </select>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'number')
                                        <!-- Number Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <input type="number"
                                                       name="{{ $key }}"
                                                       id="{{ $key }}"
                                                       value="{{ old($key, $setting->value) }}"
                                                       min="0"
                                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                                @if($key === 'password_expiry_days' || $key === 'api_key_expiry_days' || $key === 'auto_delete_trash_days')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-16">days</span>
                                                @elseif($key === 'session_timeout' || $key === 'login_lockout_duration' || $key === 'task_reminder_hours')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">minutes</span>
                                                @elseif($key === 'password_min_length')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-16">chars</span>
                                                @elseif($key === 'items_per_page')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">items</span>
                                                @elseif($key === 'max_tasks_per_project' || $key === 'max_teams_per_user' || $key === 'max_team_members')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">max</span>
                                                @elseif($key === 'login_max_attempts' || $key === 'api_rate_limit')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">attempts</span>
                                                @elseif($key === 'max_file_size')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-16">MB</span>
                                                @elseif($key === 'storage_limit_per_user')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">MB</span>
                                                @elseif($key === 'decimal_places')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-20">places</span>
                                                @elseif($key === 'tax_rate')
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-16">%</span>
                                                @else
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-16"></span>
                                                @endif
                                            </div>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'email')
                                        <!-- Email Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <input type="email"
                                                   name="{{ $key }}"
                                                   id="{{ $key }}"
                                                   value="{{ old($key, $setting->value) }}"
                                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @else
                                        <!-- Default Text Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <input type="text"
                                                   name="{{ $key }}"
                                                   id="{{ $key }}"
                                                   value="{{ old($key, $setting->value) }}"
                                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                                    <a href="{{ route('dashboard') }}"
                                       class="px-5 py-2.5 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                        <i class="fas fa-times mr-1.5"></i> Cancel
                                    </a>
                                    <button type="submit"
                                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                        <i class="fas fa-save mr-1.5"></i> Save Settings
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>

                    @if($group === 'project')
                        <!-- Project Status Management -->
                        <div class="border-t border-gray-200 dark:border-gray-800 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                <i class="fas fa-project-diagram text-indigo-600 dark:text-indigo-400 mr-2"></i>
                                Project Status Management
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Manage and customize project statuses that can be used across the application</p>

                            <!-- Add New Status Form -->
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Add New Status</h4>
                                <form method="POST" action="{{ route('settings.project-statuses.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    @csrf
                                    <div>
                                        <input type="text" name="key" placeholder="Key (e.g., in-review)" required
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                               pattern="[a-z0-9-]+" title="Only lowercase letters, numbers, and hyphens">
                                    </div>
                                    <div>
                                        <input type="text" name="label" placeholder="Label (e.g., In Review)" required
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <input type="text" name="icon" placeholder="Icon (e.g., 👀)" required
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <input type="text" name="color" placeholder="Color (e.g., bg-purple-100 text-purple-800)" required
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                    </div>
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Add Status
                                    </button>
                                </form>
                            </div>

                            <!-- Statuses List -->
                            <div class="space-y-3">
                                @php
                                    $projectStatuses = \App\Models\Setting::getProjectStatuses();
                                @endphp
                                @foreach($projectStatuses as $key => $status)
                                    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow">
                                        <div class="flex items-center space-x-4">
                                            <span class="text-2xl">{{ $status['icon'] }}</span>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $status['label'] }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $key }}</div>
                                            </div>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $status['color'] }}">
                                                {{ $status['label'] }}
                                            </span>
                                        </div>
                                        @if(!in_array($key, ['nra', 'pending', 'in-progress', 'completed', 'on-hold', 'cancelled']))
                                            <div class="flex items-center space-x-2">
                                                <button type="button" onclick="editStatus('{{ $key }}', '{{ $status['label'] }}', '{{ $status['icon'] }}', '{{ $status['color'] }}')"
                                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('settings.project-statuses.destroy', ['key' => $key]) }}" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this status?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">Default status</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div id="editStatusModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative mx-auto p-6 border-0 shadow-xl rounded-2xl bg-white dark:bg-gray-900 w-full max-w-lg mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Project Status</h3>
            <button onclick="closeEditModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editStatusForm" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="original_key" id="edit_original_key" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                    <input type="text" name="label" id="edit_label" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                    <input type="text" name="icon" id="edit_icon" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                    <input type="text" name="color" id="edit_color" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function removeSettingImage(url) {
    if (!confirm('Are you sure you want to remove this image?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);

    document.body.appendChild(form);
    form.submit();
}

function editStatus(key, label, icon, color) {
    document.getElementById('edit_original_key').value = key;
    document.getElementById('edit_label').value = label;
    document.getElementById('edit_icon').value = icon;
    document.getElementById('edit_color').value = color;

    const form = document.getElementById('editStatusForm');
    form.action = `/settings/project-statuses/${key}`;

    document.getElementById('editStatusModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editStatusModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>
@endsection
