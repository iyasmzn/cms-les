@php
    $institutions = \App\Models\Institution::query()->active()->ordered()->get();
    $openInstitutions = $institutions->filter(fn (\App\Models\Institution $i): bool => $i->registrationOpen());
@endphp

{{-- Section tampil saat minimal satu jenjang sedang membuka pendaftaran --}}
@if($openInstitutions->isNotEmpty())
@php
    $spmbYear       = spmb_year_label();
    $cardTitle      = str_replace('{year}', $spmbYear, setting('spmb_card_title', 'SPMB Tahun Ajaran {year} Dibuka!'));
    $cardDesc       = setting('spmb_card_description', 'Pendaftaran peserta didik baru resmi dibuka. Pilih jenjang, lengkapi berkas, dan daftarkan diri Anda sebelum batas waktu.');
    $ctaLabel       = setting('spmb_card_cta_label', 'Daftar Sekarang');
    $ctaUrl         = setting('spmb_card_cta_url', '/ppdb');
    $secondaryLabel = setting('spmb_card_secondary_label', 'Info Selengkapnya');
@endphp

<section id="spmb" class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl overflow-hidden shadow-2xl border"
             style="background:linear-gradient(135deg,var(--primary-50) 0%,var(--primary-100) 50%,var(--primary-200) 100%);border-color:var(--primary-200);box-shadow:0 20px 60px color-mix(in oklab,var(--primary) 15%,transparent)"
             data-aos="fade-up">
            <div class="grid lg:grid-cols-2">

                {{-- Left: copy --}}
                <div class="p-10 lg:p-14" data-aos="fade-right" data-aos-delay="80">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-5"
                         style="border:1px solid var(--primary-300);background:color-mix(in oklab,var(--primary-100) 60%,transparent);color:var(--primary-800)">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse inline-block" style="background:var(--primary)"></span>
                        Penerimaan Peserta Didik Baru
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold leading-tight tracking-tight mb-4" style="color:var(--primary-900)">
                        {!! nl2br(e($cardTitle)) !!}
                    </h2>

                    {{-- Ringkasan jenjang yang membuka pendaftaran --}}
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl mb-5"
                         style="border:1px solid var(--primary-300); background:color-mix(in oklab,var(--primary-100) 60%,transparent)">
                        <span class="text-lg">🗓️</span>
                        <div class="leading-tight">
                            <div class="text-sm font-bold" style="color:var(--primary-900)">
                                {{ $openInstitutions->count() }} jenjang membuka pendaftaran
                            </div>
                            <div class="text-[11px] font-medium" style="color:var(--primary-700)">Tahun Ajaran {{ $spmbYear }}</div>
                        </div>
                    </div>
                    <p class="text-base leading-relaxed mb-8" style="color:var(--primary-800);opacity:.85">
                        {{ $cardDesc }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $ctaUrl }}" class="btn-primary text-base">
                            {{ $ctaLabel }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('ppdb.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border-2 font-semibold bg-transparent transition-all text-base"
                           style="border-color:var(--primary-300);color:var(--primary-900)"
                           onmouseover="this.style.background='var(--primary-100)'"
                           onmouseout="this.style.background='transparent'">
                            {{ $secondaryLabel }}
                        </a>
                    </div>
                </div>

                {{-- Right: kartu pilihan jenjang --}}
                <div class="p-8 lg:p-12 flex flex-col justify-center"
                     style="background:color-mix(in oklab,var(--primary-400) 15%,transparent)"
                     data-aos="fade-left" data-aos-delay="160">
                    <div class="text-xs font-bold uppercase tracking-widest mb-4" style="color:var(--primary-800)">Pilih Jenjang</div>
                    <div class="space-y-3">
                        @foreach($institutions as $institution)
                        @php $open = $institution->registrationOpen(); @endphp
                        <a href="{{ route('ppdb.show', $institution) }}"
                           class="group flex items-center gap-3.5 p-3.5 rounded-2xl bg-white/70 hover:bg-white transition-colors border shadow-sm"
                           style="border-color:var(--primary-200)">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:var(--primary-100)">
                                @if($url = icon_url($institution->icon_image))
                                    <img src="{{ $url }}" alt="{{ $institution->name }}" loading="lazy" class="w-6 h-6 object-contain">
                                @else
                                    <span class="text-2xl">{{ $institution->icon ?: '🏫' }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-sm" style="color:var(--primary-900)">{{ $institution->short_name ?: $institution->name }}</div>
                                <div class="text-[11px] truncate" style="color:var(--primary-700)">{{ $institution->name }}</div>
                            </div>
                            @if($open)
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-100 text-green-700 shrink-0">Dibuka</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-gray-100 text-gray-500 shrink-0">Segera</span>
                            @endif
                            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:translate-x-0.5" style="color:var(--primary-500)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endif
