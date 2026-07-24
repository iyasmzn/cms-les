@extends('layouts.public')

@push('head')
<style>
    .stories-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 55%, #0b5e60 100%);
        position: relative;
        overflow: hidden;
    }
    .stories-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 55% 55% at 15% 40%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 85% 70%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="stories-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">

    <x-hero-geo />

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            📖 Cerita Santri
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Kisah Inspiratif Santri
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Cerita perjuangan, pengalaman, dan prestasi para santri {{ setting('site_name', config('app.name')) }}.
        </p>
    </div>
</section>

{{-- ── Stories Grid ─────────────────────────────────────── --}}
<section class="py-16 sm:py-20" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($stories->isNotEmpty())
        <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($stories as $story)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">

                {{-- Image --}}
                <a href="{{ route('stories.show', $story) }}" class="relative h-52 block overflow-hidden">
                    <img src="{{ $story->thumbnail_url }}"
                         alt="{{ $story->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.5),transparent 60%)"></div>
                </a>

                {{-- Content --}}
                <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-extrabold text-base leading-snug mb-2 line-clamp-2" style="color:var(--text)">
                        <a href="{{ route('stories.show', $story) }}" class="hover:opacity-70 transition-opacity">
                            {{ $story->title }}
                        </a>
                    </h3>

                    @if($story->excerpt)
                    <p class="text-sm leading-relaxed line-clamp-3 mb-5 flex-1" style="color:var(--muted)">{{ $story->excerpt }}</p>
                    @else
                    <div class="flex-1"></div>
                    @endif

                    {{-- Author --}}
                    <div class="flex items-center gap-3 pt-4" style="border-top:1px solid var(--border)">
                        <img src="{{ $story->author_photo_url }}"
                             alt="{{ $story->author_name }}"
                             class="w-9 h-9 rounded-full object-cover ring-2"
                             style="ring-color:var(--primary)">
                        <div>
                            <div class="text-sm font-semibold leading-tight" style="color:var(--text)">
                                {{ $story->author_name }}
                            </div>
                            <div class="text-xs" style="color:var(--muted)">
                                {{ implode(' · ', array_filter([$story->author_class, $story->author_year])) }}
                            </div>
                        </div>
                        <span class="ml-auto text-xs" style="color:var(--muted)">{{ $story->formatted_date }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($stories->hasPages())
        <div class="mt-12">
            {{ $stories->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-24 fi-card">
            <div class="text-6xl mb-4">📖</div>
            <p class="text-base font-medium" style="color:var(--muted)">Belum ada cerita yang dipublikasikan.</p>
        </div>
        @endif

    </div>
</section>

@endsection
