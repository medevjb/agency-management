{{-- ============================ How it works ============================ --}}
<section id="how" class="relative py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span data-reveal class="reveal inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">How it works</span>
            <h2 data-reveal class="reveal text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">Four steps from order to revenue</h2>
        </div>

        <div class="relative grid md:grid-cols-4 gap-8">
            {{-- connecting line --}}
            <div class="hidden md:block absolute top-7 left-[12%] right-[12%] h-0.5 bg-gradient-to-r from-indigo-200 via-purple-200 to-indigo-200"></div>

            @php
                $steps = [
                    ['1','fa-plus','Add the order','Log the Fiverr or Upwork order with its value, deadline and profile.'],
                    ['2','fa-user-plus','Assign the team','Route it to the right team and members based on tech stack.'],
                    ['3','fa-truck-fast','Track delivery','Move it through pending, in-progress and delivered — dates auto-set.'],
                    ['4','fa-chart-pie','See revenue','Net revenue after fees rolls into your live analytics instantly.'],
                ];
            @endphp
            @foreach($steps as $s)
                <div data-reveal class="reveal relative text-center">
                    <div class="relative z-10 mx-auto mb-5 inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-white border-2 border-indigo-100 shadow-lg shadow-indigo-500/10">
                        <span class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 text-white text-xs font-bold inline-flex items-center justify-center">{{ $s[0] }}</span>
                        <i class="fas {{ $s[1] }} text-indigo-600 text-lg"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">{{ $s[2] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed px-2">{{ $s[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
