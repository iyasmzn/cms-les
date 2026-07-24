@extends('layouts.public')

@push('head')
<style>
    .qa-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 60%, #0a5a5c 100%);
        position: relative;
        overflow: hidden;
    }
    .qa-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 60% at 80% 20%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 10% 80%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
    .qa-form-input {
        width: 100%;
        padding: .65rem .9rem;
        border-radius: .5rem;
        border: 1.5px solid var(--border);
        background: var(--card);
        color: var(--text);
        font-size: .875rem;
        transition: border-color .15s;
        outline: none;
    }
    .qa-form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(8,72,74,.12); }
    .qa-label { display: block; font-size: .8125rem; font-weight: 600; margin-bottom: .35rem; color: var(--text); }
    .qa-card {
        border-radius: 1rem;
        border: 1.5px solid var(--border);
        background: var(--card);
        padding: 1.5rem;
    }
    .qa-badge-answered {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .7rem; font-weight: 700;
        padding: .25rem .75rem; border-radius: 9999px;
        background: rgba(8,72,74,.1); color: var(--primary);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="qa-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">
    <x-hero-geo />
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            💬 Tanya Jawab
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Ada Pertanyaan?
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Kirimkan pertanyaan Anda seputar {{ setting('site_name', config('app.name')) }} dan tim kami akan segera menjawab.
        </p>
    </div>
</section>

<section class="py-16 sm:py-20" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ── Form ────────────────────────────────────────────── --}}
        <div class="qa-card mb-12" data-aos="fade-up">
            <h2 class="text-xl font-extrabold mb-1" style="color:var(--text)">Kirim Pertanyaan</h2>
            <p class="text-sm mb-6" style="color:var(--muted)">Pertanyaan yang telah dijawab akan ditampilkan di bawah.</p>

            @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-lg mb-6"
                 style="background:rgba(8,72,74,.08);border:1px solid rgba(8,72,74,.2)">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium" style="color:var(--primary)">{{ session('success') }}</p>
            </div>
            @endif

            @auth
            <form method="POST" action="{{ route('questions.store') }}" class="space-y-5">
                @csrf
                <div class="flex items-center gap-2.5">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover">
                    <span class="text-sm font-semibold" style="color:var(--text)">{{ auth()->user()->name }}</span>
                </div>
                <div>
                    <label class="qa-label">Pertanyaan <span style="color:#ef4444">*</span></label>
                    <textarea name="question" rows="4"
                              class="qa-form-input @error('question') border-red-400 @enderror"
                              placeholder="Tulis pertanyaan Anda di sini...">{{ old('question') }}</textarea>
                    @error('question')<p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:var(--muted)">
                    <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous'))
                           class="rounded" style="accent-color:var(--primary)">
                    Tampilkan sebagai anonim (sembunyikan nama saya)
                </label>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white transition-opacity hover:opacity-80"
                        style="background:var(--primary)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Pertanyaan
                </button>
            </form>
            @else
            <div class="text-center py-6">
                <p class="text-sm mb-4" style="color:var(--muted)">Anda harus masuk terlebih dahulu untuk mengajukan pertanyaan.</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white transition-opacity hover:opacity-80"
                   style="background:var(--primary)">
                    Masuk untuk Bertanya
                </a>
            </div>
            @endauth
        </div>

        {{-- ── Q&A List ──────────────────────────────────────── --}}
        <div data-aos="fade-up" data-aos-delay="100">
            <h2 class="text-xl font-extrabold mb-6" style="color:var(--text)">Pertanyaan yang Sudah Dijawab</h2>

            @if($questions->isNotEmpty())
            <div class="space-y-5">
                @foreach($questions as $qa)
                <div class="qa-card">
                    {{-- Question --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center font-extrabold text-sm text-white"
                             style="background:var(--primary)">
                            {{ strtoupper(substr($qa->display_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-sm" style="color:var(--text)">{{ $qa->display_name }}</span>
                                <span class="text-xs" style="color:var(--muted)">{{ $qa->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm leading-relaxed" style="color:var(--text)">{{ $qa->question }}</p>
                        </div>
                    </div>

                    {{-- Answer --}}
                    @if($qa->answer)
                    <div class="flex items-start gap-3 pl-3 pt-4"
                         style="border-top:1.5px solid var(--border)">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center"
                             style="background:rgba(8,72,74,.1)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-sm" style="color:var(--primary)">
                                    {{ setting('site_name', config('app.name')) }}
                                </span>
                                <span class="qa-badge-answered">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Dijawab
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed" style="color:var(--text)">{!! nl2br(e($qa->answer)) !!}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($questions->hasPages())
            <div class="mt-10">
                {{ $questions->links() }}
            </div>
            @endif

            @else
            <div class="text-center py-16 qa-card">
                <div class="text-5xl mb-4">💬</div>
                <p class="text-base font-medium" style="color:var(--muted)">Belum ada pertanyaan yang dijawab.</p>
                <p class="text-sm mt-1" style="color:var(--muted)">Jadilah yang pertama bertanya!</p>
            </div>
            @endif
        </div>

    </div>
</section>

@endsection
