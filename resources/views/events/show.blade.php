@extends('layouts.public')

@push('head')
<style>
    .sidebar-sticky { position: sticky; top: 5.5rem; }
    .toc-item {
        display: flex; align-items: flex-start; gap: .625rem;
        padding: .35rem .5rem; border-radius: .375rem;
        font-size: .75rem; color: #6b7280; line-height: 1.5;
        transition: background .15s, color .15s; cursor: pointer;
    }
    .toc-item:hover { background: var(--primary-50); color: var(--primary-700); }
    .toc-num { flex-shrink: 0; font-weight: 700; color: var(--primary-400); font-size: .6875rem; min-width: 1.25rem; }
    .event-meta-row {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .875rem 0; border-bottom: 1px solid var(--border);
    }
    .event-meta-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .event-meta-row:first-child { padding-top: 0; }
    .event-meta-icon {
        width: 2.25rem; height: 2.25rem; border-radius: .625rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        background: var(--primary-50);
    }
</style>
@include('partials.prose-styles')
@include('partials.content-block-styles')
@endpush

@section('content')

{{-- ── Breadcrumb ──────────────────────────────────────────── --}}
<div class="-mt-17" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0c1a14 100%);border-bottom:1px solid rgba(255,255,255,.08)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-5 sm:pt-28 sm:pb-6">
        <nav class="flex items-center gap-2 text-sm" style="color:rgba(255,255,255,.6)">
            <a href="{{ route('home') }}" class="hover:opacity-75 transition-opacity">Beranda</a>
            <span>/</span>
            <a href="{{ route('events.index') }}" class="hover:opacity-75 transition-opacity">Kegiatan</a>
            <span>/</span>
            <span class="font-medium line-clamp-1" style="color:#fff">{{ $event->title }}</span>
        </nav>
    </div>
</div>

{{-- ── Hero Image ───────────────────────────────────────────── --}}
<div class="relative h-64 sm:h-80 lg:h-96 overflow-hidden">
    <img src="{{ $event->thumbnail_url }}" alt="{{ $event->title }}"
         class="w-full h-full object-cover">
    <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.75),rgba(0,0,0,.2))"></div>
    <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
        <div class="max-w-7xl mx-auto">
            @if($event->category)
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3"
                  style="background:rgba(8,72,74,.85);color:#fff">{{ $event->category }}</span>
            @endif
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight max-w-3xl">
                {{ $event->title }}
            </h1>
        </div>
    </div>
</div>

{{-- Accent bar --}}
<div style="height:4px;background:linear-gradient(90deg,var(--primary),var(--primary-300) 50%,var(--primary-100) 100%)"></div>

{{-- ── Content ──────────────────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid lg:grid-cols-4 gap-10">

        {{-- ── Main content ────────────────────────────────── --}}
        <article class="lg:col-span-3" data-aos="fade-up" data-aos-duration="500">

            {{-- YouTube Video --}}
            @if($event->youtube_embed_url)
            <div class="mb-8">
                <div class="rounded-2xl overflow-hidden shadow-lg" style="aspect-ratio:16/9">
                    <iframe
                        src="{{ $event->youtube_embed_url }}"
                        title="{{ $event->title }}"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            @endif

            <div class="fi-card overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,var(--primary),var(--primary-300) 60%,transparent)"></div>
                <div class="p-6 sm:p-10">
                    @if($event->content)
                        <div class="article-prose">
                            {!! $event->content !!}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl" style="background:var(--primary-50);border:1px solid var(--primary-100)">📅</div>
                            <p class="text-sm font-medium" style="color:var(--muted)">Deskripsi kegiatan belum tersedia.</p>
                        </div>
                    @endif

                    {{-- Konten tambahan (blocks) --}}
                    @include('partials.content-blocks', ['blocks' => $event->blocks, 'title' => $event->title])
                </div>
            </div>

            {{-- Back --}}
            <div class="mt-8">
                <a href="{{ route('events.index') }}" class="btn-outline group text-sm">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Semua Kegiatan
                </a>
            </div>
        </article>

        {{-- ── Sidebar ──────────────────────────────────────── --}}
        <aside class="lg:col-span-1" data-aos="fade-up" data-aos-delay="100" data-aos-duration="500">
            <div class="sidebar-sticky space-y-4">

                {{-- Event detail card --}}
                <div class="fi-card p-5">
                    <div class="text-[10px] font-bold uppercase tracking-widest mb-3" style="color:var(--primary)">Detail Kegiatan</div>

                    <div class="event-meta-row">
                        <div class="event-meta-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-semibold mb-0.5" style="color:var(--muted)">Mulai</div>
                            <div class="text-sm font-bold leading-snug" style="color:var(--text)">{{ $event->starts_at->translatedFormat('l, d F Y') }}</div>
                            <div class="text-xs" style="color:var(--muted)">{{ $event->starts_at->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    @if($event->ends_at)
                    <div class="event-meta-row">
                        <div class="event-meta-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-semibold mb-0.5" style="color:var(--muted)">Selesai</div>
                            <div class="text-sm font-bold leading-snug" style="color:var(--text)">{{ $event->ends_at->translatedFormat('d F Y') }}</div>
                            <div class="text-xs" style="color:var(--muted)">{{ $event->ends_at->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    @endif

                    @if($event->location)
                    <div class="event-meta-row">
                        <div class="event-meta-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-semibold mb-0.5" style="color:var(--muted)">Lokasi</div>
                            <div class="text-sm font-bold leading-snug" style="color:var(--text)">{{ $event->location }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Table of contents --}}
                @php
                    preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/is', $event->content ?? '', $headings);
                @endphp
                @if(count($headings[1]) > 1)
                    <div class="fi-card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-md flex items-center justify-center" style="background:var(--primary-50)">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest" style="color:var(--primary)">Daftar Isi</span>
                        </div>
                        <ol class="space-y-0.5">
                            @foreach($headings[1] as $i => $heading)
                                <li class="toc-item">
                                    <span class="toc-num">{{ $i + 1 }}.</span>
                                    <span>{{ strip_tags($heading) }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                {{-- Back to list --}}
                <a href="{{ route('events.index') }}" class="btn-primary w-full justify-center text-xs group">
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Semua Kegiatan
                </a>
            </div>
        </aside>

    </div>
</div>

{{-- ── Related ──────────────────────────────────────────────── --}}
@if($related->isNotEmpty())
<section class="border-t py-14" style="border-color:#f3f4f6;background:var(--bg-alt)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 mb-8">
            <span class="w-4 h-px inline-block" style="background:var(--primary)"></span>
            <h2 class="text-xl font-extrabold" style="color:var(--text)">Kegiatan Lainnya</h2>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $rel)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <a href="{{ route('events.show', $rel) }}" class="relative h-40 block overflow-hidden">
                    <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </a>
                <div class="p-5">
                    <p class="text-xs mb-1.5" style="color:var(--muted)">{{ $rel->formatted_date }}</p>
                    <h3 class="font-bold text-sm line-clamp-2" style="color:var(--text)">
                        <a href="{{ route('events.show', $rel) }}" class="hover:opacity-70 transition-opacity">{{ $rel->title }}</a>
                    </h3>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
