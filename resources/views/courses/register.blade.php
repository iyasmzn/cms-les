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
    .course-input {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: .75rem;
        padding: .625rem .875rem;
        font-size: .875rem;
        background: var(--bg, #fff);
        color: var(--text);
        transition: border-color .15s, box-shadow .15s;
    }
    .course-input:focus {
        outline: none;
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217,119,6,.15);
    }
    .course-label { display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem; color:var(--text); }
    .course-hint { font-size:.75rem; }
</style>
@endpush

@section('content')
{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-12 sm:pt-36 sm:pb-14">
    <x-hero-geo />
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 z-10" data-aos="fade-up">
        <a href="{{ route('courses.show', $institution) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-300 hover:text-amber-200 mb-5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            {{ $institution->name }}
        </a>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">Register — {{ $group->name }}</h1>
        <p class="text-white/70 text-sm sm:text-base mt-2">
            @if($schedule = $group->scheduleLabel())🗓️ {{ $schedule }} @endif
            @if($group->level) · {{ $group->level }} @endif
        </p>
    </div>
</section>

{{-- ═══════════════════════ FORM ═══════════════════════════════ --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if(session('error'))
        <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('courses.register.store', [$institution, $group]) }}" class="fi-card border p-6 sm:p-8 space-y-5" style="border-color:var(--border)">
        @csrf
        <x-spam-guard />

        <div>
            <label for="full_name" class="course-label">Full Name <span class="text-red-500">*</span></label>
            <input type="text" id="full_name" name="full_name" class="course-input @error('full_name') border-red-400 @enderror"
                   value="{{ old('full_name') }}" placeholder="Participant's full name" required>
            @error('full_name')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="gender" class="course-label">Gender</label>
                <select id="gender" name="gender" class="course-input @error('gender') border-red-400 @enderror">
                    <option value="">—</option>
                    <option value="male" @selected(old('gender') === 'male')>Male</option>
                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                </select>
                @error('gender')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="course-label">Phone <span class="text-red-500">*</span></label>
                <input type="tel" id="phone" name="phone" class="course-input @error('phone') border-red-400 @enderror"
                       value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                @error('phone')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="birth_place" class="course-label">Birth Place</label>
                <input type="text" id="birth_place" name="birth_place" class="course-input @error('birth_place') border-red-400 @enderror"
                       value="{{ old('birth_place') }}" placeholder="City of birth">
                @error('birth_place')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="birth_date" class="course-label">Birth Date</label>
                <input type="date" id="birth_date" name="birth_date" class="course-input @error('birth_date') border-red-400 @enderror"
                       value="{{ old('birth_date') }}">
                @error('birth_date')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="email" class="course-label">Email</label>
            <input type="email" id="email" name="email" class="course-input @error('email') border-red-400 @enderror"
                   value="{{ old('email') }}" placeholder="name@email.com">
            @error('email')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="address" class="course-label">Address</label>
            <textarea id="address" name="address" rows="2" class="course-input @error('address') border-red-400 @enderror"
                      placeholder="Home address">{{ old('address') }}</textarea>
            @error('address')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="parent_name" class="course-label">Parent/Guardian Name</label>
                <input type="text" id="parent_name" name="parent_name" class="course-input @error('parent_name') border-red-400 @enderror"
                       value="{{ old('parent_name') }}">
                @error('parent_name')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="parent_phone" class="course-label">Parent/Guardian Phone</label>
                <input type="tel" id="parent_phone" name="parent_phone" class="course-input @error('parent_phone') border-red-400 @enderror"
                       value="{{ old('parent_phone') }}">
                @error('parent_phone')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="notes" class="course-label">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="course-input @error('notes') border-red-400 @enderror"
                      placeholder="Anything we should know?">{{ old('notes') }}</textarea>
            @error('notes')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold">
            Submit Registration
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
    </form>
</div>
@endsection
