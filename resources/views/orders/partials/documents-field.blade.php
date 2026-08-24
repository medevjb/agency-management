{{--
    Initial client documents upload field.
    Expects: $order (App\Models\Order|null) — existing documents are listed when present.
--}}
@php
    $existingDocuments = $order?->documents() ?? [];
@endphp

<div>
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Initial Client Documents
        </label>
        @if($existingDocuments)
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($existingDocuments) }} uploaded</span>
        @endif
    </div>

    <label for="clientDocumentsInput"
           id="clientDocumentsDropzone"
           class="doc-dropzone flex flex-col items-center justify-center gap-2 w-full px-4 py-6 rounded-lg cursor-pointer">
        <span class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
            <i class="fas fa-cloud-arrow-up"></i>
        </span>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Click to upload or drop files here</span>
        <span class="text-xs text-gray-500 dark:text-gray-400">PDF, Office, images, archives — up to 20 MB each</span>
        <input type="file" name="client_documents[]" id="clientDocumentsInput" multiple class="hidden"
               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.md,.json,.zip,.png,.jpg,.jpeg,.gif,.webp">
    </label>

    <ul id="clientDocumentsPreview" class="mt-3 space-y-2 hidden"></ul>

    @error('client_documents')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
    @foreach($errors->get('client_documents.*') as $documentErrors)
        @foreach($documentErrors as $documentError)
            <p class="mt-1 text-xs text-red-600">{{ $documentError }}</p>
        @endforeach
    @endforeach

    @if($existingDocuments)
        <div class="mt-4 space-y-2" id="existingDocumentsList">
            @foreach($existingDocuments as $document)
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                     data-document-row="{{ $document['id'] }}">
                    <span class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ \Illuminate\Support\Str::endsWith(strtolower($document['name']), ['.png', '.jpg', '.jpeg', '.gif', '.webp']) ? 'fa-file-image' : (\Illuminate\Support\Str::endsWith(strtolower($document['name']), '.pdf') ? 'fa-file-pdf' : (\Illuminate\Support\Str::endsWith(strtolower($document['name']), ['.zip']) ? 'fa-file-zipper' : 'fa-file-lines')) }}"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $document['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ number_format(($document['size'] ?? 0) / 1024, 1) }} KB
                            @if(! empty($document['uploaded_by_name']))
                                • {{ $document['uploaded_by_name'] }}
                            @endif
                            • {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('M d, Y') }}
                        </p>
                    </div>
                    <a href="{{ route('orders.documents.download', [$order, $document['id']]) }}"
                       class="px-2.5 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-colors">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                    <button type="button"
                            class="px-2.5 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors"
                            onclick="deleteOrderDocument('{{ route('orders.documents.destroy', [$order, $document['id']]) }}', '{{ $document['id'] }}')">
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
<style>
    .doc-dropzone {
        border: 2px dashed #d1d5db;
        background-color: #f9fafb;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .doc-dropzone:hover,
    .doc-dropzone.is-dragging {
        border-color: #FF7A3D;
        background-color: #FFF3EC;
    }

    .dark .doc-dropzone {
        border-color: #374151;
        background-color: #1f2937;
    }

    .dark .doc-dropzone:hover,
    .dark .doc-dropzone.is-dragging {
        border-color: #FF5A1F;
        background-color: rgba(255, 90, 31, 0.08);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('clientDocumentsInput');
        const dropzone = document.getElementById('clientDocumentsDropzone');
        const preview = document.getElementById('clientDocumentsPreview');

        if (!input || !preview) {
            return;
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function renderPreview() {
            const files = Array.from(input.files || []);

            if (files.length === 0) {
                preview.classList.add('hidden');
                preview.innerHTML = '';
                return;
            }

            preview.classList.remove('hidden');
            preview.innerHTML = files.map(file => `
                <li class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100">
                    <i class="fas fa-file-arrow-up text-indigo-500 dark:text-indigo-400"></i>
                    <span class="text-sm text-gray-800 dark:text-gray-200 truncate flex-1">${file.name}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">${formatSize(file.size)}</span>
                </li>
            `).join('');
        }

        input.addEventListener('change', renderPreview);

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone.addEventListener('drop', function(event) {
            input.files = event.dataTransfer.files;
            renderPreview();
        });
    });

    function deleteOrderDocument(url, documentId) {
        Swal.fire({
            title: 'Remove this document?',
            text: 'The file will be deleted from storage.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            axios.delete(url)
                .then(response => {
                    document.querySelector(`[data-document-row="${documentId}"]`)?.remove();
                    Swal.fire({
                        title: 'Removed',
                        text: response.data.message || 'Document removed.',
                        icon: 'success',
                        confirmButtonColor: '#D67D06',
                        timer: 1600,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: error.response?.data?.message || 'Could not remove the document.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                });
        });
    }
</script>
@endpush
