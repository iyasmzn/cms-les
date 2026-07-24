@php
    $popups = \App\Models\Popup::activeNow()->get()->map(fn ($p) => [
        'id'             => $p->id,
        'title'          => $p->title,
        'content'        => $p->content,
        'image'          => $p->image ? asset('storage/' . $p->image) : null,
        'youtube_url'    => $p->youtube_url,
        'button_label'   => $p->button_label,
        'button_url'     => $p->button_url,
        'open_in_new_tab'=> $p->open_in_new_tab,
        'delay_seconds'  => (int) $p->delay_seconds,
        'show_every_days'=> (int) $p->show_every_days,
        'width'          => $p->width,
    ])->values();
@endphp

@if($popups->isNotEmpty())
<style>
/* ── Backdrop ────────────────────────────────────────────────── */
#pop-ov {
    position:fixed; inset:0; z-index:99999;
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    padding:1rem;
    background:rgba(0,0,0,.7);
    backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
    opacity:0; transition:opacity .35s ease; pointer-events:none;
    /* clip peek cards at viewport edge so they don't cause body horizontal scroll */
    overflow:hidden;
}
#pop-ov.pop-in { opacity:1; pointer-events:all; }

/* ── Stage — reference box, overflow visible for peek ────────── */
#pop-stage {
    position:relative;
    display:flex; flex-direction:column;
    align-items:center;
    width:100%;          /* fill overlay content box (accounts for padding) */
    /* max-width set by JS */
}

/* ── Track — holds absolutely positioned slide cards ─────────── */
#pop-track {
    position:relative;
    width:100%;
    /* height set by JS */
    transition:height .35s ease;
}

/* ── Individual slide card ───────────────────────────────────── */
.pop-slide {
    position:absolute; top:0; left:50%;
    width:100%;
    background:#fff;
    border-radius:1.75rem;
    overflow:hidden;
    transition:
        transform .44s cubic-bezier(.77,0,.175,1),
        opacity .38s ease,
        filter .38s ease,
        box-shadow .38s ease;
}
.pop-slide .pop-body { display:flex; flex-direction:column; }

/* ── YouTube ─────────────────────────────────────────────────── */
.pop-yt { flex-shrink:0; background:#000; position:relative; aspect-ratio:16/9; width:100%; overflow:hidden; }
/* thumbnail — always visible; passes through pointer events so slide click still fires */
.pop-yt-thumb { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block; pointer-events:none; }
/* iframe — hidden by default; only shown when slide is active */
.pop-yt iframe { position:absolute; inset:0; width:100%; height:100%; border:none; display:none; }

/* ── Image ───────────────────────────────────────────────────── */
.pop-img { flex-shrink:0; width:100%; height:260px; overflow:hidden; background:#f0f0f0; }
.pop-img img { width:100%; height:100%; object-fit:cover; display:block; pointer-events:none; }

/* ── Body text ───────────────────────────────────────────────── */
.pop-txt { padding:1.625rem 1.75rem 1.5rem; }
.pop-txt.no-media { padding-top:2.5rem; }

/* ── CTA ─────────────────────────────────────────────────────── */
.pop-cta {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.75rem 1.5rem; border-radius:.875rem;
    font-size:.9375rem; font-weight:600;
    background:var(--primary,#08484A); color:#fff; text-decoration:none;
    transition:opacity .15s, transform .15s;
}
.pop-cta:hover { opacity:.86; transform:translateY(-1px); }

/* ── Close button ────────────────────────────────────────────── */
#pop-close {
    position:absolute; top:.875rem; right:.875rem; z-index:20;
    width:2.25rem; height:2.25rem; border-radius:9999px; border:none; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.9); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
    box-shadow:0 2px 10px rgba(0,0,0,.2);
    transition:transform .15s, background .15s;
}
#pop-close:hover { background:#fff; transform:scale(1.1); }

/* ── Nav bar — floating pill, detached from card ─────────────── */
#pop-nav {
    display:none; align-items:center; gap:.5rem;
    padding:.5rem .75rem;
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
    border-radius:9999px;
    border:1px solid rgba(0,0,0,.08);
    box-shadow:0 4px 20px rgba(0,0,0,.18);
    margin-top:.875rem;
    position:relative; z-index:10;
}
#pop-nav.visible { display:flex; }

