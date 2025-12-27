@extends('layouts.dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-cog text-indigo-600 mr-2"></i>
                System Settings
            </h1>
            <p class="mt-1 text-sm text-gray-500">Manage your application settings and preferences</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar - Settings Groups -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Settings Groups</h2>
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
                                    'storage' => 'hdd',
                                    'maintenance' => 'tools',
                                    default => 'cog'
                                };
                            @endphp
                            <a href="{{ route('settings.group', ['group' => $groupItem]) }}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                                      {{ $group === $groupItem
                                          ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600'
                                          : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-{{ $icon }} mr-3 w-5 text-center"></i>
                                {{ ucfirst($groupItem) }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Main Content - Settings Form -->
            <div class="flex-1">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
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
                                'storage' => 'hdd',
                                'maintenance' => 'tools',
                                default => 'cog'
                            };
                        @endphp
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-{{ $headerIcon }} text-indigo-600 mr-2"></i>
                            {{ ucfirst($group) }} Settings
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">Configure your {{ $group }} settings below</p>
                    </div>

                    @if($group === 'currency')
                        <!-- Currency Preview -->
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 border-b border-indigo-100">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">
                                <i class="fas fa-eye mr-2 text-indigo-600"></i>Currency Preview
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="text-xs text-gray-500 mb-1">Small Amount</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ \App\Helpers\CurrencyHelper::format(150.50) }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="text-xs text-gray-500 mb-1">Medium Amount</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ \App\Helpers\CurrencyHelper::format(1500.75) }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="text-xs text-gray-500 mb-1">Large Amount</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ \App\Helpers\CurrencyHelper::format(150000.00) }}</div>
                                </div>
                            </div>
                            <div class="mt-4 text-xs text-gray-600">
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
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                                    <i class="fas fa-cog text-2xl text-indigo-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Settings Available</h3>
                                <p class="text-gray-500">There are no settings configured for this group yet.</p>
                            </div>
                        @else
                            <div class="p-6">
                                <div class="space-y-6">
                                    @foreach($settings as $key => $setting)
                                <div class="setting-field bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    @if($setting->type === 'image')
                                        <!-- Image Upload Field -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $setting->label }}
                                            </label>
                                            @if($setting->value)
                                                <div class="mb-3 relative inline-block">
                                                    <img src="{{ asset('storage/' . $setting->value) }}"
                                                         alt="{{ $setting->label }}"
                                                         class="h-32 rounded-lg border border-gray-300">
                                                    <a href="{{ route('settings.delete-value', ['key' => $key]) }}"
                                                       onclick="return confirm('Are you sure you want to remove this image?')"
                                                       class="absolute -top-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-red-600 shadow-lg">
                                                        <i class="fas fa-times text-sm"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="mt-2 flex items-center gap-3">
                                                <label class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-cloud-upload-alt text-indigo-600 mr-2"></i>
                                                    <span class="text-sm text-gray-700">Choose File</span>
                                                    <input type="file" name="{{ $key }}" class="hidden" accept="image/*">
                                                </label>
                                                <span class="text-sm text-gray-500">JPG, PNG, GIF up to 2MB</span>
                                            </div>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'textarea')
                                        <!-- Textarea Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <textarea name="{{ $key }}"
                                                      id="{{ $key }}"
                                                      rows="3"
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old($key, $setting->value) }}</textarea>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'checkbox' || $setting->type === 'boolean')
                                        <!-- Checkbox Field -->
                                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                                            <div>
                                                <label for="{{ $key }}" class="text-sm font-medium text-gray-700 cursor-pointer">
                                                    {{ $setting->label }}
                                                </label>
                                                @if($setting->description)
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $setting->description }}</p>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <input type="checkbox"
                                                       name="{{ $key }}"
                                                       id="{{ $key }}"
                                                       value="1"
                                                       {{ $setting->getValue() ? 'checked' : '' }}
                                                       class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            </div>
                                        </div>

                                    @elseif($setting->type === 'select')
                                        <!-- Select Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <select name="{{ $key }}"
                                                    id="{{ $key }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'number')
                                        <!-- Number Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <input type="number"
                                                       name="{{ $key }}"
                                                       id="{{ $key }}"
                                                       value="{{ old($key, $setting->value) }}"
                                                       min="0"
                                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                @if($key === 'password_expiry_days' || $key === 'api_key_expiry_days' || $key === 'auto_delete_trash_days')
                                                    <span class="text-sm font-medium text-gray-600 min-w-16">days</span>
                                                @elseif($key === 'session_timeout' || $key === 'login_lockout_duration' || $key === 'task_reminder_hours')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">minutes</span>
                                                @elseif($key === 'password_min_length')
                                                    <span class="text-sm font-medium text-gray-600 min-w-16">chars</span>
                                                @elseif($key === 'items_per_page')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">items</span>
                                                @elseif($key === 'max_tasks_per_project' || $key === 'max_teams_per_user' || $key === 'max_team_members')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">max</span>
                                                @elseif($key === 'login_max_attempts' || $key === 'api_rate_limit')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">attempts</span>
                                                @elseif($key === 'max_file_size')
                                                    <span class="text-sm font-medium text-gray-600 min-w-16">MB</span>
                                                @elseif($key === 'storage_limit_per_user')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">MB</span>
                                                @elseif($key === 'decimal_places')
                                                    <span class="text-sm font-medium text-gray-600 min-w-20">places</span>
                                                @elseif($key === 'tax_rate')
                                                    <span class="text-sm font-medium text-gray-600 min-w-16">%</span>
                                                @else
                                                    <span class="text-sm font-medium text-gray-600 min-w-16"></span>
                                                @endif
                                            </div>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @elseif($setting->type === 'email')
                                        <!-- Email Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <input type="email"
                                                   name="{{ $key }}"
                                                   id="{{ $key }}"
                                                   value="{{ old($key, $setting->value) }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>

                                    @else
                                        <!-- Default Text Field -->
                                        <div>
                                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ $setting->label }}
                                            </label>
                                            <input type="text"
                                                   name="{{ $key }}"
                                                   id="{{ $key }}"
                                                   value="{{ old($key, $setting->value) }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
                                    <a href="{{ route('dashboard') }}"
                                       class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
