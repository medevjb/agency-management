{{-- ============================ Hero ============================ --}}
<section id="top" class="relative pt-28 lg:pt-44 pb-20 lg:pb-32 overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 gradient-mesh opacity-60"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-paper/40 via-paper/70 to-paper"></div>
    <div class="glow-blob bg-ember-300 w-[28rem] h-[28rem] -top-32 -left-24" data-glow></div>
    <div class="glow-blob bg-gold-300 w-[26rem] h-[26rem] top-20 right-0" data-glow></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">

            {{-- Left: copy --}}
            <div class="max-w-xl">
                <div data-hero class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-ember-100 shadow-sm eyebrow">
                    Live · Fiverr &amp; Upwork on one board
                </div>

                <h1 data-hero class="reveal mt-6 font-display text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-[1.05]">
                    Run your agency like
                    <span class="text-gradient">a dispatch tower</span>
                </h1>

                <p data-hero class="reveal mt-6 text-lg text-gray-600 leading-relaxed">
                    Track every Fiverr &amp; Upwork order month to month, assign the right team, watch deadlines, and see real delivered revenue — with platform fees handled automatically.
                </p>

                <div data-hero class="reveal mt-8 flex flex-col sm:flex-row gap-3">
                    <a data-magnetic href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-white font-semibold bg-gradient-to-r from-ember-600 to-gold-500 shadow-xl shadow-ember-500/30 hover:shadow-ember-500/50 transition-shadow">
                        Start free <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="#how"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-gray-700 bg-white border border-gray-200 hover:border-ember-300 hover:text-ember-600 shadow-sm transition-all">
                        <i class="fas fa-play text-xs"></i> See how it works
                    </a>
                </div>

                <div data-hero class="reveal mt-8 flex items-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-aqua-500"></i> No credit card</div>
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-aqua-500"></i> Role-based access</div>
                </div>
            </div>

            {{-- Right: dispatch board (signature element) + net revenue chip --}}
            <div class="relative h-[420px] sm:h-[480px] lg:h-[520px]" data-parallax-scene>

                {{-- Live dispatch board --}}
                <div data-float="0.18" class="dispatch-board float-soft absolute left-0 top-6 w-full sm:w-[92%]">
                    <div class="board-header">
                        <span class="board-live-dot"></span> Live dispatch
                        <span class="board-clock" data-clock>00:00:00</span>
                    </div>
                    <div class="board-rows">
                        @php
                            $dispatchRows = [
                                ['ORD-2041', 'Logo animation', 'fvr', 'FVR'],
                                ['ORD-2039', 'Shopify theme', 'upw', 'UPW'],
                                ['ORD-2034', 'Brand kit', 'fvr', 'FVR'],
                                ['ORD-2028', 'Landing page', 'upw', 'UPW'],
                            ];
                        @endphp
                        @foreach($dispatchRows as $row)
                            <div class="board-row">
                                <span class="row-code">{{ $row[0] }}</span>
                                <span class="row-title">{{ $row[1] }}</span>
                                <span class="row-platform {{ $row[2] }}">{{ $row[3] }}</span>
                                <span class="flap" data-flap data-states="PEND,TRAN,DLVR">
                                    <span class="flap-face" data-state="PEND">PEND</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Net revenue chip --}}
                <div data-float="0.4" class="mockup chip-card float-soft-delayed absolute right-0 bottom-0 w-[62%] sm:w-[52%]">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-700">Net revenue, this order</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-aqua-100 text-aqua-700 font-semibold">Upwork</span>
                        </div>
                        <div class="space-y-2 text-xs font-mono">
                            <div class="flex justify-between"><span class="text-gray-500">Order amount</span><span class="font-semibold text-gray-900">৳1,000</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Platform fee (10%)</span><span class="font-semibold text-ember-600">-৳100</span></div>
                            <div class="flex justify-between pt-2 border-t border-gray-100"><span class="font-semibold text-gray-900">Net value</span><span class="font-bold text-aqua-600">৳900</span></div>
                        </div>
                        <div class="mt-3 flex h-2 rounded-full overflow-hidden bg-gray-100">
                            <div class="bg-aqua-500" style="width:90%"></div>
                            <div class="bg-ember-400" style="width:10%"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
