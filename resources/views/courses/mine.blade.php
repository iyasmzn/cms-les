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
</style>
@endpush

@section('content')
@php
    $statusStyles = [
        'pending'  => ['label' => 'Pending',  'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'active'   => ['label' => 'Active',   'class' => 'bg-green-50 text-green-700 border-green-200'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-gray-100 text-gray-500 border-gray-200'],
    ];
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-12 sm:pt-36 sm:pb-14">
    <x-hero-geo />
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 z-10 flex flex-wrap items-end justify-between gap-4" data-aos="fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-5">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">My Courses</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">Your Course Registrations</h1>
            <p class="text-white/70 text-sm sm:text-base mt-2">Track the groups you registered for, their schedule, and your status.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('courses.billing') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-sm font-bold hover:bg-white/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Billing
            </a>
            <a href="{{ route('courses.calendar') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 text-slate-900 text-sm font-bold hover:bg-amber-400 transition-colors shadow-lg shadow-amber-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════ REGISTRATIONS ══════════════════════ --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if($registrations->isEmpty())
        <div class="fi-card p-10 text-center" data-aos="fade-up">
            <div class="text-5xl mb-4">🏊</div>
            <h2 class="font-bold text-lg mb-2" style="color:var(--text)">No Registrations Yet</h2>
            <p class="text-sm mb-6" style="color:var(--muted)">You haven't registered for any course yet.</p>
            <a href="{{ route('courses.index') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold">
                Browse Courses
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($registrations as $registration)
            @php
                $group = $registration->group;
                $institution = $group?->institution;
                $status = $statusStyles[$registration->status] ?? $statusStyles['pending'];
                $totals = $registration->paymentTotals();
                $rupiah = fn ($n) => 'Rp'.number_format((float) $n, 0, ',', '.');
            @endphp
            <div class="fi-card border p-5 sm:p-6" style="border-color:var(--border)" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        @if($institution)
                            <span class="text-xs font-bold uppercase tracking-widest text-amber-600">{{ $institution->name }}</span>
                        @endif
                        <h2 class="font-bold text-lg mt-0.5" style="color:var(--text)">
                            @if($group)
                                <a href="{{ route('courses.sessions', $registration) }}" class="hover:underline">{{ $group->name }}</a>
                            @else
                                Group removed
                            @endif
                        </h2>
                        <dl class="mt-3 space-y-1.5 text-sm" style="color:var(--muted)">
                            @if($group && ($schedule = $group->scheduleLabel()))
                                <div class="flex items-center gap-2">🗓️ <span>{{ $schedule }}</span></div>
                            @endif
                            @if($group?->location)
                                <div class="flex items-center gap-2">📍 <span>{{ $group->location }}</span></div>
                            @endif
                            @if($group?->teacher)
                                <div class="flex items-center gap-2">🧑‍🏫 <span>{{ $group->teacher->name }}</span></div>
                            @endif
                            <div class="flex items-center gap-2">👤 <span>{{ $registration->full_name }}</span></div>
                        </dl>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $status['class'] }}">{{ $status['label'] }}</span>
                        @if($registration->registration_number)
                            <p class="text-[11px] font-mono mt-2" style="color:var(--muted)">{{ $registration->registration_number }}</p>
                        @endif
                    </div>
                </div>

                @if($registration->payments->isNotEmpty())
                    <div class="mt-4 pt-4 border-t flex flex-wrap items-center gap-x-6 gap-y-2 text-sm" style="border-color:var(--border)">
                        <span class="inline-flex items-center gap-1.5" style="color:var(--muted)">
                            💳 Paid: <span class="font-semibold text-green-600">{{ $rupiah($totals['paid']) }}</span>
                        </span>
                        @if($totals['outstanding'] > 0)
                            <span class="inline-flex items-center gap-1.5" style="color:var(--muted)">
                                ⏳ Outstanding: <span class="font-semibold text-amber-600">{{ $rupiah($totals['outstanding']) }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-green-600 font-semibold">✅ Fully paid</span>
                        @endif
                        @if($totals['review'] > 0)
                            <span class="inline-flex items-center gap-1.5" style="color:var(--muted)">
                                ⏱️ In verification: <span class="font-semibold text-blue-600">{{ $rupiah($totals['review']) }}</span>
                            </span>
                        @endif
                        @if($totals['waived'] > 0)
                            <span class="inline-flex items-center gap-1.5" style="color:var(--muted)">
                                🎟️ Waived: <span class="font-semibold">{{ $rupiah($totals['waived']) }}</span>
                            </span>
                        @endif
                    </div>
                @endif

                @if($group || $institution || $registration->payments->isNotEmpty())
                    <div class="mt-4 pt-4 border-t flex flex-wrap items-center gap-x-5 gap-y-2" style="border-color:var(--border)">
                        @if($group)
                            <a href="{{ route('courses.sessions', $registration) }}" class="inline-flex items-center gap-1.5 text-sm font-bold" style="color:var(--primary)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Sessions &amp; schedule
                            </a>
                        @endif
                        @if($registration->payments->isNotEmpty())
                            <a href="{{ route('courses.billing', ['registration' => $registration->id]) }}" class="inline-flex items-center gap-1.5 text-sm font-bold" style="color:var(--primary)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                Bills &amp; payments
                            </a>
                        @endif
                        @if($institution)
                            <a href="{{ route('courses.show', $institution) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:var(--muted)">
                                View course
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
