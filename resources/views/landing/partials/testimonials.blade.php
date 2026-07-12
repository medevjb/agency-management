{{-- ============================ Testimonials ============================ --}}
<section class="relative py-24 bg-indigo-50/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span data-reveal class="reveal inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">Loved by agency teams</span>
            <h2 data-reveal class="reveal text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">Built around how agencies really work</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @php
                $quotes = [
                    ['We finally see real delivered revenue after fees — not just gross order value. The month carry-forward alone saved our reporting.','Maya R.','Operations Manager'],
                    ['Team leaders only see their own orders, so the dashboard is clean for everyone. Onboarding new members takes minutes.','Daniel O.','Agency Founder'],
                    ['Fiverr and Upwork in one pipeline with the right fee on each order. Our numbers finally match our payouts.','Priya S.','Finance Lead'],
                ];
            @endphp
            @foreach($quotes as $q)
                <figure data-reveal class="reveal rounded-2xl bg-white border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all">
                    <div class="flex gap-1 text-amber-400 mb-4">
                        @for($i=0;$i<5;$i++)<i class="fas fa-star text-sm"></i>@endfor
                    </div>
                    <blockquote class="text-gray-700 leading-relaxed mb-6">“{{ $q[0] }}”</blockquote>
                    <figcaption class="flex items-center gap-3">
                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($q[1]) }}&background=4f46e5&color=fff&size=80" alt="{{ $q[1] }}">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $q[1] }}</p>
                            <p class="text-xs text-gray-500">{{ $q[2] }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
