@props([
    'name',
    'label',
    'users'       => [],
    'selected'    => '',
    'placeholder' => 'Choose...',
    'icon'        => 'fa-user-tie',
    'uid'         => null,
])

@php
    $pickerId     = $uid ?? preg_replace('/[^a-z0-9]/i', '_', $name);
    $selectedValue = (string) old($name, $selected);
@endphp

<div>
    <label for="{{ $pickerId }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        <i class="fas {{ $icon }} mr-2 text-purple-500 dark:text-purple-400"></i>{{ $label }}
    </label>

    <div class="relative" id="{{ $pickerId }}Container">
        <input type="hidden" name="{{ $name }}" id="{{ $pickerId }}" value="{{ $selectedValue }}">

        <button type="button" id="{{ $pickerId }}Trigger"
                class="w-full px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 shadow-sm transition-all duration-200 text-left flex items-center justify-between">
            <div id="{{ $pickerId }}Display" class="flex items-center flex-1 min-w-0">
                <span class="text-gray-400 dark:text-gray-500">{{ $placeholder }}</span>
            </div>
            <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-200 ml-2" id="{{ $pickerId }}Chevron"></i>
        </button>

        <div id="{{ $pickerId }}Dropdown"
             class="hidden absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-visible">
            <div class="p-3 border-b border-gray-100 dark:border-gray-700 rounded-t-xl bg-white dark:bg-gray-800">
                <div class="relative">
                    <input type="text" id="{{ $pickerId }}Search" placeholder="Search by name or employee ID..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                </div>
            </div>
            <div id="{{ $pickerId }}Options"
                 class="min-h-[12rem] max-h-80 overflow-y-auto overflow-x-hidden p-2 pb-3 border-b border-gray-100 dark:border-gray-700"
                 style="overscroll-behavior: contain;"></div>
            <div class="p-2 border-t border-gray-100 dark:border-gray-700 rounded-b-xl bg-white dark:bg-gray-800">
                <button type="button" id="{{ $pickerId }}Clear"
                        class="w-full px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear selection
                </button>
            </div>
        </div>
    </div>

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </p>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    (function () {
        const uid = '{{ $pickerId }}';

        var pickerUsers = @json($users);
        var pickerSelectedId = '{{ $selectedValue }}';
        var pickerOpen = false;

        var triggerBtn = document.getElementById(uid + 'Trigger');
        var dropdown = document.getElementById(uid + 'Dropdown');
        var searchInput = document.getElementById(uid + 'Search');
        var optionsContainer = document.getElementById(uid + 'Options');
        var hiddenInput = document.getElementById(uid);
        var displayEl = document.getElementById(uid + 'Display');
        var chevron = document.getElementById(uid + 'Chevron');
        var clearBtn = document.getElementById(uid + 'Clear');

        function renderOptions(filter) {
            var search = (filter || '').toLowerCase();
            var html = '';
            var count = 0;

            pickerUsers.forEach(function (user) {
                var name = user.name.toLowerCase();
                var empId = (user.employee_id || '').toLowerCase();
                var position = (user.position || '').toLowerCase();

                if (search && !name.includes(search) && !empId.includes(search) && !position.includes(search)) {
                    return;
                }

                var isSelected = String(user.id) === String(pickerSelectedId);
                var selectedClass = isSelected ? 'selected bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-800' : 'border-transparent';
                var checkIcon = isSelected ? '<i class="fas fa-check text-indigo-600 dark:text-indigo-400 ml-auto"></i>' : '';

                html += '<div class="leader-option flex items-center p-3 rounded-lg cursor-pointer border-2 ' + selectedClass + ' mb-1" data-id="' + user.id + '">' +
                    '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=4F46E5&color=fff&size=40" class="w-10 h-10 rounded-full mr-3 flex-shrink-0">' +
                    '<div class="flex-1 min-w-0">' +
                        '<div class="font-semibold text-gray-900 dark:text-gray-100 truncate">' + user.name + '</div>' +
                        '<div class="text-sm text-gray-500 dark:text-gray-400">' + (user.employee_id || 'No ID') + (user.position ? ' &bull; ' + user.position : '') + '</div>' +
                    '</div>' +
                    checkIcon +
                '</div>';
                count++;
            });

            if (count === 0) {
                html = '<div class="p-4 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-search mr-2"></i>No users found</div>';
            }

            optionsContainer.innerHTML = html;

            optionsContainer.querySelectorAll('.leader-option').forEach(function (el) {
                el.addEventListener('click', function () {
                    selectPicker(this.getAttribute('data-id'));
                });
            });
        }

        function selectPicker(userId) {
            pickerSelectedId = userId;
            hiddenInput.value = userId;

            var user = pickerUsers.find(function (u) { return String(u.id) === String(userId); });
            if (user) {
                displayEl.innerHTML =
                    '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=4F46E5&color=fff&size=40" class="w-8 h-8 rounded-full mr-3 flex-shrink-0">' +
                    '<div class="min-w-0">' +
                        '<div class="font-semibold text-gray-900 dark:text-gray-100 truncate">' + user.name + '</div>' +
                        '<div class="text-xs text-gray-500 dark:text-gray-400">' + (user.employee_id || 'No ID') + '</div>' +
                    '</div>';
            }

            closePicker();
        }

        function clearPicker() {
            pickerSelectedId = '';
            hiddenInput.value = '';
            displayEl.innerHTML = '<span class="text-gray-400 dark:text-gray-500">{{ $placeholder }}</span>';
            closePicker();
        }

        function openPicker() {
            pickerOpen = true;
            dropdown.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            searchInput.value = '';
            renderOptions('');
            setTimeout(function () { searchInput.focus(); }, 50);
        }

        function closePicker() {
            pickerOpen = false;
            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }

        triggerBtn.addEventListener('click', function () {
            if (pickerOpen) {
                closePicker();
            } else {
                openPicker();
            }
        });

        searchInput.addEventListener('input', function () {
            renderOptions(this.value);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closePicker();
            }
        });

        clearBtn.addEventListener('click', function () {
            clearPicker();
        });

        document.addEventListener('click', function (e) {
            var container = document.getElementById(uid + 'Container');
            if (!container.contains(e.target)) {
                closePicker();
            }
        });

        if (pickerSelectedId) {
            selectPicker(pickerSelectedId);
        }
    })();
});
</script>
@endpush