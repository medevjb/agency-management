{{-- ============================ Hero ============================ --}}
<section id="top" class="relative pt-28 lg:pt-44 pb-20 lg:pb-32 overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 gradient-mesh opacity-60"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-white/40 via-white/70 to-white"></div>
    <div class="glow-blob bg-indigo-400 w-[28rem] h-[28rem] -top-32 -left-24" data-glow></div>
    <div class="glow-blob bg-purple-400 w-[26rem] h-[26rem] top-20 right-0" data-glow></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">

            {{-- Left: copy --}}
            <div class="max-w-xl">
                <div data-hero class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-indigo-100 shadow-sm text-xs font-semibold text-indigo-700 mb-6">
                    <span class="inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                    Built for Fiverr &amp; Upwork agencies
                </div>

                <h1 data-hero class="reveal text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-[1.05]">
                    Run your agency from one
                    <span class="text-gradient">command center</span>
                </h1>

                <p data-hero class="reveal mt-6 text-lg text-gray-600 leading-relaxed">
                    Track every Fiverr &amp; Upwork order month to month, assign the right team, watch deadlines, and see real delivered revenue — with platform fees handled automatically.
                </p>

                <div data-hero class="reveal mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-white font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all">
                        Start free <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="#how"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-gray-700 bg-white border border-gray-200 hover:border-indigo-300 hover:text-indigo-600 shadow-sm transition-all">
                        <i class="fas fa-play text-xs"></i> See how it works
                    </a>
                </div>

                <div data-hero class="reveal mt-8 flex items-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> No credit card</div>
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Role-based access</div>
                </div>
            </div>

            {{-- Right: floating product mockups --}}
            <div class="relative h-[420px] sm:h-[480px] lg:h-[560px]" data-parallax-scene>

                {{-- Main dashboard mockup --}}
                <div data-float="0.18" class="mockup float-soft absolute left-0 top-6 w-[88%] sm:w-[78%]">
                    <div class="mockup-bar">
                        <span class="mockup-dot bg-red-400"></span>
                        <span class="mockup-dot bg-yellow-400"></span>
                        <span class="mockup-dot bg-green-400"></span>
                        <span class="ml-3 text-[11px] text-gray-400 font-medium">Orders dashboard</span>
                    </div>
                    <div class="p-4">
                        {{-- stat row --}}
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="rounded-xl bg-indigo-50 p-3">
                                <p class="text-[10px] text-indigo-500 font-semibold uppercase tracking-wide">Revenue</p>
                                <p class="text-lg font-bold text-gray-900">৳84,200</p>
                                <p class="text-[10px] text-green-600 font-medium"><i class="fas fa-arrow-up"></i> 12%</p>
                            </div>
                            <div class="rounded-xl bg-purple-50 p-3">
                                <p class="text-[10px] text-purple-500 font-semibold uppercase tracking-wide">Running</p>
                                <p class="text-lg font-bold text-gray-900">37</p>
                                <p class="text-[10px] text-gray-400 font-medium">orders</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-3">
                                <p class="text-[10px] text-emerald-500 font-semibold uppercase tracking-wide">Delivered</p>
                                <p class="text-lg font-bold text-gray-900">128</p>
                                <p class="text-[10px] text-gray-400 font-medium">this month</p>
                            </div>
                        </div>
                        {{-- chart --}}
                        <div class="rounded-xl border border-gray-100 p-3">
                            <div class="flex items-end gap-1.5 h-24">
                                @php $bars = [40,62,48,75,58,88,70,95,80,68,90,76]; @endphp
                                @foreach($bars as $b)
                                    <div class="flex-1 rounded-t bg-gradient-to-t from-indigo-500 to-purple-400" style="height: {{ $b }}%"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenue split card --}}
                <div data-float="0.34" class="mockup float-soft-delayed absolute right-0 top-0 w-[58%] sm:w-[48%]">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-700">Order value split</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Upwork</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between"><span class="text-gray-500">Order amount</span><span class="font-semibold text-gray-900">৳1,000</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Platform fee (10%)</span><span class="font-semibold text-red-500">-৳100</span></div>
                            <div class="flex justify-between pt-2 border-t border-gray-100"><span class="font-semibold text-gray-900">Net value</span><span class="font-bold text-green-600">৳900</span></div>
                        </div>
                        <div class="mt-3 flex h-2 rounded-full overflow-hidden bg-gray-100">
                            <div class="bg-green-500" style="width:90%"></div>
                            <div class="bg-red-400" style="width:10%"></div>
                        </div>
                    </div>
                </div>

                {{-- Status pills card --}}
                <div data-float="0.52" class="mockup absolute right-6 bottom-2 w-[52%] sm:w-[44%]">
                    <div class="p-4">
                        <p class="text-xs font-semibold text-gray-700 mb-3">Pipeline status</p>
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-blue-700"><span class="h-2 w-2 rounded-full bg-blue-500"></span> In progress</span>
                                <span class="text-[11px] font-bold text-gray-900">18</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-yellow-700"><span class="h-2 w-2 rounded-full bg-yellow-400"></span> Pending</span>
                                <span class="text-[11px] font-bold text-gray-900">9</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-green-700"><span class="h-2 w-2 rounded-full bg-green-500"></span> Delivered</span>
                                <span class="text-[11px] font-bold text-gray-900">128</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
