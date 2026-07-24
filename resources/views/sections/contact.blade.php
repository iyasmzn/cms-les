@if($contactItems->isNotEmpty())
<section id="kontak-section" class="py-24 sm:py-32"
         style="background:linear-gradient(135deg,#0a0a0b 0%,#1a1a1e 50%,#0a0f1a 100%)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        @php
            $eyebrow  = setting('section_contact_eyebrow', 'Hubungi Kami');
            $subtitle = setting('section_contact_subtitle', 'Punya pertanyaan seputar SPMB, akademik, atau kegiatan sekolah? Jangan ragu untuk menghubungi kami.');
        @endphp
        <div class="text-center mb-16" data-aos="fade-up">
            @if($eyebrow)
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border text-xs font-bold uppercase tracking-widest mb-5"
                     style="background:rgba(217,119,6,.12);border-color:rgba(217,119,6,.3);color:var(--primary-300)">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse inline-block"></span>
                    {{ $eyebrow }}
                </div>
            @endif
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight tracking-tight mb-4">
                {{ setting('section_contact_title') ?: 'Kami Siap Membantu Anda' }}
            </h2>
            @if($subtitle)
                <p class="text-white/50 text-base max-w-xl mx-auto leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- Contact Cards Grid --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min($contactItems->count(), 3) }} xl:grid-cols-{{ min($contactItems->count(), 4) }}">
            @foreach($contactItems as $ci)
                <div class="group relative overflow-hidden rounded-3xl border bg-white/5 backdrop-blur-sm p-7 transition-all duration-300 hover:bg-white/10 hover:border-amber-400/30 hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1"
                     style="border-color:rgba(255,255,255,.08)"
                     data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">

                    {{-- Subtle glow on hover --}}
                    <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                         style="background:radial-gradient(ellipse at top left,rgba(217,119,6,.07),transparent 70%)"></div>

                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-5 transition-all duration-300 group-hover:scale-110 overflow-hidden"
                         style="background:rgba(217,119,6,.1);border:1px solid rgba(217,119,6,.2)">
                        @if($url = icon_url($ci->icon_image))
                            <img src="{{ $url }}" alt="{{ $ci->label }}" loading="lazy" class="w-8 h-8 object-contain">
                        @else
                            {{ $ci->icon }}
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="fi-label mb-1.5" style="color:rgba(217,119,6,.75);letter-spacing:.1em">{{ $ci->label }}</div>
                    <div class="text-white/80 text-sm font-medium leading-relaxed mb-5">{{ $ci->value }}</div>

                    {{-- Link --}}
                    @if($ci->link)
                        <a href="{{ $ci->link }}"
                           target="{{ str_starts_with($ci->link, 'http') ? '_blank' : '_self' }}"
                           rel="{{ str_starts_with($ci->link, 'http') ? 'noopener noreferrer' : '' }}"
                           class="inline-flex items-center gap-1.5 text-sm font-semibold transition-all group/link"
                           style="color:var(--primary-400)">
                            Buka
                            <svg class="w-3.5 h-3.5 transition-transform group-hover/link:translate-x-0.5"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Google Maps --}}
        @if(setting('contact_map_url') && str_starts_with(setting('contact_map_url'), 'https://www.google.com/maps/embed'))
        <div class="mt-14" data-aos="fade-up" data-aos-delay="150">
            <div class="relative overflow-hidden rounded-3xl border shadow-2xl shadow-black/40"
                 style="border-color:rgba(255,255,255,.08)">
                <div class="flex items-center gap-3 px-6 py-4 border-b"
                     style="background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08)">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg"
                         style="background:rgba(217,119,6,.12);border:1px solid rgba(217,119,6,.2)">📍</div>
                    <div>
                        <div class="text-white/90 text-sm font-semibold">Lokasi Sekolah</div>
                        @if(setting('contact_address'))
                            <div class="text-white/40 text-xs truncate max-w-sm">{{ setting('contact_address') }}</div>
                        @endif
                    </div>
                    <a href="{{ str_replace('/embed?', '/search?', setting('contact_map_url')) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="ms-auto inline-flex items-center gap-1.5 text-xs font-semibold transition-opacity hover:opacity-75"
                       style="color:var(--primary-400)">
                        Buka di Maps
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                <iframe
                    src="{{ setting('contact_map_url') }}"
                    width="100%"
                    height="400"
                    style="border:0;display:block;"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Lokasi {{ setting('site_name') }}"
                ></iframe>
            </div>
        </div>
        @endif

        {{-- Bottom CTA --}}
        <div class="mt-16 text-center" data-aos="fade-up" data-aos-delay="200">
            <p class="text-white/40 text-sm mb-6">Atau kunjungi langsung kantor kami pada hari dan jam operasional.</p>
            <div class="flex flex-wrap gap-4 justify-center">
                @if(setting('social_whatsapp'))
                    <a href="https://wa.me/{{ setting('social_whatsapp') }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl font-semibold text-base transition-all hover:opacity-90 shadow-lg shadow-green-500/20 hover:-translate-y-0.5"
                       style="background:#22c55e;color:#fff">
                        💬 Chat WhatsApp
                    </a>
                @endif
                @if(setting('contact_email'))
                    <a href="mailto:{{ setting('contact_email') }}"
                       class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl border font-semibold text-base text-white transition-all hover:bg-white/10 hover:-translate-y-0.5"
                       style="border-color:rgba(255,255,255,.2)">
                        ✉️ Kirim Email
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
