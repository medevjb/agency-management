{{-- ============================ Footer ============================ --}}
<footer class="bg-ink text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <x-site-logo
                        img-class="h-8 w-auto max-w-[140px] object-contain bg-white/5 rounded-lg p-1"
                        badge-class="h-8 w-8 text-sm" />
                    <span class="font-display font-semibold text-white text-lg">{{ $siteName }}</span>
                </div>
                <p class="text-sm leading-relaxed text-gray-500">
                    {{ $siteDescription }}
                </p>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Product</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="#how" class="hover:text-white transition-colors">How it works</a></li>
                    <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Account</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign in</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Create account</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Contact</h4>
                <ul class="space-y-2.5 text-sm">
                    @if($companyEmail)
                        <li><a href="mailto:{{ $companyEmail }}" class="hover:text-white transition-colors"><i class="fas fa-envelope mr-2 text-gray-500"></i>{{ $companyEmail }}</a></li>
                    @endif
                    @if($companyPhone)
                        <li><span><i class="fas fa-phone mr-2 text-gray-500"></i>{{ $companyPhone }}</span></li>
                    @endif
                    <li class="flex items-center gap-3 pt-2">
                        <a href="#" class="h-8 w-8 rounded-lg bg-white/5 hover:bg-white/10 inline-flex items-center justify-center transition-colors"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="h-8 w-8 rounded-lg bg-white/5 hover:bg-white/10 inline-flex items-center justify-center transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="h-8 w-8 rounded-lg bg-white/5 hover:bg-white/10 inline-flex items-center justify-center transition-colors"><i class="fab fa-facebook-f"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
            <p class="text-xs text-gray-600">Built for Fiverr &amp; Upwork agencies.</p>
        </div>
    </div>
</footer>
