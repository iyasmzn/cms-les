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
    .group-card { transition: transform .18s, border-color .18s; }
    .group-card:hover { transform: translateY(-3px); border-color: #d97706; }
</style>
@endpush

@section('content')
@php
    $siteName = setting('site_name', config('app.name'));
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-14 sm:pt-36 sm:pb-16">
    <x-hero-geo />
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-10" data-aos="fade-up">
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-300 hover:text-amber-200 mb-5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            All Courses
        </a>
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                @if($url = icon_url($institution->icon_image))
                    <img src="{{ $url }}" alt="{{ $institution->name }}" class="w-9 h-9 object-contain">
                @else
                    <span class="text-4xl">{{ $institution->icon ?: '🏊' }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">{{ $institution->name }}</h1>
                @if($institution->description)
                    <p class="text-white/70 text-sm sm:text-base mt-1 max-w-2xl">{{ $institution->description }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ FLASH MESSAGES ═════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    @if(session('success'))
        <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800" data-aos="fade-up">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800" data-aos="fade-up">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @guest
        @if(session('offer_account') && Route::has('register'))
            @php $offer = session('offer_account'); @endphp
            <div class="mb-6 fi-card border p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4" style="border-color:var(--border)" data-aos="fade-up">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0 text-2xl">👤</div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold" style="color:var(--text)">Create an account to track your registration</h3>
                    <p class="text-sm mt-0.5" style="color:var(--muted)">Sign up with the same details to follow your status, schedule, and payments under <strong>My Courses</strong>.</p>
                </div>
                <a href="{{ route('register', array_filter(['name' => $offer['name'] ?? null, 'email' => $offer['email'] ?? null])) }}"
                   class="btn-primary inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold shrink-0">
                    Create Account
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @endif
    @endguest
</div>

{{-- ═══════════════════════ GROUPS ═════════════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <h2 class="font-bold text-xl mb-6" style="color:var(--text)">Groups</h2>

    @if($groups->isEmpty())
        <div class="fi-card p-10 text-center" data-aos="fade-up">
            <div class="text-5xl mb-4">👥</div>
            <h3 class="font-bold text-lg mb-2" style="color:var(--text)">No Groups Yet</h3>
            <p class="text-sm" style="color:var(--muted)">Groups for this course will be available soon.</p>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2">
            @foreach($groups as $group)
            @php
                $open = $group->isOpen();
                $remaining = $group->remainingSeats();
            @endphp
            <div class="group-card fi-card border p-6 flex flex-col" style="border-color:var(--border)"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="font-bold text-lg" style="color:var(--text)">{{ $group->name }}</h3>
                        @if($group->level)
                            <span class="inline-block mt-1 text-xs font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">{{ $group->level }}</span>
                        @endif
                    </div>
                    @if($open)
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Open
                        </span>
                    @else
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 border border-gray-200 shrink-0">Full</span>
                    @endif
                </div>

                <dl class="space-y-2 text-sm mb-5" style="color:var(--muted)">
                    @if($schedule = $group->scheduleLabel())
                        <div class="flex items-center gap-2">🗓️ <span>{{ $schedule }}</span></div>
                    @endif
                    @if($group->location)
                        <div class="flex items-center gap-2">📍 <span>{{ $group->location }}</span></div>
                    @endif
                    @if($group->teacher)
                        <div class="flex items-center gap-2">🧑‍🏫 <span>{{ $group->teacher->name }}</span></div>
                    @endif
                    @if($group->capacity)
                        <div class="flex items-center gap-2">👥 <span>{{ $remaining }} of {{ $group->capacity }} seats left</span></div>
                    @endif
                </dl>

                @if($group->description)
                    <p class="text-sm leading-relaxed mb-5" style="color:var(--muted)">{{ $group->description }}</p>
                @endif

                @if($existingRegistration && $existingRegistration->group_id === $group->id)
                    <a href="{{ route('courses.sessions', $existingRegistration) }}"
                       class="mt-auto inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors">
                        ✅ You're registered — view sessions
                    </a>
                @elseif($existingRegistration)
                    <span class="mt-auto inline-flex items-center justify-center text-center w-full py-2.5 px-3 rounded-xl text-sm font-semibold bg-gray-100 text-gray-500 cursor-not-allowed"
                          title="Only one group per course is allowed.">
                        Already in "{{ $existingRegistration->group?->name }}"
                    </span>
                @elseif($open)
                    <a href="{{ route('courses.register', [$institution, $group]) }}"
                       class="btn-primary mt-auto inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold">
                        Register
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <span class="mt-auto inline-flex items-center justify-center w-full py-2.5 rounded-xl text-sm font-bold bg-gray-100 text-gray-400 cursor-not-allowed">
                        Registration Closed
                    </span>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
