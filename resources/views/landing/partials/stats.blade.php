{{-- ============================ Stats band ============================ --}}
<section class="relative py-20 overflow-hidden bg-ink">
    <div class="absolute inset-0 gradient-mesh opacity-40"></div>
    <div class="glow-blob bg-ember-500 w-96 h-96 -top-32 -left-20" data-glow></div>
    <div class="glow-blob bg-gold-400 w-96 h-96 -bottom-32 -right-20" data-glow></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white">
            @php
                $metrics = [
                    ['12000','+','Orders tracked'],
                    ['2','','Marketplaces supported'],
                    ['98','%','On-time visibility'],
                    ['30','%','Less admin overhead'],
                ];
            @endphp
            @foreach($metrics as $m)
                <div data-reveal class="reveal">
                    <div class="font-display text-4xl sm:text-5xl font-bold tracking-tight">
                        <span class="count-up font-mono" data-target="{{ $m[0] }}">0</span>{{ $m[1] }}
                    </div>
                    <p class="mt-2 text-sm text-white/50 font-medium">{{ $m[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
