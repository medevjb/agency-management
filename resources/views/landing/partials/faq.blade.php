{{-- ============================ FAQ ============================ --}}
<section id="faq" class="relative py-24 bg-indigo-50/40">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span data-reveal class="reveal inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">FAQ</span>
            <h2 data-reveal class="reveal text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">Questions, answered</h2>
        </div>

        <div class="space-y-4" x-data="{ open: 0 }">
            @php
                $faqs = [
                    ['How does the month-to-month carry-forward work?','Any order that isn\'t delivered or cancelled automatically appears in the next month\'s pipeline, so your running workload and overdue counts always reflect reality — no manual rollover needed.'],
                    ['How are Fiverr and Upwork fees handled?','Each freelance profile is tied to a platform. When you enter an order amount, the correct fee (20% Fiverr, 10% Upwork) is deducted automatically and the net value feeds your revenue analytics.'],
                    ['Can team leaders see other teams\' orders?','No. Team and stack leaders are scoped to their own team. Admins, operation managers and project managers get the full cross-team view.'],
                    ['Is revenue based on order date or delivery date?','Delivered date. Revenue counts when work is actually delivered, so your monthly and yearly numbers reflect money earned, not just orders created.'],
                    ['Can I customise currency, statuses and branding?','Yes — currency, company details, order statuses and your logo are all configurable from System Settings, and they flow through the whole app.'],
                ];
            @endphp
            @foreach($faqs as $i => $f)
                <div data-reveal class="reveal rounded-2xl bg-white border border-gray-100 overflow-hidden">
                    <button @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                            class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">{{ $f[0] }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform shrink-0" :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse x-cloak>
                        <p class="px-6 pb-5 text-gray-600 leading-relaxed">{{ $f[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
