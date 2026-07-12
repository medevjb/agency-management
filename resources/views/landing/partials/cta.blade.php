{{-- ============================ Final CTA ============================ --}}
<section class="relative py-24 overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div data-reveal class="reveal relative rounded-3xl px-8 py-16 sm:px-16 text-center overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700"></div>
            <div class="absolute inset-0 gradient-mesh opacity-30"></div>
            <div class="glow-blob bg-purple-400 w-80 h-80 -top-24 -left-10" data-glow></div>
            <div class="glow-blob bg-indigo-300 w-80 h-80 -bottom-24 -right-10" data-glow></div>

            <div class="relative">
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">
                    Start managing your agency today
                </h2>
                <p class="mt-4 text-lg text-indigo-100 max-w-xl mx-auto">
                    Bring every Fiverr &amp; Upwork order, team and revenue number into one place — free to start.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-indigo-600 bg-white hover:bg-indigo-50 shadow-xl hover:-translate-y-0.5 transition-all">
                        Get started free <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white border border-white/40 hover:bg-white/10 transition-all">
                        Sign in
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
