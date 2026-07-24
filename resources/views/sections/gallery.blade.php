@php
    $galleryMedia ??= collect();

    $lightboxItems = $galleryMedia->map(fn ($m) => [
        'type' => $m->is_embed ? 'video' : 'image',
        'name' => $m->name,
        'src'  => $m->is_embed ? null : $m->url,
        'html' => $m->is_embed ? (string) $m->embed_html : null,
        'provider' => $m->is_embed ? $m->embed_provider : null,
        'embedSrc' => $m->is_embed ? \App\Services\EmbedVideo::embedSrc($m->embed_provider, $m->embed_url ?? '') : null,
        'vertical' => in_array($m->embed_provider, ['tiktok', 'instagram'], true),
        'ratio' => $m->is_embed ? round(\App\Services\EmbedVideo::aspectRatio($m->embed_provider), 4) : null,
    ])->values();
@endphp

<style>
    [x-cloak] { display: none !important; }

    .glr-card {
        transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    }
    .glr-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 50px rgba(0,0,0,.16);
        border-color: var(--primary);
    }
    .glr-card:hover .glr-card-img { transform: scale(1.07); }
    .glr-card-img { transition: transform .7s ease; }

    /* Caption fades & slides up on hover */
    .glr-cap {
        transition: transform .35s ease, opacity .35s ease;
        transform: translateY(10px);
        opacity: 0;
    }
    .glr-card:hover .glr-cap { transform: translateY(0); opacity: 1; }
    .glr-card:hover .glr-overlay { opacity: 1; }
    @media (hover: none) {
        .glr-cap { transform: none; opacity: 1; }
        .glr-overlay { opacity: 1; }
    }
</style>

