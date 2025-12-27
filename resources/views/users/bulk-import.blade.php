@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2 text-sm"></i>
                        <span class="text-sm font-medium">Back to Users</span>
                    </a>
                </li>
            </ol>
        </nav>
        <div class="mt-6">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Bulk Import Users</h1>
            <p class="text-gray-600 text-lg">Import multiple users from a CSV file</p>
        </div>
    </div>

    <!-- Import Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">CSV File Requirements</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• File must be in CSV format (.csv or .txt)</li>
                    <li>• First row should contain column headers</li>
                    <li>• Required columns: <code class="bg-blue-100 px-2 py-1 rounded">name</code>, <code class="bg-blue-100 px-2 py-1 rounded">email</code></li>
                    <li>• Optional columns: <code class="bg-blue-100 px-2 py-1 rounded">employee_id</code>, <code class="bg-blue-100 px-2 py-1 rounded">phone</code>, <code class="bg-blue-100 px-2 py-1 rounded">department</code>, <code class="bg-blue-100 px-2 py-1 rounded">position</code>, <code class="bg-blue-100 px-2 py-1 rounded">hire_date</code>, <code class="bg-blue-100 px-2 py-1 rounded">salary</code>, <code class="bg-blue-100 px-2 py-1 rounded">address</code>, <code class="bg-blue-100 px-2 py-1 rounded">is_active</code>, <code class="bg-blue-100 px-2 py-1 rounded">role</code>, <code class="bg-blue-100 px-2 py-1 rounded">team_id</code>, <code class="bg-blue-100 px-2 py-1 rounded">tech_stack_id</code></li>
                    <li>• Date format: <code class="bg-blue-100 px-2 py-1 rounded">YYYY-MM-DD</code> or <code class="bg-blue-100 px-2 py-1 rounded">MM/DD/YYYY</code></li>
                    <li>• <code class="bg-blue-100 px-2 py-1 rounded">is_active</code> should be <code class="bg-blue-100 px-2 py-1 rounded">yes</code> or <code class="bg-blue-100 px-2 py-1 rounded">no</code></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Download Sample CSV -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Sample CSV Template</h3>
                <p class="text-sm text-gray-600">Download a sample CSV file to understand the format</p>
            </div>
            <a href="{{ route('users.export') }}" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Download Sample CSV
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
        <form id="bulkImportForm" enctype="multipart/form-data">
            @csrf

            <!-- File Upload -->
            <div class="mb-8">
                <label for="csv_file" class="block text-sm font-bold text-gray-700 mb-3">
                    <i class="fas fa-file-csv mr-2 text-indigo-500"></i>
                    Select CSV File
                </label>
                <div class="relative">
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                           class="w-full px-5 py-4 border-2 border-dashed border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <p class="mt-2 text-sm text-gray-500">Maximum file size: 10MB</p>
            </div>

            <!-- Default Values -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Default Role -->
                <div>
                    <label for="default_role" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-2 text-indigo-500"></i>Default Role
                    </label>
                    <div class="relative">
                        <select id="default_role" name="default_role"
                                class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                            <option value="">No default role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Applied to all imported users if not specified in CSV</p>
                </div>

                <!-- Default Team -->
                <div>
                    <label for="default_team" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-users mr-2 text-indigo-500"></i>Default Team
                    </label>
                    <div class="relative">
                        <select id="default_team" name="default_team"
                                class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                            <option value="">No default team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Applied to all imported users if not specified in CSV</p>
                </div>

                <!-- Default Tech Stack -->
                <div>
                    <label for="default_tech_stack" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-code mr-2 text-indigo-500"></i>Default Tech Stack
                    </label>
                    <div class="relative">
                        <select id="default_tech_stack" name="default_tech_stack"
                                class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                            <option value="">No default tech stack</option>
                            @foreach($techStacks as $techStack)
                                <option value="{{ $techStack->id }}">{{ $techStack->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Applied to all imported users if not specified in CSV</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-file-import mr-3"></i>
                    Import Users
                </button>
            </div>
        </form>
    </div>

    <!-- Import Results (Hidden initially) -->
    <div id="importResults" class="hidden mt-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Import Results</h3>
            <div id="resultsContent"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('bulkImportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Importing...';

    axios.post('/users/bulk-import', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        const data = response.data;
        if (data.success) {
            showImportResults(data);
            if (data.errors.length === 0 && data.duplicates.length === 0) {
                // Redirect to users list after successful import
                setTimeout(() => {
                    window.location.href = '/users';
                }, 3000);
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Import Failed',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            } else {
                alert('Import failed: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while importing users',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        } else {
            alert('An error occurred while importing users');
        }
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});

function showImportResults(data) {
    const resultsDiv = document.getElementById('importResults');
    const resultsContent = document.getElementById('resultsContent');

    let html = '';

    // Success message
    html += `
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <div>
                    <p class="text-green-900 font-semibold">${data.message}</p>
                    <p class="text-green-700 text-sm">Successfully imported ${data.imported_count} users</p>
                </div>
            </div>
        </div>
    `;

    // Imported users
    if (data.imported_users.length > 0) {
        html += `
            <div class="mb-4">
                <h4 class="font-semibold text-gray-900 mb-2">Successfully Imported Users (${data.imported_users.length}):</h4>
                <div class="bg-green-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    <ul class="space-y-1">
                        ${data.imported_users.map(user => `<li class="text-green-800 text-sm">• ${user}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    // Errors
    if (data.errors.length > 0) {
        html += `
            <div class="mb-4">
                <h4 class="font-semibold text-gray-900 mb-2">Errors (${data.errors.length}):</h4>
                <div class="bg-red-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    <ul class="space-y-1">
                        ${data.errors.map(error => `<li class="text-red-800 text-sm">• ${error}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    // Duplicates
    if (data.duplicates.length > 0) {
        html += `
            <div class="mb-4">
                <h4 class="font-semibold text-gray-900 mb-2">Skipped (Duplicates) (${data.duplicates.length}):</h4>
                <div class="bg-yellow-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    <ul class="space-y-1">
                        ${data.duplicates.map(duplicate => `<li class="text-yellow-800 text-sm">• ${duplicate}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    resultsContent.innerHTML = html;
    resultsDiv.classList.remove('hidden');

    // Scroll to results
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

// File upload preview
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2);

        // Create file info display
        const fileInfo = document.createElement('div');
        fileInfo.className = 'mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between';
        fileInfo.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-file-csv text-indigo-600 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900">${fileName}</p>
                    <p class="text-xs text-gray-500">${fileSize} KB</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Remove any existing file info
        const existingInfo = e.target.parentNode.querySelector('.mt-3');
        if (existingInfo) {
            existingInfo.remove();
        }

        e.target.parentNode.appendChild(fileInfo);
    }
});
</script>
@endpush
@endsection