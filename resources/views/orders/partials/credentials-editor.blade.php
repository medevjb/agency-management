{{--
    Rich text editor for order credentials.
    Expects: $value (raw HTML string, may be empty).
--}}
<div>
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Credentials
        </label>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
            <i class="fas fa-lock text-[10px]"></i>
            Encrypted at rest
        </span>
    </div>

    <input type="hidden" name="credentials" id="credentialsInput" value="{{ $value }}">

    <div class="credentials-editor-shell rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
        <div id="credentialsEditor"></div>
    </div>

    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        <i class="fas fa-info-circle mr-1"></i>
        Logins, hosting panels, API keys and anything else the team needs to deliver this order.
    </p>
    @error('credentials')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
<style>
    #credentialsEditor {
        min-height: 160px;
        font-size: 0.875rem;
    }

    .credentials-editor-shell .ql-toolbar.ql-snow,
    .credentials-editor-shell .ql-container.ql-snow {
        border: 0;
    }

    .credentials-editor-shell .ql-toolbar.ql-snow {
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .credentials-editor-shell .ql-toolbar.ql-snow {
        border-bottom-color: #374151;
        background-color: #111827;
    }

    .dark .credentials-editor-shell .ql-editor {
        color: #f3f4f6;
    }

    .dark .credentials-editor-shell .ql-editor.ql-blank::before {
        color: #6b7280;
    }

    .dark .credentials-editor-shell .ql-snow .ql-stroke {
        stroke: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-fill {
        fill: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-picker {
        color: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-picker-options {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .credentials-editor-shell .ql-container.ql-snow {
        background-color: #1f2937;
    }

    .dark .credentials-editor-shell .ql-editor {
        background-color: #1f2937;
        color: #f3f4f6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editorElement = document.getElementById('credentialsEditor');
        const hiddenInput = document.getElementById('credentialsInput');

        if (!editorElement || !hiddenInput || typeof Quill === 'undefined') {
            return;
        }

        const editor = new Quill(editorElement, {
            theme: 'snow',
            placeholder: 'Hosting panel, client portal, API keys…',
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'code-block', 'blockquote'],
                    ['clean'],
                ],
            },
        });

        if (hiddenInput.value) {
            // Set the editor content properly from HTML
            editor.root.innerHTML = hiddenInput.value;
        }

        editorElement.closest('form').addEventListener('submit', function() {
            hiddenInput.value = editor.getText().trim() === '' ? '' : editor.root.innerHTML;
        });
    });
</script>
@endpush
