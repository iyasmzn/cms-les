@extends('layouts.public')

@push('head')
<style>
    /* ── Hero ─────────────────────────────────── */
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

    /* ── Tab pill nav ─────────────────────────── */
    .tab-nav { display: flex; gap: .5rem; flex-wrap: wrap; }
    .tab-pill {
        padding: .55rem 1.25rem;
        border-radius: 9999px;
        font-size: .8125rem;
        font-weight: 700;
        border: 1.5px solid var(--border);
        background: var(--card);
        color: var(--muted);
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }
    .tab-pill.active,
    .tab-pill:hover { border-color: #d97706; color: #d97706; background: #fffbeb; }
    .tab-pill.active { background: #d97706; color: #fff; border-color: #d97706; }

    /* ── Step cards ──────────────────────────── */
    .step-card { position: relative; }
    .step-num {
        position: absolute;
        top: -.75rem;
        left: 1.25rem;
        width: 2rem; height: 2rem;
        border-radius: 9999px;
        background: #d97706;
        color: #fff;
        font-size: .75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(217,119,6,.4);
    }

    /* ── Fee table ───────────────────────────── */
    .fee-row:nth-child(even) { background: rgba(0,0,0,.025); }

    /* ── Form ────────────────────────────────── */
    .ppdb-input {
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
    .ppdb-input:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,.12); }
    .ppdb-label { display: block; font-size: .8125rem; font-weight: 600; margin-bottom: .35rem; color: var(--text); }
    .ppdb-required { color: #ef4444; margin-left: .15rem; }
    .ppdb-hint { font-size: .7rem; color: var(--muted); margin-top: .25rem; }

    /* Jalur radio cards */
    .jalur-card { display: flex; align-items: flex-start; gap: .75rem; padding: .85rem 1rem; border-radius: .625rem; border: 1.5px solid var(--border); cursor: pointer; transition: all .15s; }
    .jalur-card:has(input:checked) { border-color: #d97706; background: #fffbeb; }
    .jalur-card input[type="radio"] { margin-top: .15rem; accent-color: #d97706; }
    .jalur-card:hover { border-color: #fbbf24; }
</style>
@endpush

@section('content')
@php
    $formEnabled   = (bool) setting('spmb_form_enabled', true);
    $spmbYear      = spmb_year_label();
    $otherJenjang  = \App\Models\Institution::query()->active()->whereKeyNot($institution->id)->exists();
    $fmtDate       = fn ($d) => $d ? $d->locale('id')->translatedFormat('d M Y') : '—';
    $siteName      = setting('site_name', config('app.name'));
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="ppdb-hero -mt-17 pt-32 pb-16 sm:pt-36 sm:pb-20">
    <x-hero-geo />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="flex flex-col lg:flex-row items-center gap-10">

            {{-- Left copy --}}
            <div class="flex-1 text-center lg:text-left" data-aos="fade-right">
                @if($otherJenjang)
                <div class="mb-4">
                    <a href="{{ route('ppdb.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/60 hover:text-amber-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Pilih jenjang lain
                    </a>
                </div>
                @endif
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-5">
                    @if($spmbOpen)
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">SPMB Dibuka{{ $scheduleWave ? ' — '.$scheduleWave->name : '' }}</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        <span class="text-xs font-bold text-red-300 uppercase tracking-widest">SPMB Ditutup</span>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                    PPDB {{ $institution->short_name ?? $institution->name }}<br>
                    <span class="text-amber-400">{{ $spmbYear }}</span>
                </h1>
                <p class="text-white/70 text-sm sm:text-base leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8">
                    Penerimaan Peserta Didik Baru <span class="text-white font-semibold">{{ $institution->name }}</span> — {{ $siteName }}.@if($institution->usesInternalForm() && $paths->isNotEmpty()) Daftarkan diri melalui jalur {{ $paths->pluck('name')->join(', ', ', dan ') }}.@endif
                </p>

                {{-- Timeline chips — diambil dari gelombang pendaftaran --}}
                @if($scheduleWave)
                <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                    @foreach([['📅', 'Batas Daftar', $scheduleWave->end_date], ['🔍', 'Seleksi', $scheduleWave->selection_date], ['🎉', 'Pengumuman', $scheduleWave->announcement_date]] as [$ic, $label, $val])
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl border border-white/15 backdrop-blur-sm">
                        <span class="text-base">{{ $ic }}</span>
                        <div>
                            <div class="text-[10px] text-white/50 font-medium uppercase tracking-wider">{{ $label }}</div>
                            <div class="text-sm font-bold text-amber-300">{{ $fmtDate($val) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Right — panel pendaftaran (mode-aware) --}}
            <div class="shrink-0 w-full lg:w-80" data-aos="fade-left" data-aos-delay="100">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/15 p-6">
                    @if($institution->usesInternalForm())
                        <div class="fi-label text-amber-400 mb-4">Jalur Pendaftaran</div>
                        <div class="space-y-2.5">
                            @foreach($paths as $path)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/8 border border-white/10">
                                @if($url = icon_url($path->icon_image))
                                    <img src="{{ $url }}" alt="{{ $path->name }}" loading="lazy" class="w-6 h-6 object-contain shrink-0">
                                @else
                                    <span class="text-xl">{{ $path->icon }}</span>
                                @endif
                                <span class="text-white font-semibold text-sm">{{ $path->name }}</span>
                            </div>
                            @endforeach
                        </div>
                        @if($spmbOpen && $formEnabled)
                        <a href="#form-pendaftaran" x-data
                           @click="$dispatch('open-form')"
                           class="mt-5 flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-sm transition-colors">
                            Daftar Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        @endif
                    @elseif($institution->usesExternalLink())
                        <div class="fi-label text-amber-400 mb-3">Pendaftaran Online</div>
                        <p class="text-white/70 text-sm leading-relaxed mb-5">Pendaftaran {{ $institution->short_name ?? $institution->name }} dilakukan melalui portal resmi di situs terpisah.</p>
                        @if($spmbOpen && $formEnabled)
                        <a href="{{ $institution->external_url }}" target="_blank" rel="noopener"
                           class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-sm transition-colors">
                            Buka Portal Pendaftaran
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        @endif
                    @else
                        <div class="fi-label text-amber-400 mb-3">Formulir Pendaftaran</div>
                        <p class="text-white/70 text-sm leading-relaxed mb-5">Isi formulir pendaftaran {{ $institution->short_name ?? $institution->name }} langsung di halaman ini.</p>
                        @if($spmbOpen && $formEnabled)
                        <a href="#form-pendaftaran" x-data
                           @click="$dispatch('open-form')"
                           class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-sm transition-colors">
                            Isi Formulir
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </a>
                        @endif
                    @endif

                    @if($institution->detail_url)
                        @php $detailIsExternal = \Illuminate\Support\Str::startsWith($institution->detail_url, ['http://', 'https://']); @endphp
                        <a href="{{ $institution->detail_url }}" @if($detailIsExternal) target="_blank" rel="noopener" @endif
                           class="mt-3 flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white font-semibold text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Lihat Detail Jenjang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ MAIN CONTENT ════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ tab: 'prosedur' }" @open-form.window="tab = 'form'">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="mb-6 flex gap-3 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm" data-aos="fade-up">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 flex gap-3 p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm" data-aos="fade-up">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Tab nav --}}
    <div class="tab-nav mb-8" data-aos="fade-up">
        <button @click="tab = 'prosedur'" :class="tab === 'prosedur' ? 'active' : ''" class="tab-pill">
            📋 Prosedur Pendaftaran
        </button>
        @if(!empty($fees))
        <button @click="tab = 'biaya'" :class="tab === 'biaya' ? 'active' : ''" class="tab-pill">
            💰 Biaya Pendaftaran
        </button>
        @endif
        <button @click="tab = 'form'" :class="tab === 'form' ? 'active' : ''" class="tab-pill" id="form-pendaftaran">
            📝 Form Pendaftaran
        </button>
    </div>

    {{-- ──────── TAB: PROSEDUR ──────── --}}
    <div x-show="tab === 'prosedur'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="mb-6" data-aos="fade-up">
            <h2 class="text-xl font-bold mb-1" style="color:var(--text)">Prosedur Pendaftaran</h2>
            <p class="text-sm" style="color:var(--muted)">Ikuti langkah-langkah berikut untuk mendaftarkan diri sebagai calon peserta didik baru {{ $spmbYear }}.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-{{ min(count($procedures), 4) }} gap-6 mb-10">
            @foreach($procedures as $index => $step)
            <div class="fi-card fi-card-hover p-6 pt-8 step-card" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                <div class="step-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                @if($url = icon_url($step['icon_image'] ?? null))
                    <img src="{{ $url }}" alt="{{ $step['title'] ?? '' }}" loading="lazy" class="w-8 h-8 mb-3 object-contain">
                @else
                    <div class="text-2xl mb-3">{{ $step['icon'] ?? '📌' }}</div>
                @endif
                <div class="font-bold text-sm mb-2" style="color:var(--text)">{{ $step['title'] ?? '' }}</div>
                <p class="text-xs leading-relaxed" style="color:var(--muted)">{{ $step['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>

        {{-- Jadwal Gelombang Pendaftaran --}}
        @if($waves->isNotEmpty())
        <div class="fi-card p-6 sm:p-8 mb-10" data-aos="fade-up">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-5">
                <h3 class="font-bold text-base" style="color:var(--text)">🗓️ Jadwal Gelombang Pendaftaran</h3>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Tahun Ajaran {{ $spmbYear }}</span>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-{{ min($waves->count(), 3) }}">
                @foreach($waves as $w)
                @php
                    if ($w->isOpen()) {
                        [$badgeText, $badgeClass, $dot] = ['Dibuka', 'bg-green-50 text-green-700 border-green-200', 'bg-green-500 animate-pulse'];
                    } elseif ($w->start_date->isFuture()) {
                        [$badgeText, $badgeClass, $dot] = ['Akan Datang', 'bg-blue-50 text-blue-700 border-blue-200', 'bg-blue-500'];
                    } else {
                        [$badgeText, $badgeClass, $dot] = ['Selesai', 'bg-gray-100 text-gray-500 border-gray-200', 'bg-gray-400'];
                    }
                @endphp
                <div class="rounded-xl border p-4" style="border-color:var(--border); background:var(--bg)">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-bold text-sm" style="color:var(--text)">{{ $w->name }}</span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-full border {{ $badgeClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>{{ $badgeText }}
                        </span>
                    </div>
                    <dl class="space-y-1.5 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="shrink-0">📅</span>
                            <span style="color:var(--muted)">Pendaftaran:</span>
                            <span class="font-semibold ml-auto text-right" style="color:var(--text)">{{ $fmtDate($w->start_date) }} – {{ $fmtDate($w->end_date) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="shrink-0">🔍</span>
                            <span style="color:var(--muted)">Seleksi:</span>
                            <span class="font-semibold ml-auto" style="color:var(--text)">{{ $fmtDate($w->selection_date) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="shrink-0">🎉</span>
                            <span style="color:var(--muted)">Pengumuman:</span>
                            <span class="font-semibold ml-auto" style="color:var(--text)">{{ $fmtDate($w->announcement_date) }}</span>
                        </div>
                    </dl>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Syarat Pendaftaran --}}
        <div class="fi-card p-6 sm:p-8" data-aos="fade-up">
            <h3 class="font-bold text-base mb-5" style="color:var(--text)">📎 Persyaratan Dokumen</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach([
                    'Ijazah / Surat Keterangan Lulus (SKL) asli dan fotokopi',
                    'Rapor kelas 7, 8, dan 9 yang telah dilegalisasi',
                    'Akta Kelahiran asli dan fotokopi',
                    'Kartu Keluarga asli dan fotokopi',
                    'Pas foto terbaru ukuran 3×4 (5 lembar, latar merah)',
                    'Surat keterangan domisili (untuk jalur Zonasi)',
                    'Piagam / sertifikat prestasi (untuk jalur Prestasi)',
                    'Surat keterangan tidak mampu (untuk jalur Afirmasi)',
                ] as $doc)
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm" style="color:var(--muted)">{{ $doc }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ──────── TAB: BIAYA ──────── --}}
    @if(!empty($fees))
    <div x-show="tab === 'biaya'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="mb-6" data-aos="fade-up">
            <h2 class="text-xl font-bold mb-1" style="color:var(--text)">Biaya Pendaftaran</h2>
            <p class="text-sm" style="color:var(--muted)">Rincian biaya yang perlu disiapkan dalam proses pendaftaran SPMB {{ $spmbYear }}.</p>
        </div>

        <div class="fi-card overflow-hidden" data-aos="fade-up">
            <div class="amber-bar"></div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color:var(--border); background:var(--bg)">
                        <th class="px-6 py-3.5 text-left font-semibold text-xs uppercase tracking-wider" style="color:var(--muted)">Komponen Biaya</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-xs uppercase tracking-wider" style="color:var(--muted)">Jumlah</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-xs uppercase tracking-wider hidden sm:table-cell" style="color:var(--muted)">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fees as $fee)
                    <tr class="fee-row border-b" style="border-color:var(--border)">
                        <td class="px-6 py-4 font-medium" style="color:var(--text)">{{ $fee['category'] ?? '' }}</td>
                        <td class="px-6 py-4 font-bold text-amber-600">{{ $fee['amount'] ?? '' }}</td>
                        <td class="px-6 py-4 hidden sm:table-cell" style="color:var(--muted)">{{ $fee['note'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex gap-2 p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-xs" data-aos="fade-up">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p>Biaya di atas dapat berubah sewaktu-waktu. Hubungi panitia SPMB untuk informasi terkini.</p>
        </div>
    </div>
    @endif

    {{-- ──────── TAB: FORM ──────── --}}
    <div x-show="tab === 'form'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        @if(!$spmbOpen || !$formEnabled)
        {{-- Closed state --}}
        <div class="fi-card p-10 text-center" data-aos="fade-up">
            <div class="text-5xl mb-4">🔒</div>
            <h3 class="font-bold text-lg mb-2" style="color:var(--text)">Form Pendaftaran Ditutup</h3>
            <p class="text-sm max-w-md mx-auto" style="color:var(--muted)">{{ $closedMessage }}</p>
            @if(setting('contact_whatsapp') || setting('social_whatsapp'))
            <a href="https://wa.me/{{ setting('contact_whatsapp', setting('social_whatsapp')) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 mt-6 btn-primary">
                💬 Hubungi via WhatsApp
            </a>
            @endif
        </div>
        @elseif($institution->usesExternalLink())
        {{-- Pendaftaran via tautan eksternal — tidak menyimpan data di sistem ini --}}
        <div class="fi-card p-10 text-center max-w-2xl mx-auto" data-aos="fade-up">
            <div class="text-5xl mb-4">🔗</div>
            <h3 class="font-bold text-lg mb-2" style="color:var(--text)">Pendaftaran {{ $institution->name }}</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color:var(--muted)">Pendaftaran jenjang ini dilakukan melalui portal resmi di situs terpisah. Klik tombol di bawah untuk melanjutkan.</p>
            <a href="{{ $institution->external_url }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all">
                Buka Portal Pendaftaran
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
        @elseif($institution->usesEmbed())
        {{-- Formulir eksternal disematkan — tidak menyimpan data di sistem ini --}}
        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <div class="mb-6">
                <h2 class="text-xl font-bold mb-1" style="color:var(--text)">{{ $formTitle }}</h2>
                <p class="text-sm" style="color:var(--muted)">{{ $formDesc }}</p>
            </div>
            <div class="fi-card overflow-hidden">
                <div class="amber-bar"></div>
                <iframe src="{{ $institution->embed_url }}" title="Formulir Pendaftaran {{ $institution->name }}"
                        class="w-full" style="height: 80vh; min-height: 600px; border: 0;"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allow="fullscreen"></iframe>
            </div>
            <p class="text-xs text-center mt-3" style="color:var(--muted)">
                Formulir ini dikelola oleh pihak eksternal. Jika tidak tampil,
                <a href="{{ $institution->embed_url }}" target="_blank" rel="noopener" class="text-amber-600 font-semibold underline">buka di tab baru</a>.
            </p>
        </div>
        @else
        {{-- Open form --}}
        <div class="max-w-3xl mx-auto">
            <div class="mb-6" data-aos="fade-up">
                <h2 class="text-xl font-bold mb-1" style="color:var(--text)">{{ $formTitle }}</h2>
                <p class="text-sm" style="color:var(--muted)">{{ $formDesc }}</p>
            </div>

            {{-- Info gelombang aktif tempat pendaftaran ini akan tercatat --}}
            @if($scheduleWave)
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-xl border border-green-200 bg-green-50" data-aos="fade-up">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                    <span class="text-sm font-bold text-green-800">Pendaftaran {{ $scheduleWave->name }}</span>
                </div>
                <div class="text-xs text-green-700 sm:ml-auto">
                    Tahun Ajaran {{ $spmbYear }} · Dibuka {{ $fmtDate($scheduleWave->start_date) }} – {{ $fmtDate($scheduleWave->end_date) }}
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50" data-aos="fade-up">
                <p class="font-semibold text-red-700 text-sm mb-2">Harap perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-xs text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('ppdb.store', $institution) }}" method="POST" enctype="multipart/form-data" class="space-y-6" data-aos="fade-up">
                @csrf

                @if($fields->isNotEmpty())
                {{-- ══ Formulir dinamis (field diatur admin per jenjang) ══ --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">1</span>
                        Data Calon Peserta
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($fields as $field)
                            @include('ppdb.partials.field', ['field' => $field])
                        @endforeach
                    </div>
                </div>

                @if($paths->isNotEmpty())
                {{-- Jalur --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">2</span>
                        Jalur Pendaftaran <span class="ppdb-required">*</span>
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($paths as $path)
                        <label class="jalur-card">
                            <input type="radio" name="admission_path_id" value="{{ $path->id }}" {{ (int) old('admission_path_id', $paths->first()?->id) === $path->id ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm flex items-center gap-1.5" style="color:var(--text)">
                                    @if($url = icon_url($path->icon_image))
                                        <img src="{{ $url }}" alt="" loading="lazy" class="w-5 h-5 object-contain shrink-0">
                                    @else
                                        <span>{{ $path->icon }}</span>
                                    @endif
                                    <span>{{ $path->name }}</span>
                                </div>
                                <div class="text-xs mt-0.5" style="color:var(--muted)">{{ $path->description }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('admission_path_id')<p class="ppdb-hint text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>
                @endif
                @else
                {{-- Data Pribadi --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">1</span>
                        Data Pribadi Calon Peserta
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="ppdb-label" for="full_name">Nama Lengkap <span class="ppdb-required">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="ppdb-input @error('full_name') border-red-400 @enderror"
                                   value="{{ old('full_name') }}" placeholder="Sesuai akta kelahiran" required>
                            @error('full_name')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="ppdb-label" for="nik">NIK <span class="ppdb-required">*</span></label>
                            <input type="text" inputmode="numeric" id="nik" name="nik" maxlength="16" class="ppdb-input @error('nik') border-red-400 @enderror"
                                   value="{{ old('nik') }}" placeholder="16 digit Nomor Induk Kependudukan" required>
                            @error('nik')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="phone">No. HP / WhatsApp <span class="ppdb-required">*</span></label>
                            <input type="tel" id="phone" name="phone" class="ppdb-input @error('phone') border-red-400 @enderror"
                                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                            @error('phone')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="ppdb-input @error('email') border-red-400 @enderror"
                                   value="{{ old('email') }}" placeholder="nama@email.com">
                            @error('email')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="birth_place">Tempat Lahir</label>
                            <input type="text" id="birth_place" name="birth_place" class="ppdb-input @error('birth_place') border-red-400 @enderror"
                                   value="{{ old('birth_place') }}" placeholder="Kota kelahiran">
                            @error('birth_place')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="birth_date">Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date" class="ppdb-input @error('birth_date') border-red-400 @enderror"
                                   value="{{ old('birth_date') }}">
                            @error('birth_date')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="ppdb-label" for="address">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="2" class="ppdb-input @error('address') border-red-400 @enderror"
                                      placeholder="Jl. ..., RT/RW ..., Kel./Desa ..., Kec. ..., Kab./Kota ...">{{ old('address') }}</textarea>
                            @error('address')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Jalur --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">2</span>
                        Jalur Pendaftaran <span class="ppdb-required">*</span>
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($paths as $path)
                        <label class="jalur-card">
                            <input type="radio" name="admission_path_id" value="{{ $path->id }}" {{ (int) old('admission_path_id', $paths->first()?->id) === $path->id ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm flex items-center gap-1.5" style="color:var(--text)">
                                    @if($url = icon_url($path->icon_image))
                                        <img src="{{ $url }}" alt="" loading="lazy" class="w-5 h-5 object-contain shrink-0">
                                    @else
                                        <span>{{ $path->icon }}</span>
                                    @endif
                                    <span>{{ $path->name }}</span>
                                </div>
                                <div class="text-xs mt-0.5" style="color:var(--muted)">{{ $path->description }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('admission_path_id')<p class="ppdb-hint text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Asal Sekolah --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">3</span>
                        Asal Sekolah
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ppdb-label" for="previous_school">Nama Sekolah Asal <span class="ppdb-required">*</span></label>
                            <input type="text" id="previous_school" name="previous_school" class="ppdb-input @error('previous_school') border-red-400 @enderror"
                                   value="{{ old('previous_school') }}" placeholder="SMP Negeri / Swasta ..." required>
                            @error('previous_school')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="previous_school_city">Kota / Kabupaten</label>
                            <input type="text" id="previous_school_city" name="previous_school_city" class="ppdb-input @error('previous_school_city') border-red-400 @enderror"
                                   value="{{ old('previous_school_city') }}" placeholder="Kota ...">
                            @error('previous_school_city')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Orang Tua --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">4</span>
                        Data Orang Tua / Wali
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ppdb-label" for="parent_name">Nama Orang Tua / Wali</label>
                            <input type="text" id="parent_name" name="parent_name" class="ppdb-input @error('parent_name') border-red-400 @enderror"
                                   value="{{ old('parent_name') }}" placeholder="Nama lengkap orang tua / wali">
                            @error('parent_name')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="ppdb-label" for="parent_phone">No. HP Orang Tua / Wali</label>
                            <input type="tel" id="parent_phone" name="parent_phone" class="ppdb-input @error('parent_phone') border-red-400 @enderror"
                                   value="{{ old('parent_phone') }}" placeholder="08xxxxxxxxxx">
                            @error('parent_phone')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-extrabold">5</span>
                        Catatan Tambahan
                    </h3>
                    <textarea id="notes" name="notes" rows="3" class="ppdb-input @error('notes') border-red-400 @enderror"
                              placeholder="Informasi tambahan yang perlu panitia ketahui (opsional)...">{{ old('notes') }}</textarea>
                    @error('notes')<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
                </div>

                @endif

                <x-spam-guard />

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-xl border border-amber-200 bg-amber-50" data-aos="fade-up">
                    <p class="text-xs text-amber-800 max-w-sm">
                        Dengan mengirim formulir ini, Anda menyatakan bahwa data yang diisi adalah benar dan dapat dipertanggungjawabkan.
                    </p>
                    <button type="submit"
                            class="shrink-0 flex items-center gap-2 px-7 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all">
                        Kirim Pendaftaran
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

</div>

{{-- Auto-switch to form tab if redirected with success or errors --}}
@if(session('success') || $errors->any() || request()->has('tab'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.querySelector('[x-data]');
        if (el && el._x_dataStack) {
            el._x_dataStack[0].tab = '{{ session('success') || $errors->any() ? 'form' : request('tab', 'prosedur') }}';
        }
    });
</script>
@endif
@endsection
