<section class="relative h-130 sm:h-145 lg:h-160 overflow-hidden -mt-17">

    {{-- Slides --}}
    @forelse($slides as $index => $s)
        <div class="slide absolute inset-0 transition-opacity duration-700"
             :class="{{ $index }} === slide ? 'opacity-100 z-10' : 'opacity-0 z-0'">

            {{-- Background image --}}
            <img src="{{ $s->image_url }}"
                 alt="{{ $s->title }}"
                 class="absolute inset-0 w-full h-full object-cover"
                 loading="{{ $index === 0 ? 'eager' : 'lazy' }}">

            {{-- Refined gradient overlay --}}
            <div class="absolute inset-0"
                 style="background:linear-gradient(135deg,rgba(0,0,0,.7) 0%,rgba(0,0,0,.4) 55%,rgba(0,0,0,.1) 100%)"></div>

            {{-- Content --}}
            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="max-w-2xl text-white">

                        {{-- Slide counter pill --}}
                        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-sm mb-6">
                            <span class="w-2 h-2 rounded-full animate-pulse" style="background:var(--primary-400)"></span>
                            <span class="text-white/80 text-xs font-semibold tracking-wider">{{ $index + 1 }} / {{ $slides->count() }}</span>
                        </div>

                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.08] tracking-tight mb-5">
                            {{ $s->title }}
                        </h1>

                        @if($s->subtitle)
                            <p class="text-white/75 text-base sm:text-lg leading-relaxed mb-8 max-w-xl font-light">
                                {{ $s->subtitle }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-3">
                            @if($s->button_label && $s->button_url)
                                <a href="{{ $s->button_url }}"
                                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl font-bold hover:opacity-90 transition-all shadow-xl text-sm"
                                   style="background:var(--primary-400);color:var(--primary-900)">
                                    {{ $s->button_label }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @endif
                            <a href="#profil"
                               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl bg-white/10 border border-white/25 backdrop-blur-sm font-semibold hover:bg-white/20 transition-all text-sm">
                                Profil Sekolah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        {{-- Fallback --}}
        <div class="absolute inset-0 flex items-center justify-center"
             style="background:linear-gradient(135deg,#1d1d1f 0%,#3a3a3c 100%)">
            <div class="text-center text-white px-6">
                <h1 class="text-5xl font-extrabold mb-4 tracking-tight">{{ setting('site_name', config('app.name')) }}</h1>
                <p class="text-white/60 text-lg font-light">{{ setting('site_tagline', 'Unggul, Berkarakter, Berprestasi') }}</p>
            </div>
        </div>
    @endforelse

    {{-- Dot indicators --}}
    @if($slides->count() > 1)
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            @foreach($slides as $index => $s)
                <button @click="slide = {{ $index }}"
                        class="transition-all duration-300 rounded-full"
                        :class="{{ $index }} === slide
                            ? 'w-7 h-2.5 opacity-100'
                            : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/70'"
                        :style="{{ $index }} === slide ? 'background:var(--primary-400)' : ''"></button>
            @endforeach
        </div>
    @endif

    {{-- Prev / Next arrows --}}
    @if($slides->count() > 1)
        <button @click="slide = (slide - 1 + total) % total"
                class="absolute left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/25 hover:bg-black/45 backdrop-blur-sm text-white flex items-center justify-center transition-all hover:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="slide = (slide + 1) % total"
                class="absolute right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/25 hover:bg-black/45 backdrop-blur-sm text-white flex items-center justify-center transition-all hover:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    @endif

    {{-- Bottom wave --}}
    <div class="absolute bottom-0 inset-x-0 z-10">
        <svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-14">
            <path d="M0 56L80 48C160 40 320 24 480 22.7C640 21.3 800 34.7 960 40C1120 45.3 1280 42.7 1360 41.3L1440 40V56H0Z" fill="var(--bg)"/>
        </svg>
    </div>
</section>
