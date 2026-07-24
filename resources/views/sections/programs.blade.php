<section id="program" class="py-20 sm:py-28" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_programs_eyebrow', 'Keunggulan Kami');
            $subtitle = setting('section_programs_subtitle', 'Berbagai program yang dirancang untuk membentuk santri berprestasi dan berakhlak mulia.');
        @endphp
        <div class="text-center mb-14" data-aos="fade-up">
            @if($eyebrow)
                <div class="fi-label mb-3">{{ $eyebrow }}</div>
            @endif
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">
                {{ setting('section_programs_title') ?: 'Program Unggulan' }}
            </h2>
            @if($subtitle)
                <p class="mt-3 text-base max-w-lg mx-auto leading-relaxed" style="color:var(--muted)">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        @if(isset($programs) && $programs->isNotEmpty())
        <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($programs as $program)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">

                <a href="{{ route('programs.show', $program) }}" class="relative h-48 block overflow-hidden">
                    <img src="{{ $program->thumbnail_url }}"
                         alt="{{ $program->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.5),transparent 60%)"></div>
                    @if($url = icon_url($program->icon_image))
                    <div class="absolute bottom-4 left-4 w-8 h-8 drop-shadow">
                        <img src="{{ $url }}" alt="{{ $program->title }}" loading="lazy" class="w-full h-full object-contain">
                    </div>
                    @elseif($program->icon)
                    <div class="absolute bottom-4 left-4 text-3xl drop-shadow">{{ $program->icon }}</div>
                    @endif
                </a>

                <div class="p-6 flex flex-col flex-1">
                    @if($program->category)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mb-2 self-start"
                          style="background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                        {{ $program->category }}
                    </span>
                    @endif

                    <h3 class="font-extrabold text-lg leading-snug mb-3 flex-1" style="color:var(--text)">
                        <a href="{{ route('programs.show', $program) }}" class="hover:opacity-70 transition-opacity">
                            {{ $program->title }}
                        </a>
                    </h3>

                    @if($program->excerpt)
                    <p class="text-sm leading-relaxed line-clamp-2 mb-5" style="color:var(--muted)">{{ $program->excerpt }}</p>
                    @endif

                    <a href="{{ route('programs.show', $program) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-75 transition-opacity mt-auto"
                       style="color:var(--primary)">
                        Selengkapnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('programs.index') }}" class="btn-outline">Lihat Semua Program</a>
        </div>

        @else
        <div class="text-center py-16 fi-card">
            <div class="text-5xl mb-4">🎓</div>
            <p class="text-base font-medium" style="color:var(--muted)">Program segera tersedia.</p>
        </div>
        @endif

    </div>
</section>
