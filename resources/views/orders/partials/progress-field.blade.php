{{--
    Order completion progress.
    Expects: $value (int 0-100).
--}}
<div>
    <label for="progressRange" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Progress
    </label>

    <div class="progress-track" id="progressPreview">
        <div class="progress-fill" id="progressBarFill" style="width: {{ $value }}%"></div>
        <span class="progress-label" id="progressLabelBase">{{ $value }}%</span>
        <span class="progress-label-clip" id="progressLabelClip" style="width: {{ $value }}%">
            <span class="progress-label" id="progressLabelInner" style="width: {{ $value > 0 ? 10000 / $value : 100 }}%">{{ $value }}%</span>
        </span>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-4">
        <input type="range" name="progress" id="progressRange" min="0" max="100" step="5" value="{{ $value }}"
               class="progress-range">
        <div class="flex gap-1">
            @foreach([0, 25, 50, 75, 100] as $preset)
                <button type="button" data-progress-preset="{{ $preset }}"
                        class="px-2 py-1 text-[11px] font-semibold rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ $preset }}%
                </button>
            @endforeach
        </div>
    </div>

    @error('progress')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('styles')
<style>
    .progress-range {
        flex: 1;
        min-width: 11rem;
        height: 0.5rem;
        border-radius: 9999px;
        appearance: none;
        cursor: pointer;
        background-color: #e5e7eb;
        accent-color: #F0430A;
    }

    .dark .progress-range {
        background-color: #374151;
    }

    .progress-track {
        position: relative;
        height: 2.25rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .dark .progress-track {
        background-color: #1f2937;
    }

    .progress-fill {
        position: absolute;
        inset: 0 auto 0 0;
        border-radius: 9999px;
        background-image: linear-gradient(90deg, #F0430A 0%, #F59A0F 100%);
        transition: width 250ms ease;
    }

    .progress-label {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        color: #374151;
    }

    .dark .progress-label {
        color: #d1d5db;
    }

    .progress-label-clip {
        position: absolute;
        inset: 0 auto 0 0;
        overflow: hidden;
        transition: width 250ms ease;
    }

    .progress-label-clip .progress-label {
        color: #ffffff;
    }

    @media (prefers-reduced-motion: reduce) {
        .progress-fill,
        .progress-label-clip {
            transition: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const range = document.getElementById('progressRange');
        const fill = document.getElementById('progressBarFill');
        const base = document.getElementById('progressLabelBase');
        const clip = document.getElementById('progressLabelClip');
        const inner = document.getElementById('progressLabelInner');

        if (!range) {
            return;
        }

        function syncProgress() {
            const value = parseInt(range.value) || 0;

            fill.style.width = value + '%';
            clip.style.width = value + '%';
            inner.style.width = (value > 0 ? 10000 / value : 100) + '%';
            base.textContent = value + '%';
            inner.textContent = value + '%';
        }

        range.addEventListener('input', syncProgress);

        document.querySelectorAll('[data-progress-preset]').forEach(button => {
            button.addEventListener('click', function() {
                range.value = this.dataset.progressPreset;
                syncProgress();
            });
        });
    });
</script>
@endpush
