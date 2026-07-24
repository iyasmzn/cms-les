{{--
    Reusable "Konten Tambahan" image blocks renderer.
    Params:
      $blocks — array of block definitions (from the model's `blocks` cast)
      $title  — fallback alt/caption text (page/post/program title)
--}}
@php $title = $title ?? ''; @endphp
@if(!empty($blocks))
    @foreach($blocks as $block)
        @php $type = $block['type'] ?? ''; @endphp

        {{-- ── Cover Image ─────────────────────────────── --}}
        @if($type === 'image_cover' && !empty($block['image']))
            <figure class="block-cover">
                <div class="block-label">Cover Image</div>
                <img src="{{ asset('storage/' . $block['image']) }}"
                     alt="{{ $block['caption'] ?? $title }}"
                     loading="lazy">
                @if(!empty($block['caption']))
                    <figcaption>{{ $block['caption'] }}</figcaption>
                @endif
            </figure>
        @endif

        {{-- ── Carousel ─────────────────────────────────── --}}
        @if($type === 'image_carousel' && !empty($block['images']))
            @php $slides = array_values(array_filter($block['images'], fn($i) => !empty($i['image']))); @endphp
            @if(count($slides) > 0)
                <div class="my-8">
                    <div class="block-label">Carousel</div>
                    <div class="block-carousel"
                         x-data="{
                             slide: 0,
                             total: {{ count($slides) }},
                             next() { this.slide = (this.slide + 1) % this.total },
                             prev() { this.slide = (this.slide - 1 + this.total) % this.total }
                         }">
                        @foreach($slides as $i => $img)
                            <div x-show="slide === {{ $i }}"
                                 x-transition:enter="transition-opacity duration-500 ease-in-out"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity duration-300 ease-in-out"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="relative">
                                <img src="{{ asset('storage/' . $img['image']) }}"
                                     alt="{{ $img['caption'] ?? '' }}"
                                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                                @if(!empty($img['caption']))
                                    <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/65 to-transparent px-5 py-4">
                                        <p class="text-white text-sm leading-snug">{{ $img['caption'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if(count($slides) > 1)
                            {{-- Prev / Next --}}
                            <button @click="prev()" class="carousel-btn" style="left:.75rem" aria-label="Sebelumnya">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="next()" class="carousel-btn" style="right:.75rem" aria-label="Selanjutnya">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Dots --}}
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                                @foreach($slides as $i => $img)
                                    <button @click="slide = {{ $i }}"
                                            :class="slide === {{ $i }} ? 'w-5 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
                                            class="h-2 rounded-full transition-all duration-300"
                                            aria-label="Slide {{ $i + 1 }}">
                                    </button>
                                @endforeach
                            </div>

                            {{-- Counter --}}
                            <div class="absolute top-3 right-3 text-xs font-semibold text-white bg-black/40 backdrop-blur-sm rounded-full px-2.5 py-1">
                                <span x-text="slide + 1"></span>/{{ count($slides) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        {{-- ── Gallery ──────────────────────────────────── --}}
        @if($type === 'image_gallery' && !empty($block['images']))
            @php
                $imgs    = array_values(array_filter($block['images'], fn($i) => !empty($i['image'])));
                $cols    = $block['columns'] ?? '3';
                $gridCls = match($cols) {
                    '2'     => 'grid-cols-2',
                    '4'     => 'grid-cols-2 sm:grid-cols-4',
                    default => 'grid-cols-2 sm:grid-cols-3',
                };
            @endphp
            @if(count($imgs) > 0)
                <div class="block-gallery"
                     x-data="{
                         lightbox: false,
                         current: '',
                         currentAlt: '',
                         open(src, alt) { this.current = src; this.currentAlt = alt; this.lightbox = true; },
                         close() { this.lightbox = false; }
                     }">
                    <div class="block-label">Galeri Foto</div>

                    <div class="grid {{ $gridCls }} gap-3">
                        @foreach($imgs as $img)
                            <div class="gallery-item"
                                 @click="open('{{ asset('storage/' . $img['image']) }}', '{{ $img['caption'] ?? '' }}')">
                                <img src="{{ asset('storage/' . $img['image']) }}"
                                     alt="{{ $img['caption'] ?? '' }}"
                                     loading="lazy">
                                @if(!empty($img['caption']))
                                    <div class="gallery-caption">
                                        <p>{{ $img['caption'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Lightbox (teleported to <body> so `position: fixed` escapes any
                         transformed ancestor card and covers the full viewport) --}}
                    <template x-teleport="body">
                        <div x-show="lightbox"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click.self="close()"
                             @keydown.escape.window="close()"
                             class="lightbox-overlay"
                             style="display: none;">
                            <img :src="current" :alt="currentAlt" class="lightbox-img">
                            <button @click="close()"
                                    class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-colors"
                                    aria-label="Tutup">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <p x-show="currentAlt" x-text="currentAlt"
                               class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/70 text-sm bg-black/40 backdrop-blur-sm rounded-full px-4 py-1.5 max-w-sm text-center"></p>
                        </div>
                    </template>
                </div>
            @endif
        @endif

        {{-- ── CTA Button ───────────────────────────────── --}}
        @if($type === 'cta_button' && !empty($block['label']) && !empty($block['url']))
            @php
                $align   = $block['alignment'] ?? 'center';
                $justify = match($align) {
                    'left'  => 'justify-start',
                    'right' => 'justify-end',
                    default => 'justify-center',
                };
                $styleCls = ($block['style'] ?? 'primary') === 'outline' ? 'block-cta-outline' : 'block-cta-primary';
                $newTab   = !empty($block['open_in_new_tab']);
                $color    = trim($block['color'] ?? '');
            @endphp
            <div class="block-cta flex {{ $justify }}">
                <a href="{{ $block['url'] }}"
                   class="block-cta-btn {{ $styleCls }}"
                   @if($color) style="--cta: {{ $color }}" @endif
                   @if($newTab) target="_blank" rel="noopener noreferrer" @endif>
                    {{ $block['label'] }}
                </a>
            </div>
        @endif

    @endforeach
@endif
