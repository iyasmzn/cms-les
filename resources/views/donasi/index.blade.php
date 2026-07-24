@extends('layouts.public')

@push('head')
<style>
    /* ── Hero ─────────────────────────────────── */
    .donasi-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #022c22 100%);
        position: relative;
        overflow: hidden;
    }
    .donasi-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 10% 50%, rgba(16,185,129,.2) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 90% 10%, rgba(52,211,153,.1) 0%, transparent 50%);
    }

    /* ── Form input ──────────────────────────── */
    .donasi-input {
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
    .donasi-input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
    .donasi-label { display: block; font-size: .8125rem; font-weight: 600; margin-bottom: .35rem; color: var(--text); }
    .donasi-required { color: #ef4444; margin-left: .15rem; }
    .donasi-hint { font-size: .7rem; color: var(--muted); margin-top: .25rem; }

    /* ── Nominal chips ────────────────────────── */
    .nominal-chip {
        padding: .55rem 1rem;
        border-radius: .625rem;
        border: 1.5px solid var(--border);
        background: var(--card);
        color: var(--text);
        font-size: .8125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }
    .nominal-chip:hover, .nominal-chip.active {
        border-color: #10b981;
        color: #10b981;
        background: #f0fdf4;
    }
    .nominal-chip.active { background: #10b981; color: #fff; }

    /* ── Payment method cards ─────────────────── */
    .method-card {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1rem;
        border-radius: .625rem;
        border: 1.5px solid var(--border);
        cursor: pointer;
        transition: all .15s;
    }
    .method-card:has(input:checked) { border-color: #10b981; background: #f0fdf4; }
    .method-card input[type="radio"] { accent-color: #10b981; }
    .method-card:hover { border-color: #34d399; }

    /* ── Info rekening ───────────────────────── */
    .rekening-card {
        border: 1.5px solid #d1fae5;
        background: #ecfdf5;
        border-radius: .75rem;
        padding: 1.25rem;
    }
    .rekening-num {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: .05em;
        color: #065f46;
    }
</style>
@endpush

@section('content')
@php
    $siteName = setting('site_name', config('app.name'));
    $bankName = setting('donasi_bank_name', 'Bank Syariah Indonesia (BSI)');
    $bankAccount = setting('donasi_bank_account', '7123456789');
    $bankHolder = setting('donasi_bank_holder', $siteName);
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="donasi-hero -mt-17 pt-32 pb-16 sm:pt-36 sm:pb-20">
    <x-hero-geo />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="flex flex-col lg:flex-row items-center gap-10">

            {{-- Left copy --}}
            <div class="flex-1 text-center lg:text-left" data-aos="fade-right">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 mb-5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest">Donasi Dibuka</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                    Bersama Kita<br>
                    <span class="text-emerald-400">Membangun Ilmu</span>
                </h1>
                <p class="text-white/70 text-sm sm:text-base leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8">
                    Donasi Anda membantu kami mewujudkan pendidikan berkualitas, menyediakan sarana belajar, dan mendukung program-program pesantren.
                </p>

                {{-- Keunggulan donasi --}}
                <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                    @foreach([['💡', 'Transparan', 'Laporan donasi terbuka'], ['🤲', 'Amanah', 'Dikelola secara syariah'], ['🏫', 'Berdampak', 'Langsung ke program pendidikan']] as [$ic, $title, $desc])
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl border border-white/15 backdrop-blur-sm">
                        <span class="text-base">{{ $ic }}</span>
                        <div>
                            <div class="text-[10px] text-white/50 font-medium uppercase tracking-wider">{{ $title }}</div>
                            <div class="text-sm font-bold text-emerald-300">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right — rekening info --}}
            <div class="shrink-0 w-full lg:w-80" data-aos="fade-left" data-aos-delay="100">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/15 p-6">
                    <div class="text-emerald-400 font-bold text-sm mb-4">💳 Info Rekening Donasi</div>
                    <div class="space-y-3">
                        <div class="rekening-card">
                            <p class="text-xs text-emerald-700 font-semibold mb-1">{{ $bankName }}</p>
                            <p class="rekening-num">{{ $bankAccount }}</p>
                            <p class="text-xs text-emerald-600 mt-1">a.n. {{ $bankHolder }}</p>
                        </div>
                        <p class="text-xs text-white/60 text-center">Atau isi formulir donasi di bawah untuk konfirmasi</p>
                    </div>
                    <a href="#form-donasi"
                       class="mt-4 flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-colors">
                        Konfirmasi Donasi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════ MAIN CONTENT ════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="mb-8 flex gap-3 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm" data-aos="fade-up">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-8 flex gap-3 p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm" data-aos="fade-up">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">

        {{-- ── LEFT: FORM ─────────────────────────────── --}}
        <div class="lg:col-span-2" id="form-donasi">
            <div class="mb-6" data-aos="fade-up">
                <h2 class="text-xl font-bold mb-1" style="color:var(--text)">Formulir Konfirmasi Donasi</h2>
                <p class="text-sm" style="color:var(--muted)">Isi formulir ini setelah Anda melakukan transfer, agar kami dapat segera mengkonfirmasi donasi Anda.</p>
            </div>

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

            <form action="{{ route('donasi.store') }}" method="POST" class="space-y-5" data-aos="fade-up"
                  x-data="{
                      amount: '{{ old('amount', '') }}',
                      setAmount(val) { this.amount = val; document.getElementById('amount').value = val; }
                  }">
                @csrf

                {{-- Data Donatur --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-extrabold">1</span>
                        Data Donatur
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="donasi-label" for="name">Nama Lengkap <span class="donasi-required">*</span></label>
                            <input type="text" id="name" name="name" class="donasi-input @error('name') border-red-400 @enderror"
                                   value="{{ old('name') }}" placeholder="Nama Anda" required>
                            @error('name')<p class="donasi-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="donasi-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="donasi-input @error('email') border-red-400 @enderror"
                                   value="{{ old('email') }}" placeholder="nama@email.com">
                            @error('email')<p class="donasi-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="donasi-label" for="phone">No. HP / WhatsApp</label>
                            <input type="tel" id="phone" name="phone" class="donasi-input @error('phone') border-red-400 @enderror"
                                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')<p class="donasi-hint text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" name="is_anonymous" value="1" class="w-4 h-4 rounded accent-emerald-600"
                                       {{ old('is_anonymous') ? 'checked' : '' }}>
                                <span class="text-sm font-medium" style="color:var(--text)">Sembunyikan nama saya (tampil sebagai "Hamba Allah")</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Nominal --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-extrabold">2</span>
                        Nominal Donasi <span class="donasi-required">*</span>
                    </h3>

                    {{-- Pilihan cepat --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach([50000, 100000, 150000, 200000, 500000, 1000000] as $nominal)
                        <button type="button"
                                @click="setAmount({{ $nominal }})"
                                :class="amount == {{ $nominal }} ? 'active' : ''"
                                class="nominal-chip">
                            Rp {{ number_format($nominal, 0, ',', '.') }}
                        </button>
                        @endforeach
                    </div>

                    <label class="donasi-label" for="amount">Atau masukkan nominal lain</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold" style="color:var(--muted)">Rp</span>
                        <input type="number" id="amount" name="amount"
                               class="donasi-input pl-10 @error('amount') border-red-400 @enderror"
                               x-model="amount"
                               value="{{ old('amount') }}"
                               placeholder="Contoh: 75000"
                               min="1000" required>
                    </div>
                    @error('amount')<p class="donasi-hint text-red-500">{{ $message }}</p>@enderror
                    <p class="donasi-hint">Minimal donasi Rp 1.000</p>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-extrabold">3</span>
                        Metode Pembayaran <span class="donasi-required">*</span>
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach([
                            ['transfer_bank', '🏦', 'Transfer Bank', 'BSI, BRI, BNI, Mandiri, dll.'],
                            ['qris', '📱', 'QRIS', 'Scan QR via aplikasi apapun'],
                            ['tunai', '💵', 'Tunai', 'Langsung ke kantor / panitia'],
                            ['lainnya', '🔄', 'Lainnya', 'Metode lain yang tidak tercantum'],
                        ] as [$val, $ico, $name, $desc])
                        <label class="method-card">
                            <input type="radio" name="payment_method" value="{{ $val }}"
                                   {{ old('payment_method', 'transfer_bank') === $val ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm" style="color:var(--text)">{{ $ico }} {{ $name }}</div>
                                <div class="text-xs mt-0.5" style="color:var(--muted)">{{ $desc }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="donasi-hint text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Pesan --}}
                <div class="fi-card p-6">
                    <h3 class="font-bold text-sm mb-5 flex items-center gap-2" style="color:var(--text)">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-extrabold">4</span>
                        Pesan / Doa <span class="text-xs font-normal" style="color:var(--muted)">(opsional)</span>
                    </h3>
                    <textarea id="message" name="message" rows="3"
                              class="donasi-input @error('message') border-red-400 @enderror"
                              placeholder="Tuliskan pesan, harapan, atau doa Anda...">{{ old('message') }}</textarea>
                    @error('message')<p class="donasi-hint text-red-500">{{ $message }}</p>@enderror
                </div>

                <x-spam-guard />

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-xl border border-emerald-200 bg-emerald-50" data-aos="fade-up">
                    <p class="text-xs text-emerald-800 max-w-sm">
                        Donasi Anda sangat berarti bagi kami. Jazakumullah khairan atas kepercayaan dan kontribusi Anda.
                    </p>
                    <button type="submit"
                            class="shrink-0 flex items-center gap-2 px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all">
                        🤲 Kirim Konfirmasi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- ── RIGHT: INFO SIDEBAR ─────────────────────── --}}
        <div class="space-y-5">

            {{-- Rekening --}}
            <div class="fi-card p-6" data-aos="fade-up">
                <h3 class="font-bold text-sm mb-4" style="color:var(--text)">💳 Rekening Donasi</h3>
                <div class="rekening-card mb-3">
                    <p class="text-xs text-emerald-700 font-semibold mb-1">{{ $bankName }}</p>
                    <p class="rekening-num">{{ $bankAccount }}</p>
                    <p class="text-xs text-emerald-600 mt-1">a.n. {{ $bankHolder }}</p>
                    <button onclick="navigator.clipboard.writeText('{{ $bankAccount }}')"
                            class="mt-2 text-xs text-emerald-700 font-semibold hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Salin Nomor Rekening
                    </button>
                </div>
                <p class="text-xs" style="color:var(--muted)">Setelah transfer, harap konfirmasi melalui formulir ini agar donasi Anda dapat segera kami catat.</p>
            </div>

            {{-- Manfaat Donasi --}}
            <div class="fi-card p-6" data-aos="fade-up" data-aos-delay="80">
                <h3 class="font-bold text-sm mb-4" style="color:var(--text)">🌱 Manfaat Donasi Anda</h3>
                <div class="space-y-3">
                    @foreach([
                        ['📚', 'Pengadaan buku & sarana belajar'],
                        ['🏫', 'Pemeliharaan fasilitas pesantren'],
                        ['🎓', 'Beasiswa santri berprestasi'],
                        ['💡', 'Program pendidikan & kegiatan'],
                    ] as [$ico, $label])
                    <div class="flex items-center gap-3">
                        <span class="text-xl w-8 text-center">{{ $ico }}</span>
                        <span class="text-sm" style="color:var(--muted)">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Kontak --}}
            @if(setting('contact_whatsapp') || setting('social_whatsapp'))
            <div class="fi-card p-6" data-aos="fade-up" data-aos-delay="120">
                <h3 class="font-bold text-sm mb-3" style="color:var(--text)">💬 Ada Pertanyaan?</h3>
                <p class="text-xs mb-4" style="color:var(--muted)">Hubungi kami via WhatsApp jika ada pertanyaan seputar donasi.</p>
                <a href="https://wa.me/{{ setting('contact_whatsapp', setting('social_whatsapp')) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm transition-colors">
                    💬 Hubungi via WhatsApp
                </a>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
