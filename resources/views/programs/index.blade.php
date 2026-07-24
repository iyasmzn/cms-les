@extends('layouts.public')

@push('head')
<style>
    .programs-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 60%, #0c5e60 100%);
        position: relative;
        overflow: hidden;
    }
    .programs-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 50% 50% at 30% 60%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 70% 10%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
    .program-card { transition: transform .3s ease; }
    .program-card:hover { transform: translateY(-6px); }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="programs-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">
    <x-hero-geo />
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            🎓 Program Unggulan
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Program di {{ setting('site_name', config('app.name')) }}
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Berbagai program yang dirancang untuk membentuk santri berprestasi dan berakhlak mulia.
        </p>
    </div>
</section>

{{-- ── Programs Grid ────────────────────────────────────── --}}
<section class="py-16 sm:py-20" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($programs->isNotEmpty())
        <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($programs as $program)
            <article class="program-card fi-card fi-card-hover group flex flex-col overflow-hidden"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">

                {{-- Image --}}
                <a href="{{ route('programs.show', $program) }}" class="relative h-52 block overflow-hidden">
                    <img src="{{ $program->thumbnail_url }}"
                         alt="{{ $program->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.5),transparent 60%)"></div>
                    @if($url = icon_url($program->icon_image))
                    <div class="absolute bottom-4 left-4 w-9 h-9">
                        <img src="{{ $url }}" alt="{{ $program->title }}" loading="lazy" class="w-full h-full object-contain drop-shadow">
                    </div>
                    @elseif($program->icon)
                    <div class="absolute bottom-4 left-4 text-3xl">{{ $program->icon }}</div>
                    @endif
                </a>

                {{-- Content --}}
                <div class="p-6 flex flex-col flex-1">
                    @if($program->category)
                    <span class="text-xs font-bold mb-2 block" style="color:var(--primary)">{{ $program->category }}</span>
                    @endif

                    <h3 class="font-extrabold text-lg leading-snug mb-3 flex-1" style="color:var(--text)">
                        <a href="{{ route('programs.show', $program) }}" class="hover:opacity-70 transition-opacity">
                            {{ $program->title }}
                        </a>
                    </h3>

                    @if($program->excerpt)
                    <p class="text-sm leading-relaxed line-clamp-3 mb-5" style="color:var(--muted)">{{ $program->excerpt }}</p>
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

        @else
        <div class="text-center py-24 fi-card">
            <div class="text-6xl mb-4">🎓</div>
            <p class="text-base font-medium" style="color:var(--muted)">Belum ada program yang dipublikasikan.</p>
        </div>
        @endif

    </div>
</section>

@endsection
