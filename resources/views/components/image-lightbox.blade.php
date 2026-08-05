{{--
    Thumbnail card that opens a zoomable lightbox: scroll or pinch to zoom,
    drag/swipe to pan, double-click to toggle, Esc to close. The image is
    contained inside its card, so an oversized upload can never break the
    surrounding layout.

    Works anywhere Alpine is loaded — the admin panel (Filament ships Alpine)
    and the public site alike:

        <x-image-lightbox :url="$payment->proofUrl()" caption="Bukti transfer" />

    Every style is inline on purpose. The admin panel uses Filament's
    precompiled CSS with no custom theme build, so Tailwind classes written here
    would silently render as unstyled markup there.

    @param  string|null  $url       Image to show; renders a dash when empty.
    @param  string       $caption   Line under the thumbnail; also the alt text fallback.
    @param  string|null  $alt       Explicit alt text.
    @param  string       $maxWidth  CSS width cap for the card.
    @param  string       $maxHeight CSS height cap for the thumbnail.
    @param  bool         $card      Set false to drop the card chrome and caption.
--}}
@props([
    'url' => null,
    'caption' => 'Click to zoom',
    'alt' => null,
    'maxWidth' => '28rem',
    'maxHeight' => '20rem',
    'card' => true,
])

@php
    $alt ??= $caption;
@endphp

@if (blank($url))
    <p style="font-size:.875rem;color:#9ca3af">—</p>
