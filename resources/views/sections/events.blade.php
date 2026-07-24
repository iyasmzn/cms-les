<section id="kegiatan-upcoming" class="py-20 sm:py-28" style="background:var(--bg-alt)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_events_eyebrow', 'Agenda Pesantren');
            $subtitle = setting('section_events_subtitle', 'Pengajian, seminar, dan berbagai kegiatan menarik yang segera diselenggarakan.');
        @endphp
        <div class="flex items-end justify-between gap-6 mb-14" data-aos="fade-up">
            <div>
                @if($eyebrow)
                    <div class="fi-label mb-3">{{ $eyebrow }}</div>
                @endif
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">
                    {{ setting('section_events_title') ?: 'Kegiatan Akan Datang' }}
                </h2>
                @if($subtitle)
                    <p class="mt-2 text-base max-w-md leading-relaxed" style="color:var(--muted)">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <a href="{{ route('events.index') }}"
               class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-75"
               style="color:var(--primary)">
                Semua Kegiatan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if(isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($upcomingEvents as $event)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">

                <a href="{{ route('events.show', $event) }}" class="relative h-48 block overflow-hidden">
                    <img src="{{ $event->thumbnail_url }}"
                         alt="{{ $event->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    {{-- Date badge --}}
                    <div class="absolute top-4 left-4 rounded-xl overflow-hidden shadow-lg text-center"
                         style="background:#fff;min-width:3rem">
                        <div class="text-xs font-bold py-1 px-2" style="background:var(--primary);color:#fff">
                            {{ $event->starts_at->format('M') }}
                        </div>
                        <div class="text-xl font-extrabold py-1 px-2" style="color:var(--text)">
                            {{ $event->starts_at->format('d') }}
                        </div>
                    </div>
                    {{-- YouTube badge --}}
                    @if($event->youtube_url)
                    <div class="absolute top-4 right-4 flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full shadow"
                         style="background:rgba(255,0,0,.85);color:#fff">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        Video
                    </div>
                    @endif
                </a>

                <div class="p-6 flex flex-col flex-1">
                    @if($event->category)
                    <span class="text-xs font-bold mb-2 block" style="color:var(--primary)">{{ $event->category }}</span>
                    @endif

                    <h3 class="font-extrabold text-base leading-snug mb-2 line-clamp-2 flex-1" style="color:var(--text)">
                        <a href="{{ route('events.show', $event) }}" class="hover:opacity-70 transition-opacity">
                            {{ $event->title }}
                        </a>
                    </h3>

                    <div class="flex items-center gap-3 text-xs mt-2" style="color:var(--muted)">
                        <span>{{ $event->formatted_date }}</span>
                        @if($event->location)
                        <span>· {{ Str::limit($event->location, 25) }}</span>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 fi-card">
            <div class="text-5xl mb-4">🗓</div>
            <p class="text-base font-medium" style="color:var(--muted)">Belum ada kegiatan yang akan datang.</p>
            <a href="{{ route('events.index', ['filter' => 'past']) }}" class="btn-outline mt-4 inline-flex">Lihat Kegiatan Sebelumnya</a>
        </div>
        @endif

    </div>
</section>
