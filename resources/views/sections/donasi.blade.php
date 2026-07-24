<section id="donasi" class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl overflow-hidden shadow-2xl"
             style="background:linear-gradient(135deg,#064e3b 0%,#065f46 55%,#022c22 100%);border:1px solid rgba(52,211,153,.15);box-shadow:0 20px 60px rgba(6,78,59,.35)"
             data-aos="fade-up">

            <div class="grid lg:grid-cols-5">

                {{-- Left: copy ─────────────────────────────── --}}
                <div class="lg:col-span-3 p-10 lg:p-14" data-aos="fade-right" data-aos-delay="80">

                    @php
                        $eyebrow   = setting('section_donasi_eyebrow', 'Program Donasi');
                        $highlight = setting('section_donasi_title_highlight', 'Pendidikan Berkualitas');
                        $subtitle  = setting('section_donasi_subtitle', 'Setiap kontribusi Anda sangat berarti bagi perkembangan pendidikan santri. Donasi Anda akan digunakan untuk pengadaan sarana belajar, beasiswa, dan program-program pesantren.');
                    @endphp

                    @if($eyebrow)
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                             style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3);color:#6ee7b7">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                            {{ $eyebrow }}
                        </div>
                    @endif

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        {{ setting('section_donasi_title') ?: 'Bersama Wujudkan' }}@if($highlight)<br>
                        <span style="color:#6ee7b7">{{ $highlight }}</span>@endif
                    </h2>

                    @if($subtitle)
                        <p class="text-base leading-relaxed mb-8" style="color:rgba(255,255,255,.7)">
                            {{ $subtitle }}
                        </p>
                    @endif

                    {{-- Impact stats --}}
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        @foreach([
                            ['🤲', 'Amanah', 'Dikelola secara syariah & transparan'],
                            ['📚', 'Berdampak', 'Langsung ke program pendidikan'],
                            ['💡', 'Mudah', 'Bisa transfer bank, QRIS, atau tunai'],
                        ] as [$ico, $title, $desc])
                        <div class="flex flex-col gap-1.5"
                             style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:.875rem;padding:.875rem">
                            <span class="text-xl">{{ $ico }}</span>
                            <div class="text-xs font-extrabold text-emerald-300">{{ $title }}</div>
                            <div class="text-[11px] leading-snug" style="color:rgba(255,255,255,.5)">{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('donasi.index') }}"
                           class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl font-bold text-sm text-white transition-all hover:opacity-90 hover:-translate-y-0.5"
                           style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 18px rgba(16,185,129,.45)">
                            🤲 Donasi Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ route('donasi.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl font-semibold text-sm transition-all"
                           style="border:1.5px solid rgba(52,211,153,.35);color:rgba(255,255,255,.8)"
                           onmouseover="this.style.background='rgba(52,211,153,.1)';this.style.borderColor='rgba(52,211,153,.6)'"
                           onmouseout="this.style.background='';this.style.borderColor='rgba(52,211,153,.35)'">
                            Lihat Info Rekening
                        </a>
                    </div>
                </div>

                {{-- Right: visual panel ──────────────────── --}}
                <div class="hidden lg:flex lg:col-span-2 flex-col items-center justify-center px-8 py-14 relative overflow-hidden"
                     style="background:rgba(255,255,255,.04);border-left:1px solid rgba(255,255,255,.06)"
                     data-aos="fade-left" data-aos-delay="160">

                    {{-- Background decoration --}}
                    <div class="absolute inset-0" style="background:radial-gradient(circle at 50% 50%,rgba(52,211,153,.08) 0%,transparent 70%)"></div>

                    <div class="relative text-center">
                        <div class="text-7xl mb-6">💝</div>

                        {{-- Rekening info card --}}
                        <div class="rounded-2xl p-5 mb-5 text-left"
                             style="background:rgba(255,255,255,.08);border:1px solid rgba(52,211,153,.2);backdrop-filter:blur(8px)">
                            <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#6ee7b7">
                                Rekening Donasi
                            </p>
                            <p class="text-sm font-medium mb-1" style="color:rgba(255,255,255,.7)">
                                {{ setting('donasi_bank_name', 'Bank Syariah Indonesia (BSI)') }}
                            </p>
                            <p class="text-xl font-extrabold tracking-widest text-white">
                                {{ setting('donasi_bank_account', '7123456789') }}
                            </p>
                            <p class="text-xs mt-1" style="color:rgba(255,255,255,.5)">
                                a.n. {{ setting('donasi_bank_holder', setting('site_name', config('app.name'))) }}
                            </p>
                        </div>

                        <a href="{{ route('donasi.index') }}"
                           class="text-sm font-bold transition-opacity hover:opacity-70 flex items-center justify-center gap-1.5"
                           style="color:#6ee7b7">
                            Konfirmasi donasi Anda
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
