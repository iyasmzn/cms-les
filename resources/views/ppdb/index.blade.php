@extends('layouts.public')

@push('head')
<style>
    .ppdb-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0a1628 100%);
        position: relative;
        overflow: hidden;
    }
    .ppdb-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 10% 50%, rgba(217,119,6,.25) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 90% 10%, rgba(251,191,36,.12) 0%, transparent 50%);
    }
    .jenjang-card { transition: transform .18s, border-color .18s; }
    .jenjang-card:hover { transform: translateY(-4px); border-color: #d97706; }
</style>
@endpush

@section('content')
@php
    $siteName = setting('site_name', config('app.name'));
    $spmbYear = spmb_year_label();
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="ppdb-hero -mt-17 pt-32 pb-16 sm:pt-36 sm:pb-20">
    <x-hero-geo />
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-10 text-center" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-5">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">PPDB / SPMB {{ $spmbYear }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
            Penerimaan Peserta Didik Baru<br>
            <span class="text-amber-400">Pilih Jenjang Pendidikan</span>
        </h1>
        <p class="text-white/70 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
            {{ $siteName }} membuka pendaftaran untuk beberapa jenjang. Pilih jenjang yang dituju untuk melihat prosedur, jadwal gelombang, biaya, dan formulir pendaftaran.
        </p>
    </div>
</section>

{{-- ═══════════════════════ PILIHAN JENJANG ════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if($institutions->isEmpty())
        <div class="fi-card p-10 text-center max-w-lg mx-auto" data-aos="fade-up">
            <div class="text-5xl mb-4">🏫</div>
            <h2 class="font-bold text-lg mb-2" style="color:var(--text)">Belum Ada Jenjang Tersedia</h2>
            <p class="text-sm" style="color:var(--muted)">Informasi PPDB akan segera tersedia. Silakan cek kembali beberapa saat lagi.</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($institutions as $institution)
            @php
                $open = $institution->registrationOpen();
                $wave = $institution->usesInternalForm() ? \App\Models\RegistrationWave::relevant($institution) : null;
                $fmtDate = fn ($d) => $d ? $d->locale('id')->translatedFormat('d M Y') : null;
            @endphp
            <div class="jenjang-card fi-card flex flex-col border overflow-hidden" style="border-color:var(--border)"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
            <a href="{{ route('ppdb.show', $institution) }}"
               class="p-6 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0">
                        @if($url = icon_url($institution->icon_image))
                            <img src="{{ $url }}" alt="{{ $institution->name }}" loading="lazy" class="w-8 h-8 object-contain">
                        @else
                            <span class="text-3xl">{{ $institution->icon ?: '🏫' }}</span>
                        @endif
                    </div>
                    @if($open)
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Dibuka
                        </span>
                    @else
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 border border-gray-200">Segera</span>
                    @endif
                </div>

                @if($institution->short_name)
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-1">{{ $institution->short_name }}</span>
                @endif
                <h2 class="font-bold text-lg mb-2" style="color:var(--text)">{{ $institution->name }}</h2>
                @if($institution->description)
                    <p class="text-sm leading-relaxed mb-4" style="color:var(--muted)">{{ \Illuminate\Support\Str::limit($institution->description, 130) }}</p>
                @endif

                @if($open && $wave && $fmtDate($wave->end_date))
                    <div class="flex items-center gap-1.5 text-xs font-semibold mb-4" style="color:var(--muted)">
                        🗓️ Batas daftar: <span class="text-amber-600">{{ $fmtDate($wave->end_date) }}</span>
                    </div>
                @elseif($institution->usesExternalLink() || $institution->usesEmbed())
                    <div class="flex items-center gap-1.5 text-xs font-semibold mb-4" style="color:var(--muted)">
                        🔗 Pendaftaran online
                    </div>
                @endif

                <span class="mt-auto inline-flex items-center gap-2 text-sm font-bold text-amber-600">
                    Lihat PPDB {{ $institution->short_name ?? $institution->name }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </a>
            @if($institution->detail_url)
                @php $detailIsExternal = \Illuminate\Support\Str::startsWith($institution->detail_url, ['http://', 'https://']); @endphp
                <a href="{{ $institution->detail_url }}" @if($detailIsExternal) target="_blank" rel="noopener" @endif
                   class="flex items-center justify-center gap-1.5 text-xs font-bold px-4 py-3.5 border-t transition-colors hover:text-amber-600"
                   style="color:var(--muted); border-color:var(--border)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lihat Detail Jenjang
                </a>
            @endif
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
