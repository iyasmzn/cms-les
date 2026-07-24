@extends('layouts.public')

@push('head')
<style>
    .course-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0a1628 100%);
        position: relative;
        overflow: hidden;
    }
    .course-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 10% 50%, rgba(217,119,6,.25) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 90% 10%, rgba(251,191,36,.12) 0%, transparent 50%);
    }
    .course-card { transition: transform .18s, border-color .18s; }
    .course-card:hover { transform: translateY(-4px); border-color: #d97706; }
</style>
@endpush

@section('content')
@php
    $siteName = setting('site_name', config('app.name'));
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-16 sm:pt-36 sm:pb-20">
    <x-hero-geo />
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-10 text-center" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-5">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">Courses</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
            Our Courses<br>
            <span class="text-amber-400">Pick a Course to Join</span>
        </h1>
        <p class="text-white/70 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
            {{ $siteName }} runs several courses (les), each with its own groups (kelompok). Choose a course to see its groups, schedule, level, coach, and to register online.
        </p>
    </div>
</section>

{{-- ═══════════════════════ COURSE LIST ════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if($institutions->isEmpty())
        <div class="fi-card p-10 text-center max-w-lg mx-auto" data-aos="fade-up">
            <div class="text-5xl mb-4">🏊</div>
            <h2 class="font-bold text-lg mb-2" style="color:var(--text)">No Courses Available Yet</h2>
            <p class="text-sm" style="color:var(--muted)">Course information will be available soon. Please check back later.</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($institutions as $institution)
            <a href="{{ route('courses.show', $institution) }}"
               class="course-card fi-card flex flex-col border overflow-hidden p-6" style="border-color:var(--border)"
               data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0">
                        @if($url = icon_url($institution->icon_image))
                            <img src="{{ $url }}" alt="{{ $institution->name }}" loading="lazy" class="w-8 h-8 object-contain">
                        @else
                            <span class="text-3xl">{{ $institution->icon ?: '🏊' }}</span>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $institution->groups_count }} {{ \Illuminate\Support\Str::plural('group', $institution->groups_count) }}
                    </span>
                </div>

                @if($institution->short_name)
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-1">{{ $institution->short_name }}</span>
                @endif
                <h2 class="font-bold text-lg mb-2" style="color:var(--text)">{{ $institution->name }}</h2>
                @if($institution->description)
                    <p class="text-sm leading-relaxed mb-4" style="color:var(--muted)">{{ \Illuminate\Support\Str::limit($institution->description, 130) }}</p>
                @endif

                <span class="mt-auto inline-flex items-center gap-2 text-sm font-bold text-amber-600">
                    View Groups
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
