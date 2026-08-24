{{-- ============================ Pricing ============================ --}}
<section id="pricing" class="relative py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span data-reveal class="reveal eyebrow inline-flex mb-3">Simple pricing</span>
            <h2 data-reveal class="reveal font-display text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">Start free, scale as your agency grows</h2>
            <p data-reveal class="reveal mt-4 text-lg text-gray-600">No credit card required to get started.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto items-stretch">
            @php
                $tiers = [
                    ['Starter','0','/mo','For a solo operator getting organized.',['Up to 25 active orders','1 team','Core analytics','Email support'],false,'Get started'],
                    ['Agency','—','','For growing teams running both marketplaces.',['Unlimited orders','Unlimited teams &amp; roles','Revenue analytics &amp; exports','Priority support'],true,'Start free trial'],
                    ['Scale','—','','For large agencies that need control.',['Everything in Agency','Advanced permissions','Custom statuses &amp; branding','Dedicated support'],false,'Contact sales'],
                ];
            @endphp
            @foreach($tiers as $t)
                <div data-reveal class="reveal relative rounded-2xl p-7 flex flex-col {{ $t[5] ? 'bg-gradient-to-b from-ember-600 to-gold-600 text-white shadow-2xl shadow-ember-500/30 md:-translate-y-3' : 'bg-white border border-gray-200' }}">
                    @if($t[5])
                        <span class="absolute top-5 right-5 font-mono text-[10px] font-bold uppercase tracking-widest text-white/90 bg-white/15 px-2.5 py-1 rounded-full">Popular</span>
                    @endif
                    <h3 class="font-display text-lg font-bold {{ $t[5] ? 'text-white' : 'text-gray-900' }}">{{ $t[0] }}</h3>
                    <div class="mt-3 mb-1 flex items-end gap-1">
                        <span class="font-mono text-4xl font-bold {{ $t[5] ? 'text-white' : 'text-gray-900' }}">{{ $t[1] === '0' ? 'Free' : $t[1] }}</span>
                        <span class="text-sm {{ $t[5] ? 'text-white/80' : 'text-gray-400' }} mb-1.5">{{ $t[2] }}</span>
                    </div>
                    <p class="text-sm {{ $t[5] ? 'text-white/80' : 'text-gray-500' }} mb-6">{{ $t[3] }}</p>
                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach($t[4] as $feat)
                            <li class="flex items-start gap-2.5 text-sm {{ $t[5] ? 'text-white/90' : 'text-gray-700' }}">
                                <i class="fas fa-check {{ $t[5] ? 'text-white' : 'text-aqua-500' }} mt-0.5"></i> {!! $feat !!}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}"
                       class="block text-center px-5 py-3 rounded-xl font-semibold transition-all {{ $t[5] ? 'bg-white text-ember-700 hover:bg-paper' : 'bg-ink text-white hover:bg-ink-soft' }}">
                        {{ $t[6] }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
