{{-- ============================ Sticky Nav ============================ --}}
<nav id="siteNav" x-data="{ open: false }"
     class="fixed top-0 inset-x-0 z-50 border-b border-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">

            {{-- Logo --}}
            <a href="#top" class="flex items-center gap-2.5">
                <x-site-logo
                    img-class="h-9 w-auto max-w-[150px] object-contain"
                    badge-class="h-9 w-9 text-base" />
                <span class="font-display font-semibold text-lg tracking-tight text-gray-900">{{ $siteName }}</span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-gray-600 hover:text-ember-600 transition-colors">Features</a>
                <a href="#how" class="text-sm font-medium text-gray-600 hover:text-ember-600 transition-colors">How it works</a>
                <a href="#pricing" class="text-sm font-medium text-gray-600 hover:text-ember-600 transition-colors">Pricing</a>
                <a href="#faq" class="text-sm font-medium text-gray-600 hover:text-ember-600 transition-colors">FAQ</a>
            </div>

            {{-- Desktop CTAs --}}
            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-ember-600 transition-colors">Sign in</a>
                <a data-magnetic href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-ember-600 to-gold-500 px-5 py-2.5 rounded-xl shadow-lg shadow-ember-500/30 hover:shadow-ember-500/50 transition-shadow">
                    Get started <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            {{-- Mobile toggle --}}
            <button @click="open = !open" class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" aria-label="Toggle menu">
                <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition x-cloak class="lg:hidden glass border-t border-gray-100">
        <div class="px-4 py-4 space-y-1">
            <a @click="open=false" href="#features" class="block px-3 py-2.5 rounded-lg text-gray-700 hover:bg-ember-50 hover:text-ember-600 font-medium">Features</a>
            <a @click="open=false" href="#how" class="block px-3 py-2.5 rounded-lg text-gray-700 hover:bg-ember-50 hover:text-ember-600 font-medium">How it works</a>
            <a @click="open=false" href="#pricing" class="block px-3 py-2.5 rounded-lg text-gray-700 hover:bg-ember-50 hover:text-ember-600 font-medium">Pricing</a>
            <a @click="open=false" href="#faq" class="block px-3 py-2.5 rounded-lg text-gray-700 hover:bg-ember-50 hover:text-ember-600 font-medium">FAQ</a>
            <div class="pt-3 mt-2 border-t border-gray-100 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="block text-center px-3 py-2.5 rounded-lg font-semibold text-gray-700 border border-gray-200">Sign in</a>
                <a href="{{ route('register') }}" class="block text-center px-3 py-2.5 rounded-lg font-semibold text-white bg-gradient-to-r from-ember-600 to-gold-500">Get started</a>
            </div>
        </div>
    </div>
</nav>
<style>[x-cloak]{display:none!important;}</style>
