@extends('layouts.public')

@push('head')
<style>
    .events-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 60%, #0a5a5c 100%);
        position: relative;
        overflow: hidden;
    }
    .events-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 60% at 80% 20%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 10% 80%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
    .filter-pill {
        font-size: .8125rem;
        font-weight: 600;
        padding: .5rem 1.25rem;
        border-radius: 9999px;
        border: 1.5px solid var(--border);
        color: var(--muted);
        background: var(--card);
        transition: all .2s;
    }
    .filter-pill:hover, .filter-pill.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="events-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">
    <x-hero-geo />
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            🗓 Kegiatan & Event
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Kegiatan Pesantren
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Pengajian, seminar, wisuda, dan berbagai kegiatan inspiratif dari {{ setting('site_name', config('app.name')) }}.
        </p>
    </div>
</section>

{{-- ── Events Grid ─────────────────────────────────────── --}}
<section class="py-16 sm:py-20" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filter --}}
        <div class="flex flex-wrap gap-2 mb-10" data-aos="fade-up">
            @foreach(['upcoming' => 'Akan Datang', 'past' => 'Sudah Berlalu', 'all' => 'Semua'] as $key => $label)
            <a href="{{ route('events.index', ['filter' => $key]) }}"
               class="filter-pill {{ $filter === $key ? 'active' : '' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        @if($events->isNotEmpty())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">

                {{-- Image --}}
                <a href="{{ route('events.show', $event) }}" class="relative h-48 block overflow-hidden">
                    <img src="{{ $event->thumbnail_url }}"
                         alt="{{ $event->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    {{-- Date badge --}}
                    <div class="absolute top-4 left-4 text-center rounded-xl overflow-hidden shadow-lg"
                         style="background:#fff;min-width:3rem">
                        <div class="text-xs font-bold py-1 px-2"
                             style="background:var(--primary);color:#fff">
                            {{ $event->starts_at->format('M') }}
                        </div>
                        <div class="text-xl font-extrabold py-1 px-2" style="color:var(--text)">
                            {{ $event->starts_at->format('d') }}
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                        @if($event->is_past)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full backdrop-blur-sm"
                              style="background:rgba(0,0,0,.5);color:rgba(255,255,255,.8)">Selesai</span>
                        @endif
                        @if($event->youtube_url)
                        <span class="flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full shadow"
                              style="background:rgba(255,0,0,.85);color:#fff">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Video
                        </span>
                        @endif
                    </div>
                </a>

                {{-- Content --}}
                <div class="p-6 flex flex-col flex-1">
                    @if($event->category)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full mb-3 self-start border"
                          style="background:rgba(8,72,74,.08);color:var(--primary);border-color:rgba(8,72,74,.2)">
                        {{ $event->category }}
                    </span>
                    @endif

                    <h3 class="font-extrabold text-base leading-snug mb-2 line-clamp-2 flex-1" style="color:var(--text)">
                        <a href="{{ route('events.show', $event) }}" class="hover:opacity-70 transition-opacity">
                            {{ $event->title }}
                        </a>
                    </h3>

                    @if($event->excerpt)
                    <p class="text-sm leading-relaxed line-clamp-2 mb-4" style="color:var(--muted)">{{ $event->excerpt }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-xs" style="color:var(--muted)">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->formatted_date }}
                        </span>
                        @if($event->location)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ Str::limit($event->location, 25) }}
                        </span>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($events->hasPages())
        <div class="mt-12">
            {{ $events->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-24 fi-card">
            <div class="text-6xl mb-4">🗓</div>
            <p class="text-base font-medium" style="color:var(--muted)">
                @if($filter === 'upcoming') Belum ada kegiatan yang akan datang.
                @elseif($filter === 'past') Belum ada kegiatan yang telah berlalu.
                @else Belum ada kegiatan yang dipublikasikan.
                @endif
            </p>
        </div>
        @endif

    </div>
</section>

@endsection
