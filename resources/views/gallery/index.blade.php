@extends('layouts.public')

@push('head')
<style>
    [x-cloak] { display: none !important; }

    .gallery-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 60%, #0a5a5c 100%);
        position: relative;
        overflow: hidden;
    }
    .gallery-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 60% at 80% 20%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 10% 80%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
    .album-pill {
        font-size: .8125rem;
        font-weight: 600;
        padding: .45rem 1.1rem;
        border-radius: 9999px;
        border: 1.5px solid var(--border);
        color: var(--muted);
        background: var(--card);
        transition: all .2s;
        white-space: nowrap;
    }
    .album-pill:hover, .album-pill.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }
    .gallery-grid {
        columns: 2;
        column-gap: .75rem;
    }
    @media (min-width: 640px) { .gallery-grid { columns: 3; } }
    @media (min-width: 1024px) { .gallery-grid { columns: 4; } }
    .gallery-item {
        break-inside: avoid;
        margin-bottom: .75rem;
        border-radius: .75rem;
        overflow: hidden;
        position: relative;
        display: block;
        cursor: zoom-in;
        background: var(--card);
        border: 1.5px solid var(--border);
    }
    .gallery-item img {
        width: 100%;
        display: block;
        transition: transform .4s ease;
    }
    .gallery-item:hover img { transform: scale(1.04); }
    .gallery-item-overlay {
        position: absolute;
        inset: 0;
        background: rgba(8,40,40,.6);
        opacity: 0;
        transition: opacity .25s;
        display: flex;
        align-items: flex-end;
        padding: .75rem;
    }
    .gallery-item:hover .gallery-item-overlay { opacity: 1; }
</style>
@endpush

@php
    $lightboxItems = $items->getCollection()->map(fn ($m) => [
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

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="gallery-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">
    <x-hero-geo />
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            🖼️ Galeri Foto & Video
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Galeri & Dokumentasi
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Foto kegiatan, fasilitas, dan momen berharga dari {{ setting('site_name', config('app.name')) }}.
        </p>
    </div>
</section>

{{-- ── Gallery ──────────────────────────────────────────── --}}
<section class="py-16 sm:py-20" style="background:var(--bg)"
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

        {{-- Type Filter --}}
        <div class="flex flex-wrap gap-2 mb-4" data-aos="fade-up">
            <a href="{{ route('gallery.index', array_filter(['album' => $album])) }}"
               class="album-pill {{ ! $type ? 'active' : '' }}">Semua</a>
            <a href="{{ route('gallery.index', array_filter(['type' => 'foto', 'album' => $album])) }}"
               class="album-pill {{ $type === 'foto' ? 'active' : '' }}">Foto</a>
            <a href="{{ route('gallery.index', array_filter(['type' => 'video', 'album' => $album])) }}"
               class="album-pill {{ $type === 'video' ? 'active' : '' }}">Video</a>
        </div>

        {{-- Album Filter --}}
        @if($albums->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-10 overflow-x-auto pb-1" data-aos="fade-up">
            <a href="{{ route('gallery.index', array_filter(['type' => $type])) }}"
               class="album-pill {{ is_null($album) ? 'active' : '' }}">
                Semua Album
            </a>
            @foreach($albums as $a)
            <a href="{{ route('gallery.index', array_filter(['type' => $type, 'album' => $a])) }}"
               class="album-pill {{ $album === $a ? 'active' : '' }}">
                {{ $a }}
            </a>
            @endforeach
        </div>
        @endif

        @if($items->isNotEmpty())
        <div class="gallery-grid" data-aos="fade-up">
            @foreach($items as $item)
            <a href="{{ $item->is_embed ? $item->embed_url : $item->url }}"
               @if($item->is_embed) target="_blank" rel="noopener" @endif
               @click.prevent="show({{ $loop->index }})"
               class="gallery-item group"
               title="{{ $item->alt ?? $item->name }}">
                <img src="{{ $item->thumbnail_url }}"
                     alt="{{ $item->alt ?? $item->name }}"
                     loading="lazy">

                {{-- Play overlay for videos --}}
                @if($item->is_embed)
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="w-12 h-12 rounded-full bg-white/85 backdrop-blur-sm flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110">
                        <svg class="w-5 h-5 translate-x-0.5" style="color:var(--primary)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                </div>
                <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-sm"
                      style="color:var(--primary)">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    {{ $item->getTypeLabel() }}
                </span>
                @endif

                <div class="gallery-item-overlay">
                    @if($item->alt || $item->description)
                    <p class="text-xs text-white font-medium line-clamp-2">
                        {{ $item->alt ?? $item->description }}
                    </p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        @if($items->hasPages())
        <div class="mt-12">
            {{ $items->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-24 fi-card" data-aos="fade-up">
            <div class="text-6xl mb-4">🖼️</div>
            <p class="text-base font-medium" style="color:var(--muted)">
                @if($album)
                    Belum ada media di album <strong>{{ $album }}</strong>.
                @elseif($type === 'video')
                    Belum ada video di galeri.
                @elseif($type === 'foto')
                    Belum ada foto di galeri.
                @else
                    Belum ada media di galeri.
                @endif
            </p>
        </div>
        @endif

    </div>

    {{-- ── Lightbox ────────────────────────────────────── --}}
    <div x-cloak x-show="open"
         x-transition.opacity
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-8"
         style="background:rgba(0,0,0,.9);z-index:9999"
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

@endsection
