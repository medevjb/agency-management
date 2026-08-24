{{-- ============================ Features ============================ --}}
<section id="features" class="relative py-24 bg-gradient-to-b from-white to-paper-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="max-w-2xl mx-auto text-center mb-20">
            <span data-reveal class="reveal eyebrow inline-flex mb-3">Everything in one place</span>
            <h2 data-reveal class="reveal font-display text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                The full agency workflow, end to end
            </h2>
            <p data-reveal class="reveal mt-4 text-lg text-gray-600">
                From the moment an order lands to the day you count delivered revenue — nothing slips through the cracks.
            </p>
        </div>

        {{-- Feature 1: Order pipeline + carry-forward --}}
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-28">
            <div data-reveal class="reveal">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-ember-100 text-ember-600 mb-5">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <h3 class="font-display text-2xl font-bold text-gray-900 mb-3">Month-to-month order pipeline</h3>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Undelivered orders automatically carry forward to the next month, so your running workload is always accurate. Switch months to see exactly what was live, delivered, or overdue at any point.
                </p>
                <ul class="space-y-2.5">
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Automatic carry-forward of active orders</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Overdue tracking on running work only</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Per-month snapshots &amp; exports</li>
                </ul>
            </div>
            <div data-reveal class="reveal mockup">
                <div class="mockup-bar">
                    <span class="mockup-dot bg-red-400"></span><span class="mockup-dot bg-yellow-400"></span><span class="mockup-dot bg-green-400"></span>
                    <span class="ml-3 text-[11px] text-gray-400 font-mono">Orders · June 2026</span>
                </div>
                <div class="p-5 space-y-2.5 mini-scroll">
                    @php
                        $rows = [
                            ['#ORD-2041','Logo animation','In progress','bg-ember-100 text-ember-700','৳1,200'],
                            ['#ORD-2039','Shopify theme','Pending','bg-gold-100 text-gold-700','৳3,500'],
                            ['#ORD-2034','Brand kit','Delivered','bg-aqua-100 text-aqua-700','৳900'],
                            ['#ORD-2028','Landing page','In progress','bg-ember-100 text-ember-700','৳2,100'],
                            ['#ORD-2025','SEO audit','On hold','bg-orange-100 text-orange-700','৳750'],
                        ];
                    @endphp
                    @foreach($rows as $r)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-3.5 py-2.5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="h-8 w-8 shrink-0 rounded-lg bg-ember-50 text-ember-500 inline-flex items-center justify-center text-xs"><i class="fas fa-folder"></i></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $r[1] }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $r[0] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $r[3] }}">{{ $r[2] }}</span>
                                <span class="text-xs font-bold text-gray-900 w-14 text-right font-mono">{{ $r[4] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Feature 2: Revenue analytics (reversed) --}}
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-28">
            <div data-reveal class="reveal order-2 lg:order-1 mockup">
                <div class="mockup-bar">
                    <span class="mockup-dot bg-red-400"></span><span class="mockup-dot bg-yellow-400"></span><span class="mockup-dot bg-green-400"></span>
                    <span class="ml-3 text-[11px] text-gray-400 font-mono">Revenue analytics</span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="rounded-xl bg-gradient-to-br from-ember-600 to-ember-700 p-3 text-white">
                            <p class="text-[10px] text-ember-100 uppercase tracking-wide font-semibold">This month</p>
                            <p class="text-xl font-bold font-mono">৳84,200</p>
                            <p class="text-[10px] text-gold-200"><i class="fas fa-arrow-up"></i> 12% vs last</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">This year</p>
                            <p class="text-xl font-bold text-gray-900 font-mono">৳612k</p>
                            <p class="text-[10px] text-gray-400">YTD delivered</p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <svg viewBox="0 0 300 90" class="w-full h-24">
                            <polyline fill="none" stroke="#FF5A1F" stroke-width="3" points="0,70 40,55 80,62 120,38 160,45 200,22 240,30 300,10"/>
                            <polygon fill="url(#g)" opacity="0.18" points="0,70 40,55 80,62 120,38 160,45 200,22 240,30 300,10 300,90 0,90"/>
                            <defs><linearGradient id="g" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#FF5A1F"/><stop offset="1" stop-color="#fff"/></linearGradient></defs>
                        </svg>
                    </div>
                </div>
            </div>
            <div data-reveal class="reveal order-1 lg:order-2">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-gold-100 text-gold-700 mb-5">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <h3 class="font-display text-2xl font-bold text-gray-900 mb-3">Real revenue, after platform fees</h3>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Every order's net value is calculated automatically — Fiverr's 20% and Upwork's 10% cut handled for you. Revenue is measured by delivery date, so your numbers reflect money actually earned.
                </p>
                <ul class="space-y-2.5">
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Platform-aware fee splits per profile</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Delivered-revenue analytics &amp; growth %</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Revenue by team and by month</li>
                </ul>
            </div>
        </div>

        {{-- Feature 3: Teams & roles --}}
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-28">
            <div data-reveal class="reveal">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-aqua-100 text-aqua-600 mb-5">
                    <i class="fas fa-users-gear text-lg"></i>
                </div>
                <h3 class="font-display text-2xl font-bold text-gray-900 mb-3">Teams &amp; role-based access</h3>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Admins and managers see everything; team and stack leaders see only their own team's orders. Assign members, set tech stacks, and keep every project owned by the right people.
                </p>
                <ul class="space-y-2.5">
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Granular roles &amp; permissions</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Team-scoped dashboards</li>
                    <li class="flex items-start gap-3 text-gray-700"><i class="fas fa-check text-aqua-500 mt-1"></i> Member &amp; tech-stack management</li>
                </ul>
            </div>
            <div data-reveal class="reveal mockup">
                <div class="mockup-bar">
                    <span class="mockup-dot bg-red-400"></span><span class="mockup-dot bg-yellow-400"></span><span class="mockup-dot bg-green-400"></span>
                    <span class="ml-3 text-[11px] text-gray-400 font-mono">Team · Design Squad</span>
                </div>
                <div class="p-5">
                    <div class="space-y-2.5">
                        @php
                            $members = [
                                ['Ayesha K.','Team leader','bg-ember-100 text-ember-700'],
                                ['Rahim H.','Designer','bg-gray-100 text-gray-600'],
                                ['Sofia M.','Developer','bg-gray-100 text-gray-600'],
                                ['Tanvir A.','Stack leader','bg-aqua-100 text-aqua-700'],
                            ];
                        @endphp
                        @foreach($members as $m)
                            <div class="flex items-center justify-between rounded-xl border border-gray-100 px-3.5 py-2.5">
                                <div class="flex items-center gap-3">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($m[0]) }}&background=FF5A1F&color=fff&size=64" alt="{{ $m[0] }}">
                                    <span class="text-xs font-semibold text-gray-900">{{ $m[0] }}</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $m[2] }}">{{ $m[1] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Feature mini-grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $minis = [
                    ['fa-list-check','bg-ember-100 text-ember-600','Tasks &amp; workflow','Break orders into tasks, assign owners, track status, and archive when done.'],
                    ['fa-id-badge','bg-gold-100 text-gold-700','Freelance profiles','Manage multiple Fiverr &amp; Upwork profiles, each with its own platform &amp; fee.'],
                    ['fa-bug','bg-rose-100 text-rose-600','Bug reports','Capture issues against orders so nothing gets lost in delivery.'],
                    ['fa-sliders','bg-aqua-100 text-aqua-600','System settings','Currency, company info, statuses &amp; branding — all configurable.'],
                    ['fa-file-export','bg-sky-100 text-sky-600','Exports','Pull order &amp; revenue data out to share with clients or finance.'],
                    ['fa-gauge-high','bg-amber-100 text-amber-600','Live dashboards','Admin, manager &amp; team views update as work moves.'],
                ];
            @endphp
            @foreach($minis as $f)
                <div data-reveal class="reveal group rounded-2xl bg-white border border-gray-100 p-6 hover:shadow-xl hover:shadow-ember-500/10 hover:-translate-y-1 transition-all">
                    <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl {{ $f[1] }} mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $f[0] }}"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-1.5">{!! $f[2] !!}</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">{!! $f[3] !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
