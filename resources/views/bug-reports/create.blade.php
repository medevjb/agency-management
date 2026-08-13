@extends('layouts.dashboard')

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">Report a Bug</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Submit a new bug report or feature request</p>
            </div>
            <a href="{{ route('bug-reports.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bugs
            </a>
        </div>
    </div>

    <!-- Bug Report Form -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
        <div class="p-6">
            <form method="POST" action="{{ route('bug-reports.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        placeholder="Brief summary of the bug">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="6" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        placeholder="Please describe the bug in detail. Include steps to reproduce if applicable.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <label for="bug_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Category @unless($aiTriageAvailable) <span class="text-red-500">*</span> @endunless
                        </label>
                        <select name="bug_category_id" id="bug_category_id" @unless($aiTriageAvailable) required @endunless
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="" {{ old('bug_category_id') ? '' : 'selected' }}>
                                {{ $aiTriageAvailable ? 'Auto-detect (AI will categorize)' : 'Select category' }}
                            </option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('bug_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bug_category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="bug_priority_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Priority @unless($aiTriageAvailable) <span class="text-red-500">*</span> @endunless
                        </label>
                        <select name="bug_priority_id" id="bug_priority_id" @unless($aiTriageAvailable) required @endunless
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="" {{ old('bug_priority_id') ? '' : 'selected' }}>
                                {{ $aiTriageAvailable ? 'Auto-detect (AI will categorize)' : 'Select priority level' }}
                            </option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ old('bug_priority_id') == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bug_priority_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Image Uploads -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Screenshots/Images <span class="text-gray-500 dark:text-gray-400">(Optional - Max 5 images, 5MB each)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-lg hover:border-indigo-500 transition">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="images" class="relative cursor-pointer bg-white dark:bg-gray-900 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none">
                                    <span>Upload files</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*" onchange="previewImages(event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, WEBP up to 5MB each</p>
                        </div>
                    </div>
                    <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('bug-reports.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Bug Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];

function previewImages(event) {
    const preview = document.getElementById('image-preview');
    const files = Array.from(event.target.files);

    // Add new files to the array (max 5 total)
    files.forEach(file => {
        if (selectedFiles.length < 5 && file.type.startsWith('image/')) {
            selectedFiles.push(file);
        }
    });

    // Limit to 5 files
    if (selectedFiles.length > 5) {
        selectedFiles = selectedFiles.slice(0, 5);
    }

    renderPreview();
}

function renderPreview() {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-700">
                <button type="button" onclick="removeImage(${index})" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                    <i class="fas fa-times text-sm"></i>
                </button>
                <span class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                    ${file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name}
                </span>
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    renderPreview();

    // Update the file input to reflect removed files
    const fileInput = document.getElementById('images');
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
}
</script>
@endsection