<section id="galeri" class="py-20 sm:py-28" style="background:var(--bg)"
         x-data="{
            open: false,
            active: null,
            items: @js($lightboxItems),
            show(i) { this.active = i; this.open = true; document.body.style.overflow = 'hidden'; },
            close() { this.open = false; this.active = null; document.body.style.overflow = ''; },
            get current() { return this.active !== null ? this.items[this.active] : null; },
         }"
         @keydown.escape.window="close()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_gallery_eyebrow', 'Foto & Video');
            $subtitle = setting('section_gallery_subtitle', 'Momen-momen berharga dari kehidupan sekolah kami.');
        @endphp
        <div class="flex items-end justify-between gap-6 mb-14" data-aos="fade-up">
            <div>
                @if($eyebrow)
                    <div class="fi-label mb-3">{{ $eyebrow }}</div>
                @endif
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">{{ setting('section_gallery_title') ?: 'Galeri Sekolah' }}</h2>
                @if($subtitle)
                    <p class="mt-2 text-base max-w-md leading-relaxed" style="color:var(--muted)">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <a href="{{ route('gallery.index') }}"
               class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-75"
               style="color:var(--primary)">
                Semua Foto
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($galleryMedia->isEmpty())
            <div class="rounded-2xl border border-dashed py-16 text-center" style="border-color:var(--border)" data-aos="fade-up">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl" style="background:var(--card)">🖼️</div>
                <p class="text-sm font-semibold" style="color:var(--text)">Belum ada foto di galeri</p>
                <p class="text-xs mt-1" style="color:var(--muted)">Tandai media dengan "Tampil di Galeri" dari panel admin.</p>
            </div>
        @else
            {{-- Neat, uniform media cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">
                @foreach($galleryMedia as $item)
                    <a href="{{ $item->is_embed ? $item->embed_url : $item->url }}"
                       @if($item->is_embed) target="_blank" rel="noopener" @endif
                       @click.prevent="show({{ $loop->index }})"
                       class="glr-card group relative block overflow-hidden rounded-2xl border shadow-sm cursor-pointer"
                       style="border-color:var(--border)"
                       data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}" data-aos-duration="500">

                        <div class="aspect-4/3 overflow-hidden" style="background:var(--card)">
                            <img src="{{ $item->thumbnail_url }}"
                                 alt="{{ $item->alt ?? $item->name }}"
                                 loading="lazy"
                                 class="glr-card-img w-full h-full object-cover">
                        </div>

                        {{-- Gradient overlay --}}
                        <div class="glr-overlay absolute inset-0 bg-linear-to-t from-black/75 via-black/10 to-transparent opacity-60 transition-opacity duration-300"></div>

                        {{-- Type badge --}}
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-sm"
                              style="color:var(--primary)">
                            @if($item->is_embed)
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            @endif
                            {{ $item->getTypeLabel() }}
                        </span>

                        {{-- Play overlay for videos --}}
                        @if($item->is_embed)
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span class="w-12 h-12 rounded-full bg-white/85 backdrop-blur-sm flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110">
                                    <svg class="w-5 h-5 translate-x-0.5" style="color:var(--primary)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </span>
                            </div>
                        @endif

                        {{-- Caption (fades up on hover) --}}
                        <div class="glr-cap absolute inset-x-0 bottom-0 p-3 sm:p-4">
                            <h3 class="text-white text-xs sm:text-sm font-semibold leading-snug">
                                {{ $item->name }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Lightbox ────────────────────────────────────── --}}
    <div x-cloak x-show="open"
         x-transition.opacity
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-8"
         style="background:rgba(0,0,0,.85);z-index:9999"
         @click.self="close()">

        {{-- Close --}}
        <button @click="close()"
                class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                aria-label="Tutup">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="w-full max-w-5xl" @click.stop x-show="current">
            <template x-if="current && current.type === 'image'">
                <img :src="current.src" :alt="current.name"
                     class="max-h-[80vh] w-auto mx-auto rounded-xl shadow-2xl object-contain">
            </template>
            <template x-if="current && current.type === 'video'">
                <div class="relative mx-auto rounded-xl overflow-hidden shadow-2xl bg-gray-900"
                     :style="current.provider === 'instagram' ? 'width: min(540px, 92vw)' : (current.vertical ? `width: min(calc(80vh * ${current.ratio}), 90vw)` : '')"
                     x-effect="current.embedSrc; current.html; loading = true"
                     x-data="{
                        loading: true,
                        offline: ! navigator.onLine,
                        failed: false,
                        init() {
                            const sync = () => { this.offline = ! navigator.onLine; if (! this.offline && this.loading) { this.reload(); } };
                            window.addEventListener('online', sync);
                            window.addEventListener('offline', sync);
                            this.$nextTick(() => {
                                const frame = this.$root.querySelector('iframe');
                                if (frame) { frame.addEventListener('load', () => { this.loading = false; this.failed = false; }); }
                                setTimeout(() => { if (this.loading && ! this.offline) { this.failed = true; } }, 20000);
                            });
                        },
                        reload() {
                            this.failed = false; this.loading = true;
                            const frame = this.$root.querySelector('iframe');
                            if (frame) { frame.src = frame.src; }
                        },
                     }">
                    <template x-if="current.provider === 'instagram'">
                        <iframe :src="current.embedSrc" scrolling="auto" loading="lazy"
                                class="block w-full bg-white border-0" style="height: min(640px, 80vh);"
                                @load="loading = false"></iframe>
                    </template>
                    <template x-if="current.provider !== 'instagram'">
                        <div x-html="current.html"></div>
                    </template>

                    {{-- Loading spinner --}}
                    <div x-show="loading && ! offline && ! failed" x-transition.opacity
                         class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-gray-900/85 text-white/80">
                        <svg class="w-9 h-9 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-xs font-medium">Memuat…</span>
                    </div>

                    {{-- Offline / failed warning --}}
                    <div x-show="offline || failed" x-transition.opacity
                         class="absolute inset-0 flex flex-col items-center justify-center gap-2.5 px-6 text-center bg-gray-900/90 text-white/85">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636L5.636 18.364M8.111 8.111A6 6 0 0112 7c1.657 0 3.157.672 4.243 1.757M5.636 11.05A9.96 9.96 0 0112 8m6.364 3.05A9.96 9.96 0 0012 8m-3 6a3 3 0 016 0M12 18h.01" />
                        </svg>
                        <p class="text-sm font-semibold" x-text="offline ? 'Tidak ada koneksi internet' : 'Gagal memuat konten'"></p>
                        <p class="text-xs text-white/55" x-text="offline ? 'Periksa koneksimu, lalu coba lagi.' : 'Periksa koneksi atau muat ulang konten.'"></p>
                        <button type="button" @click="reload()"
                                class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-white/10 hover:bg-white/20 px-4 py-1.5 text-xs font-semibold text-white transition-colors">
                            Coba lagi
                        </button>
                    </div>
                </div>
            </template>
            <p class="text-center text-white/80 text-sm mt-4 font-medium" x-text="current ? current.name : ''"></p>
        </div>
    </div>
</section>
