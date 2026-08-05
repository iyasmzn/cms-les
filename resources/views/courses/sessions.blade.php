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
    $sessionStyles = [
        'scheduled' => ['label' => 'Scheduled', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'completed' => ['label' => 'Completed', 'class' => 'bg-green-50 text-green-700 border-green-200'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-50 text-red-600 border-red-200'],
    ];

    $billStyles = [
        'unpaid' => ['label' => 'Unpaid', 'class' => 'text-amber-700'],
        'paid'   => ['label' => 'Paid',   'class' => 'text-green-600'],
        'waived' => ['label' => 'Waived', 'class' => 'text-gray-500'],
    ];

    $rupiah = fn ($n) => 'Rp'.number_format((float) $n, 0, ',', '.');
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-10 sm:pt-36 sm:pb-12">
    <x-hero-geo />
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 z-10" data-aos="fade-up">
        <a href="{{ route('courses.mine') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-white/60 hover:text-white transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            My Courses
        </a>

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0">
                @if($group->institution)
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-300">{{ $group->institution->name }}</span>
                @endif
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight mt-1">{{ $group->name }}</h1>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm text-white/70 mt-3">
                    @if($schedule = $group->scheduleLabel())
                        <span class="flex items-center gap-2">🗓️ {{ $schedule }}</span>
                    @endif
                    @if($group->location)
                        <span class="flex items-center gap-2">📍 {{ $group->location }}</span>
                    @endif
                    @if($group->teacher)
                        <span class="flex items-center gap-2">🧑‍🏫 {{ $group->teacher->name }}</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($paymentBySession->isNotEmpty())
                    <a href="{{ route('courses.billing', ['registration' => $registration->id]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-sm font-bold hover:bg-white/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Bills
                    </a>
                @endif
                <a href="{{ route('courses.calendar', ['group' => $group->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 text-slate-900 text-sm font-bold hover:bg-amber-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendar view
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ SESSIONS ═══════════════════════════ --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    @if($upcoming->isEmpty() && $past->isEmpty())
        <div class="fi-card p-10 text-center" data-aos="fade-up">
            <div class="text-5xl mb-4">🗓️</div>
            <h2 class="font-bold text-lg mb-2" style="color:var(--text)">No Sessions Scheduled</h2>
            <p class="text-sm" style="color:var(--muted)">Sessions for this group haven't been scheduled yet. Check back soon.</p>
        </div>
    @endif

    @foreach([['Upcoming Sessions', $upcoming], ['Past Sessions', $past]] as [$heading, $list])
        @if($list->isNotEmpty())
        <section data-aos="fade-up">
            <h2 class="text-lg font-extrabold mb-4" style="color:var(--text)">
                {{ $heading }}
                <span class="text-sm font-semibold" style="color:var(--muted)">({{ $list->count() }})</span>
            </h2>

            <div class="space-y-3">
                @foreach($list as $session)
                @php $status = $sessionStyles[$session->status] ?? $sessionStyles['scheduled']; @endphp
                <div class="fi-card border p-4 sm:p-5 flex items-start gap-4 {{ $session->status === 'cancelled' ? 'opacity-60' : '' }}"
                     style="border-color:var(--border)">

                    {{-- Date chip --}}
                    <div class="shrink-0 w-14 text-center rounded-xl border py-2" style="border-color:var(--border)">
                        <div class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">{{ $session->date->format('M') }}</div>
                        <div class="text-xl font-extrabold leading-none mt-0.5" style="color:var(--text)">{{ $session->date->format('j') }}</div>
                        <div class="text-[10px] font-semibold mt-0.5" style="color:var(--muted)">{{ $session->date->format('D') }}</div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-bold {{ $session->status === 'cancelled' ? 'line-through' : '' }}" style="color:var(--text)">
                                {{ $session->topic ?: $group->name }}
                            </h3>
                            <span class="inline-flex items-center text-[11px] font-bold px-2.5 py-1 rounded-full border shrink-0 {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </div>

                        <dl class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm" style="color:var(--muted)">
                            @if($session->timeLabel())
                                <div class="flex items-center gap-1.5">🕒 <span>{{ $session->timeLabel() }}</span></div>
                            @endif
                            @if($session->resolvedLocation())
                                <div class="flex items-center gap-1.5">📍 <span>{{ $session->resolvedLocation() }}</span></div>
                            @endif
                            @if($payment = $paymentBySession->get($session->id))
                                @php $bill = $billStyles[$payment->status] ?? $billStyles['unpaid']; @endphp
                                <div class="flex items-center gap-1.5 font-semibold {{ $bill['class'] }}">
                                    💳 <span>{{ $rupiah($payment->amount) }} · {{ $bill['label'] }}</span>
                                </div>
                            @endif
                        </dl>

                        @if($session->notes)
                            <p class="text-sm mt-2" style="color:var(--muted)">{{ $session->notes }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach
</div>
@endsection