@else
    <div
        x-data="{
            open: false,
            scale: 1,
            x: 0,
            y: 0,
            pointers: new Map(),
            pinchDistance: 0,
            dragging: false,
            lastX: 0,
            lastY: 0,

            show() {
                this.open = true;
                this.reset();
                document.body.style.overflow = 'hidden';
            },

            hide() {
                this.open = false;
                document.body.style.overflow = '';
            },

            reset() {
                this.scale = 1;
                this.x = 0;
                this.y = 0;
            },

            get stage() {
                return this.$refs.stage.getBoundingClientRect();
            },

            clampScale(value) {
                return Math.min(8, Math.max(1, value));
            },

            /** Zoom while keeping the point under the cursor/fingers still. */
            zoomTo(next, clientX, clientY) {
                next = this.clampScale(next);

                const rect = this.stage;
                const originX = clientX - rect.left - rect.width / 2;
                const originY = clientY - rect.top - rect.height / 2;
                const ratio = next / this.scale;

                this.x = originX - ratio * (originX - this.x);
                this.y = originY - ratio * (originY - this.y);
                this.scale = next;
                this.clampPan();
            },

            /** Keep the image from being dragged off the viewport. */
            clampPan() {
                if (this.scale <= 1) {
                    this.x = 0;
                    this.y = 0;

                    return;
                }

                const rect = this.stage;
                const maxX = (rect.width * (this.scale - 1)) / 2;
                const maxY = (rect.height * (this.scale - 1)) / 2;

                this.x = Math.min(maxX, Math.max(-maxX, this.x));
                this.y = Math.min(maxY, Math.max(-maxY, this.y));
            },

            onWheel(event) {
                this.zoomTo(this.scale * Math.exp(-event.deltaY * 0.0022), event.clientX, event.clientY);
            },

            onPointerDown(event) {
                this.pointers.set(event.pointerId, event);
                event.target.setPointerCapture?.(event.pointerId);

                if (this.pointers.size === 2) {
                    this.pinchDistance = this.distance();
                    this.dragging = false;

                    return;
                }

                this.dragging = true;
                this.lastX = event.clientX;
                this.lastY = event.clientY;
            },

            onPointerMove(event) {
                if (! this.pointers.has(event.pointerId)) {
                    return;
                }

                this.pointers.set(event.pointerId, event);

                if (this.pointers.size === 2) {
                    const [a, b] = [...this.pointers.values()];
                    const current = this.distance();

                    if (this.pinchDistance > 0) {
                        this.zoomTo(
                            this.scale * (current / this.pinchDistance),
                            (a.clientX + b.clientX) / 2,
                            (a.clientY + b.clientY) / 2,
                        );
                    }

                    this.pinchDistance = current;

                    return;
                }

                if (! this.dragging || this.scale === 1) {
                    return;
                }

                this.x += event.clientX - this.lastX;
                this.y += event.clientY - this.lastY;
                this.lastX = event.clientX;
                this.lastY = event.clientY;
                this.clampPan();
            },

            onPointerUp(event) {
                this.pointers.delete(event.pointerId);

                if (this.pointers.size < 2) {
                    this.pinchDistance = 0;
                }

                if (this.pointers.size === 0) {
                    this.dragging = false;
                }
            },

            distance() {
                const [a, b] = [...this.pointers.values()];

                return Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
            },

            toggleZoom(event) {
                this.scale > 1
                    ? this.reset()
                    : this.zoomTo(2.5, event.clientX, event.clientY);
            },
        }"
        x-on:keydown.escape.window="open && hide()"
    >
        <div @style([
            "max-width: {$maxWidth}",
            'overflow: hidden',
            'border: 1px solid rgba(128,128,128,.25); border-radius: .75rem; background: rgba(128,128,128,.06); padding: .5rem' => $card,
        ])>
            <button type="button" x-on:click="show()" style="display:block;width:100%;cursor:zoom-in;border:0;background:none;padding:0">
                <img
                    src="{{ $url }}"
                    alt="{{ $alt }}"
                    style="display:block;margin:0 auto;width:100%;object-fit:contain;border-radius:.5rem;max-height:{{ $maxHeight }}"
                    loading="lazy"
                >
            </button>
            @if ($card)
                <p style="padding:.5rem .25rem 0;font-size:.75rem;color:#6b7280">{{ $caption }}</p>
            @endif
        </div>

        {{-- Lightbox, teleported so no card overflow or stacking context clips it. --}}
        <template x-teleport="body">
            {{-- No x-cloak: teleported markup only exists once Alpine has run,
                 and this panel's CSS ships no [x-cloak] rule anyway. --}}
            <div
                x-show="open"
                x-transition.opacity
                style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.92)"
                x-on:click.self="hide()"
            >
                <div style="position:absolute;top:.75rem;right:.75rem;z-index:10;display:flex;align-items:center;gap:.5rem">
                    <button type="button" x-on:click="zoomTo(scale - 0.5, innerWidth / 2, innerHeight / 2)"
                            style="display:flex;height:2.5rem;width:2.5rem;align-items:center;justify-content:center;border:0;border-radius:9999px;background:rgba(255,255,255,.12);color:#fff;cursor:pointer"
                            title="Zoom out">
                        <svg style="height:1.25rem;width:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M8 11h6m5 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                    </button>
                    <button type="button" x-on:click="zoomTo(scale + 0.5, innerWidth / 2, innerHeight / 2)"
                            style="display:flex;height:2.5rem;width:2.5rem;align-items:center;justify-content:center;border:0;border-radius:9999px;background:rgba(255,255,255,.12);color:#fff;cursor:pointer"
                            title="Zoom in">
                        <svg style="height:1.25rem;width:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 8v6m-3-3h6m5 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                    </button>
                    <button type="button" x-on:click="reset()" x-show="scale !== 1"
                            style="display:flex;height:2.5rem;align-items:center;justify-content:center;border:0;border-radius:9999px;background:rgba(255,255,255,.12);padding:0 1rem;font-size:.75rem;font-weight:600;color:#fff;cursor:pointer">
                        Reset
                    </button>
                    <a href="{{ $url }}" target="_blank" rel="noopener"
                       style="display:flex;height:2.5rem;width:2.5rem;align-items:center;justify-content:center;border-radius:9999px;background:rgba(255,255,255,.12);color:#fff"
                       title="Open original">
                        <svg style="height:1.25rem;width:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button type="button" x-on:click="hide()"
                            style="display:flex;height:2.5rem;width:2.5rem;align-items:center;justify-content:center;border:0;border-radius:9999px;background:rgba(255,255,255,.12);color:#fff;cursor:pointer"
                            title="Close">
                        <svg style="height:1.25rem;width:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div
                    x-ref="stage"
                    style="display:flex;height:100%;width:100%;align-items:center;justify-content:center;overflow:hidden;padding:1rem;touch-action:none"
                    x-on:wheel.prevent="onWheel($event)"
                    x-on:pointerdown="onPointerDown($event)"
                    x-on:pointermove="onPointerMove($event)"
                    x-on:pointerup="onPointerUp($event)"
                    x-on:pointercancel="onPointerUp($event)"
                    x-on:pointerleave="onPointerUp($event)"
                    x-on:dblclick="toggleZoom($event)"
                >
                    <img
                        src="{{ $url }}"
                        alt="{{ $alt }}"
                        draggable="false"
                        style="max-height:100%;max-width:100%;object-fit:contain;user-select:none;transition:transform .05s linear"
                        {{-- Object syntax so Alpine merges these onto the inline
                             style above instead of replacing the whole attribute. --}}
                        x-bind:style="{
                            transform: `translate(${x}px, ${y}px) scale(${scale})`,
                            cursor: scale > 1 ? 'grab' : 'zoom-in',
                        }"
                    >
                </div>

                <p style="position:absolute;bottom:1rem;left:50%;transform:translateX(-50%);text-align:center;font-size:.75rem;color:rgba(255,255,255,.6);pointer-events:none">
                    Scroll or pinch to zoom · drag to pan · double-click to toggle · Esc to close
                </p>
            </div>
        </template>
    </div>
@endif
