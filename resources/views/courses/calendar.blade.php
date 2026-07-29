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
    .cal-grid { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); }
    .cal-cell { min-height:92px; }
    @media (min-width:640px){ .cal-cell { min-height:120px; } }
</style>
@endpush

@section('content')
@php
    $today = \Illuminate\Support\Carbon::today();
    $weekdays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-10 sm:pt-36 sm:pb-12">
    <x-hero-geo />
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-10 flex flex-wrap items-end justify-between gap-4" data-aos="fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-4">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">My Schedule</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">Session Calendar</h1>
        </div>
        <a href="{{ route('courses.mine') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/30 text-white/90 text-sm font-semibold hover:bg-white/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            List view
        </a>
    </div>
</section>

{{-- ═══════════════════════ CALENDAR ═══════════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Month navigation --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('courses.calendar', ['month' => $calendar->previousParam()]) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-xl border transition-colors hover:border-amber-500"
           style="border-color:var(--border);color:var(--text)" aria-label="Previous month">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg sm:text-xl font-extrabold" style="color:var(--text)">{{ $calendar->label() }}</h2>
        <a href="{{ route('courses.calendar', ['month' => $calendar->nextParam()]) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-xl border transition-colors hover:border-amber-500"
           style="border-color:var(--border);color:var(--text)" aria-label="Next month">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Weekday header --}}
    <div class="cal-grid gap-px mb-px text-center text-xs font-bold uppercase tracking-wider" style="color:var(--muted)">
        @foreach($weekdays as $wd)
            <div class="py-2">{{ $wd }}</div>
        @endforeach
    </div>

    {{-- Weeks --}}
    <div class="rounded-2xl overflow-hidden border" style="border-color:var(--border);background:var(--border)">
        @foreach($calendar->weeks() as $week)
        <div class="cal-grid gap-px">
            @foreach($week as $day)
            @php
                $daySessions = $sessions[$day->toDateString()] ?? collect();
                $inMonth = $calendar->isCurrentMonth($day);
                $isToday = $day->isSameDay($today);
            @endphp
            <div class="cal-cell p-1.5 sm:p-2 flex flex-col gap-1" style="background:var(--bg); {{ $inMonth ? '' : 'opacity:.45;' }}">
                <div class="text-xs font-bold {{ $isToday ? 'text-white bg-amber-500 rounded-full w-6 h-6 flex items-center justify-center' : '' }}"
                     style="{{ $isToday ? '' : 'color:var(--muted)' }}">{{ $day->day }}</div>

                @foreach($daySessions as $session)
                    <a href="{{ $session->group?->institution ? route('courses.show', $session->group->institution) : '#' }}"
                       class="block rounded-md px-1.5 py-1 text-[10px] sm:text-xs leading-tight bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors"
                       title="{{ $session->group?->name }}{{ $session->timeLabel() ? ' · '.$session->timeLabel() : '' }}{{ $session->resolvedLocation() ? ' @ '.$session->resolvedLocation() : '' }}">
                        <span class="font-bold text-amber-800 line-clamp-1">{{ $session->group?->name }}</span>
                        @if($session->timeLabel())
                            <span class="block text-amber-700">{{ $session->timeLabel() }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <p class="text-xs mt-4 text-center" style="color:var(--muted)">Showing sessions for groups you're registered in. Cancelled sessions are hidden.</p>
</div>
@endsection
