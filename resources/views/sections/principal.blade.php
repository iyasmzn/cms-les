@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Greeting> $greetings */
    $greetings = $greetings ?? \App\Models\Greeting::published()->get();
@endphp

@if($greetings->isNotEmpty())
<section id="sambutan" class="py-20 sm:py-28" style="background:var(--bg-alt)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $eyebrow  = setting('section_principal_eyebrow', 'Sambutan');
            $subtitle = setting('section_principal_subtitle', '');
        @endphp
        <div class="text-center mb-14" data-aos="fade-up">
            @if($eyebrow)
                <div class="fi-label mb-3">{{ $eyebrow }}</div>
            @endif
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">
                {{ setting('section_principal_title') ?: 'Sambutan Para Tokoh' }}
            </h2>
            @if($subtitle)
                <p class="mt-3 text-base max-w-lg mx-auto leading-relaxed" style="color:var(--muted)">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="relative" data-aos="fade-up" data-aos-delay="80"
             x-data="{ gSlide: 0, gTotal: {{ $greetings->count() }} }"
             @if($greetings->count() > 1)
                 x-init="setInterval(() => gSlide = (gSlide + 1) % gTotal, 8000)"
             @endif>

            {{-- Slides — ditumpuk pada sel grid yang sama agar tinggi container mengikuti slide tertinggi --}}
            <div class="grid">
                @foreach($greetings as $index => $g)
                    @php
                        $detailUrl = $g->page_slug ? route('page.show', $g->page_slug) : null;
                    @endphp
                    <div style="grid-area:1/1"
                         class="transition-opacity duration-700"
                         :class="{{ $index }} === gSlide ? 'opacity-100 relative z-10' : 'opacity-0 z-0 pointer-events-none'">

                        <div class="fi-card p-8 lg:p-12">
                            <div class="grid lg:grid-cols-3 gap-10 items-start">

                                {{-- Foto / Avatar --}}
                                <div class="flex flex-col items-center text-center">
                                    @if($g->photo)
                                        <div class="relative mb-5">
                                            <img src="{{ $g->photo_url }}"
                                                 alt="Foto {{ $g->name }}"
                                                 loading="lazy"
                                                 class="w-44 h-56 rounded-3xl object-cover shadow-2xl ring-4 ring-amber-100">
                                            <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-2xl flex items-center justify-center shadow-lg"
                                                 style="background:var(--primary)">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-36 h-36 rounded-3xl flex items-center justify-center text-5xl shadow-xl mb-5"
                                             style="background:linear-gradient(135deg,var(--primary-100),var(--primary-200))">
                                            👨‍💼
                                        </div>
                                    @endif

                                    <div class="font-bold text-base mb-1" style="color:var(--text)">{{ $g->name }}</div>
                                    <div class="text-sm" style="color:var(--muted)">{{ $g->position }}</div>

                                    @if($g->nip)
                                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border"
                                             style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                                            {{ $g->nip }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Sambutan teks --}}
                                <div class="lg:col-span-2">
                                    <svg class="w-10 h-10 mb-5" fill="currentColor" viewBox="0 0 24 24"
                                         style="color:var(--primary-300)">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                    </svg>

                                    <div class="space-y-4">
                                        @foreach(array_filter(array_map('trim', explode("\n", (string) $g->excerpt))) as $paragraph)
                                            <p class="text-base leading-relaxed" style="color:var(--muted)">{{ $paragraph }}</p>
                                        @endforeach
                                    </div>

                                    <div class="mt-8 pt-6 border-t flex flex-wrap items-center gap-4"
                                         style="border-color:var(--border)">
                                        <div class="text-sm" style="color:var(--muted)">Wassalamu'alaikum Wr. Wb.</div>

                                        <div class="ml-auto text-sm font-bold" style="color:var(--primary)">
                                            {{ $g->name }}
                                        </div>

                                        @if($detailUrl)
                                            <a href="{{ $detailUrl }}" class="btn-outline text-sm w-full sm:w-auto justify-center mt-1 sm:mt-0">
                                                Baca Selengkapnya
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($greetings->count() > 1)
                {{-- Prev / Next arrows --}}
                <button @click="gSlide = (gSlide - 1 + gTotal) % gTotal"
                        aria-label="Sambutan sebelumnya"
                        class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full border shadow-lg flex items-center justify-center transition-all hover:scale-110"
                        style="background:var(--bg);border-color:var(--border);color:var(--text)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="gSlide = (gSlide + 1) % gTotal"
                        aria-label="Sambutan berikutnya"
                        class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full border shadow-lg flex items-center justify-center transition-all hover:scale-110"
                        style="background:var(--bg);border-color:var(--border);color:var(--text)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Dot indicators --}}
                <div class="mt-8 flex justify-center gap-2">
                    @foreach($greetings as $index => $g)
                        <button @click="gSlide = {{ $index }}"
                                aria-label="Sambutan {{ $g->name }}"
                                class="transition-all duration-300 rounded-full"
                                :class="{{ $index }} === gSlide ? 'w-7 h-2.5' : 'w-2.5 h-2.5 hover:opacity-70'"
                                :style="{{ $index }} === gSlide ? 'background:var(--primary)' : 'background:var(--border)'"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif
