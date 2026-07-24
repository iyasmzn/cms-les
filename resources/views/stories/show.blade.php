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
            <a href="{{ route('stories.index') }}" class="hover:opacity-75 transition-opacity">Cerita Santri</a>
            <span>/</span>
            <span class="font-medium line-clamp-1" style="color:#fff">{{ $story->title }}</span>
        </nav>
    </div>
</div>

{{-- ── Hero Image ───────────────────────────────────────────── --}}
<div class="relative h-64 sm:h-80 lg:h-96 overflow-hidden">
    <img src="{{ $story->thumbnail_url }}" alt="{{ $story->title }}"
         class="w-full h-full object-cover">
    <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.8),rgba(0,0,0,.2))"></div>
    <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
        <div class="max-w-7xl mx-auto">
            <span class="inline-block text-[11px] font-bold px-3 py-1.5 rounded-full mb-3 uppercase tracking-wide"
                  style="background:rgba(8,72,74,.85);color:#fff">Cerita Santri</span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-snug tracking-tight max-w-3xl mb-4">
                {{ $story->title }}
            </h1>
            {{-- Author --}}
            <div class="flex items-center gap-3">
                <img src="{{ $story->author_photo_url }}"
                     alt="{{ $story->author_name }}"
                     class="w-11 h-11 rounded-full object-cover ring-2"
                     style="--tw-ring-color:rgba(255,255,255,.5)">
                <div>
                    <div class="text-sm font-semibold text-white">{{ $story->author_name }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,.7)">
                        {{ implode(' · ', array_filter([$story->author_class, $story->author_year, $story->formatted_date])) }}
                    </div>
                </div>
            </div>
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
            <div class="fi-card overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,var(--primary),var(--primary-300) 60%,transparent)"></div>
                <div class="p-6 sm:p-10">

                    @if($story->excerpt)
                        <p class="article-lead" style="font-style:italic">"{{ $story->excerpt }}"</p>
                    @endif

                    @if($story->content)
                        <div class="article-prose">
                            {!! $story->content !!}
                        </div>
                    @endif

                    {{-- Konten tambahan (blocks) --}}
                    @include('partials.content-blocks', ['blocks' => $story->blocks, 'title' => $story->title])
                </div>
            </div>

            {{-- Back --}}
            <div class="mt-8">
                <a href="{{ route('stories.index') }}" class="btn-outline group text-sm">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Semua Cerita
                </a>
            </div>
        </article>

        {{-- ── Sidebar ──────────────────────────────────────── --}}
        <aside class="lg:col-span-1" data-aos="fade-up" data-aos-delay="100" data-aos-duration="500">
            <div class="sidebar-sticky space-y-4">

                {{-- Author card --}}
                <div class="fi-card p-5 text-center">
                    <img src="{{ $story->author_photo_url }}"
                         alt="{{ $story->author_name }}"
                         class="w-20 h-20 rounded-full object-cover ring-2 mx-auto mb-3"
                         style="--tw-ring-color:var(--primary-200)">
                    <div class="font-extrabold text-sm" style="color:var(--text)">{{ $story->author_name }}</div>
                    <div class="text-xs mt-0.5" style="color:var(--muted)">
                        {{ implode(' · ', array_filter([$story->author_class, $story->author_year])) ?: 'Santri' }}
                    </div>
                    @if($story->formatted_date !== '-')
                        <div class="inline-flex items-center gap-1.5 text-[11px] mt-3 px-2.5 py-1 rounded-full" style="background:var(--primary-50);color:var(--primary-700)">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $story->formatted_date }}
                        </div>
                    @endif
                </div>

                {{-- Table of contents --}}
                @php
                    preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/is', $story->content ?? '', $headings);
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
                <a href="{{ route('stories.index') }}" class="btn-primary w-full justify-center text-xs group">
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Semua Cerita
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
            <h2 class="text-xl font-extrabold" style="color:var(--text)">Cerita Lainnya</h2>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $rel)
            <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <a href="{{ route('stories.show', $rel) }}" class="relative h-44 block overflow-hidden">
                    <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </a>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-sm line-clamp-2 mb-3 flex-1" style="color:var(--text)">
                        <a href="{{ route('stories.show', $rel) }}" class="hover:opacity-70 transition-opacity">{{ $rel->title }}</a>
                    </h3>
                    <div class="flex items-center gap-2.5 pt-3" style="border-top:1px solid var(--border)">
                        <img src="{{ $rel->author_photo_url }}" alt="{{ $rel->author_name }}"
                             class="w-7 h-7 rounded-full object-cover">
                        <span class="text-xs font-medium" style="color:var(--muted)">{{ $rel->author_name }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