.pop-nav-btn {
    width:2rem; height:2rem; border-radius:9999px; flex-shrink:0;
    border:1.5px solid rgba(0,0,0,.12); background:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:background .15s, border-color .15s, transform .15s;
}
.pop-nav-btn:hover:not(:disabled) {
    background:var(--primary,#08484A); border-color:var(--primary,#08484A);
    transform:scale(1.08);
}
.pop-nav-btn:hover:not(:disabled) svg { stroke:#fff; }
.pop-nav-btn:disabled { opacity:.28; cursor:default; }
#pop-dots-nav { display:flex; gap:.375rem; align-items:center; flex:1; justify-content:center; }
.pop-dot {
    height:.5rem; border-radius:.25rem; border:none; cursor:pointer; padding:0;
    background:rgba(0,0,0,.2); transition:background .25s, width .25s; flex-shrink:0;
}
.pop-dot.on { background:var(--primary,#08484A); }
</style>

<div id="pop-ov" role="dialog" aria-modal="true">
    <div id="pop-stage">

        <button id="pop-close" aria-label="Tutup">
            <svg width="13" height="13" fill="none" stroke="#333" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div id="pop-track"></div>

        <div id="pop-nav">
            <button class="pop-nav-btn" id="pop-prev" aria-label="Sebelumnya" disabled>
                <svg width="13" height="13" fill="none" stroke="#333" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div id="pop-dots-nav"></div>
            <button class="pop-nav-btn" id="pop-next" aria-label="Selanjutnya">
                <svg width="13" height="13" fill="none" stroke="#333" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

    </div>
</div>

<script>
(function () {
    var ALL    = @json($popups);
    var WIDTHS = { sm: '480px', md: '600px', lg: '760px' };

    var ov       = document.getElementById('pop-ov');
    var stage    = document.getElementById('pop-stage');
    var track    = document.getElementById('pop-track');
    var closeBtn = document.getElementById('pop-close');
    var nav      = document.getElementById('pop-nav');
    var dotsNav  = document.getElementById('pop-dots-nav');
    var prevBtn  = document.getElementById('pop-prev');
    var nextBtn  = document.getElementById('pop-next');

    /* ── helpers ── */
    function esc(s) {
        return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function ytId(url) {
        if (!url) { return null; }
        var m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]{11})/);
        return m ? m[1] : null;
    }

    /* ── storage filter ── */
    var queue = ALL.filter(function (p) {
        var key = 'popup_seen_' + p.id;
        if (p.show_every_days === 0) { return !sessionStorage.getItem(key); }
        var last = localStorage.getItem(key);
        if (!last) { return true; }
        return (Date.now() - parseInt(last, 10)) / 86400000 >= p.show_every_days;
    });

    if (!queue.length) { return; }

    var currentIdx = 0;
    var _canClose  = false;
    var _heights   = [];

    /* ── build one slide element ── */
    function buildSlide(p, i) {
        var vid      = ytId(p.youtube_url);
        var hasMedia = !!(vid || p.image);
        var media    = '';

        if (vid) {
            media = '<div class="pop-yt">'
                  /* thumbnail shown while slide is peek; pointer-events:none so click reaches slide */
                  + '<img class="pop-yt-thumb" src="https://img.youtube.com/vi/' + esc(vid) + '/hqdefault.jpg" alt="">'
                  /* iframe hidden by default; shown only when this slide is active */
                  + '<iframe data-src="https://www.youtube-nocookie.com/embed/' + esc(vid)
                  + '?rel=0&modestbranding=1" src="" '
                  + 'allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" '
                  + 'allowfullscreen loading="lazy"></iframe></div>';
        } else if (p.image) {
            media = '<div class="pop-img"><img src="' + esc(p.image) + '" alt="' + esc(p.title || '') + '"></div>';
        }

        var body = '<div class="pop-body"><div class="pop-txt' + (hasMedia ? '' : ' no-media') + '">';
        if (p.title)   { body += '<h2 style="font-size:1.25rem;font-weight:700;color:#1d1d1f;margin:0 0 .625rem;line-height:1.3;padding-right:2.5rem;">' + esc(p.title) + '</h2>'; }
        if (p.content) { body += '<p style="font-size:.9375rem;color:#6e6e73;line-height:1.75;margin:0 0 1.25rem;">' + esc(p.content) + '</p>'; }
        if (p.button_label && p.button_url) {
            body += '<a class="pop-cta" href="' + esc(p.button_url) + '"'
                  + ' target="' + (p.open_in_new_tab ? '_blank' : '_self') + '"'
                  + (p.open_in_new_tab ? ' rel="noopener noreferrer"' : '') + '>'
                  + esc(p.button_label)
                  + '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>'
                  + '</a>';
        }
        body += '</div></div>';

        var el       = document.createElement('div');
        el.className = 'pop-slide';
        el.dataset.idx = i;
        el.innerHTML = media + body;
        return el;
    }

    /* ── compute adaptive peek offset (% of card width) ── */
    function peekOffset() {
        var vw    = window.innerWidth;
        var cardW = Math.min(parseFloat(stage.style.maxWidth) || 600, vw - 32);
        /* stageLeft = space from viewport edge to stage left edge */
        var stageLeft = (vw - cardW) / 2;
        /* target visible peek width: up to 35% of card, but limited by available space - 16px gap */
        var peekPx = Math.max(0, Math.min(cardW * 0.35, stageLeft - 16));
        /* derive X such that: stageLeft + cardW*(0.5 - X) = peekPx */
        var X = (stageLeft - peekPx) / cardW + 0.5;
        return Math.min(1.15, Math.max(0.55, X));
    }

    /* ── apply transforms to all slides relative to currentIdx ── */
    function applyTransforms(idx) {
        var X = peekOffset();

        Array.from(track.children).forEach(function (slide, i) {
            var diff = i - idx;

            slide.style.position   = 'absolute';
            slide.style.top        = '0';
            slide.style.left       = '50%';
            slide.style.width      = '100%';

            if (diff === 0) {
                /* active */
                slide.style.transform    = 'translateX(-50%) scale(1)';
                slide.style.opacity      = '1';
                slide.style.filter       = '';
                slide.style.zIndex       = '3';
                slide.style.cursor       = 'default';
                slide.style.pointerEvents = 'auto';
                slide.style.boxShadow    = '0 4px 6px rgba(0,0,0,.05),0 24px 64px rgba(0,0,0,.32)';
                slide.style.borderRadius = '1.75rem';
            } else if (Math.abs(diff) === 1) {
                /* immediate neighbour — peek */
                var dir = diff > 0 ? 1 : -1;
                slide.style.transform    = 'translateX(calc(-50% ' + (dir > 0 ? '+' : '-') + ' ' + (X * 100).toFixed(1) + '%)) scale(0.84)';
                slide.style.opacity      = '0.65';
                slide.style.filter       = 'brightness(0.7)';
                slide.style.zIndex       = '2';
                slide.style.cursor       = 'pointer';
                slide.style.pointerEvents = 'auto';
                slide.style.boxShadow    = '0 2px 4px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.16)';
                slide.style.borderRadius = '1.75rem';
            } else {
                /* far slides — hidden off-screen */
                var dir = diff > 0 ? 1 : -1;
                slide.style.transform    = 'translateX(calc(-50% ' + (dir > 0 ? '+' : '-') + ' 120%)) scale(0.72)';
                slide.style.opacity      = '0';
                slide.style.filter       = '';
                slide.style.zIndex       = '1';
                slide.style.cursor       = 'default';
                slide.style.pointerEvents = 'none';
                slide.style.boxShadow    = '';
                slide.style.borderRadius = '1.75rem';
            }
        });

        /* update track height to active slide's measured height */
        var maxH = Math.floor(window.innerHeight * 0.82);
        track.style.height = Math.min(_heights[idx] || 240, maxH) + 'px';

        /* nav state */
        updateDots(idx);
        prevBtn.disabled = idx === 0;
        nextBtn.disabled = idx === queue.length - 1;
    }

    /* ── navigate ── */
    function goTo(idx) {
        idx = Math.max(0, Math.min(idx, queue.length - 1));

        /* hide & pause current YouTube */
        var curIframe = track.children[currentIdx] && track.children[currentIdx].querySelector('iframe[data-src]');
        if (curIframe) { curIframe.src = ''; curIframe.style.display = 'none'; }

        currentIdx = idx;
        applyTransforms(idx);

        /* show & resume target YouTube */
        var nxtIframe = track.children[idx] && track.children[idx].querySelector('iframe[data-src]');
        if (nxtIframe) { nxtIframe.src = nxtIframe.dataset.src; nxtIframe.style.display = 'block'; }
    }

    /* ── dots ── */
    function buildDots() {
        dotsNav.innerHTML = queue.map(function (_, i) {
            return '<button class="pop-dot' + (i === 0 ? ' on' : '') + '" data-i="' + i + '"'
                 + ' style="width:' + (i === 0 ? '1.25rem' : '.5rem') + '" aria-label="Slide ' + (i + 1) + '"></button>';
        }).join('');
        dotsNav.querySelectorAll('.pop-dot').forEach(function (d) {
            d.addEventListener('click', function () { goTo(parseInt(d.dataset.i, 10)); });
        });
    }
    function updateDots(idx) {
        dotsNav.querySelectorAll('.pop-dot').forEach(function (d, i) {
            var on = i === idx;
            d.classList.toggle('on', on);
            d.style.width = on ? '1.25rem' : '.5rem';
        });
    }

    /* ── show popup ── */
    function show() {
        /* constrain stage width; width:100% (from CSS) fills overlay minus padding */
        stage.style.maxWidth = WIDTHS[queue[0].width] || '600px';

        /* build slides as static first (so we can measure their natural height) */
        track.innerHTML = '';
        queue.forEach(function (p, i) {
            var el = buildSlide(p, i);
            el.style.position = 'relative';
            track.appendChild(el);

            /* direct click handler per-slide — more reliable than event delegation
               because the peek card may be outside #pop-stage's layout bounds */
            (function (slideIdx) {
                el.addEventListener('click', function (e) {
                    if (slideIdx !== currentIdx) {
                        e.stopPropagation();
                        goTo(slideIdx);
                    }
                });
            })(i);
        });

        /* measure natural heights */
        Array.from(track.children).forEach(function (slide, i) {
            _heights[i] = slide.offsetHeight;
        });

        /* apply absolute positioning & transforms */
        currentIdx = 0;
        applyTransforms(0);

        /* nav bar */
        if (queue.length > 1) {
            buildDots();
            nav.classList.add('visible');
        }

        /* load first YouTube (show iframe, hide thumbnail is handled by CSS — iframe on top) */
        var iframe = track.children[0] && track.children[0].querySelector('iframe[data-src]');
        if (iframe) { iframe.src = iframe.dataset.src; iframe.style.display = 'block'; }

        /* reveal */
        ov.classList.add('pop-in');
        _canClose = false;
        setTimeout(function () { _canClose = true; }, 700);
    }

    /* ── dismiss (marks all as seen) ── */
    function dismiss() {
        var curIframe = track.children[currentIdx] && track.children[currentIdx].querySelector('iframe[data-src]');
        if (curIframe) { curIframe.src = ''; curIframe.style.display = 'none'; }

        queue.forEach(function (p) {
            var key = 'popup_seen_' + p.id;
            if (p.show_every_days === 0) {
                sessionStorage.setItem(key, '1');
            } else {
                localStorage.setItem(key, Date.now().toString());
            }
        });
        ov.classList.remove('pop-in');
    }

    /* ── events ── */
    closeBtn.addEventListener('click', dismiss);

    /* backdrop click → close (slide clicks are handled per-slide via stopPropagation) */
    ov.addEventListener('click', function (e) {
        if (_canClose && !stage.contains(e.target)) { dismiss(); }
    });

    prevBtn.addEventListener('click', function () { goTo(currentIdx - 1); });
    nextBtn.addEventListener('click', function () { goTo(currentIdx + 1); });

    /* swipe */
    var _tx = 0;
    track.addEventListener('touchstart', function (e) { _tx = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
        var diff = _tx - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 48) { goTo(currentIdx + (diff > 0 ? 1 : -1)); }
    }, { passive: true });

    /* keyboard */
    document.addEventListener('keydown', function (e) {
        if (!ov.classList.contains('pop-in')) { return; }
        if (e.key === 'Escape')     { dismiss(); }
        if (e.key === 'ArrowRight') { goTo(currentIdx + 1); }
        if (e.key === 'ArrowLeft')  { goTo(currentIdx - 1); }
    });

    /* recompute peek on resize */
    window.addEventListener('resize', function () {
        if (ov.classList.contains('pop-in')) { applyTransforms(currentIdx); }
    }, { passive: true });

    /* ── kick off ── */
    var delay = Math.max(0, queue[0].delay_seconds || 0);
    setTimeout(show, delay * 1000);
})();
</script>
@endif
