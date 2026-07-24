{{-- Shared styles for the "Konten Tambahan" image blocks (cover / carousel / gallery / lightbox). --}}
<style>
    /* ── Blocks ───────────────────────────────────────────── */
    .block-label {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .6875rem; font-weight: 700; letter-spacing: .07em;
        text-transform: uppercase; color: var(--primary); margin-bottom: .75rem;
    }
    .block-label::before {
        content: ''; display: inline-block; width: 1rem; height: 2px;
        background: var(--primary); border-radius: 1px;
    }

    /* Cover Image */
    .block-cover { margin: 2rem 0; }
    .block-cover img {
        width: 100%; border-radius: .875rem; object-fit: cover;
        max-height: 520px; display: block;
        box-shadow: 0 8px 32px rgba(0,0,0,.12);
    }
    .block-cover figcaption {
        text-align: center; font-size: .8rem; color: #9ca3af;
        margin-top: .625rem; font-style: italic;
    }

    /* Carousel */
    .block-carousel { margin: 2rem 0; border-radius: .875rem; overflow: hidden; position: relative; }
    .block-carousel img { width: 100%; max-height: 520px; object-fit: cover; display: block; }
    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 2.5rem; height: 2.5rem; border-radius: 9999px;
        background: rgba(255,255,255,.9); backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.6);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    .carousel-btn:hover { background: #fff; transform: translateY(-50%) scale(1.08); }
    .carousel-btn svg   { width: 1rem; height: 1rem; color: #374151; flex-shrink: 0; }

    /* Gallery */
    .block-gallery { margin: 2rem 0; }
    .gallery-item {
        position: relative; overflow: hidden; border-radius: .75rem;
        cursor: zoom-in; aspect-ratio: 4/3;
    }
    .gallery-item img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform .5s ease;
    }
    .gallery-item:hover img { transform: scale(1.06); }
    .gallery-caption {
        position: absolute; inset-x: 0; bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,.65), transparent);
        padding: .75rem .875rem .625rem;
        transform: translateY(100%); transition: transform .3s ease;
    }
    .gallery-item:hover .gallery-caption { transform: translateY(0); }
    .gallery-caption p { color: #fff; font-size: .75rem; line-height: 1.4; }

    /* CTA Button */
    .block-cta { margin: 2rem 0; }
    .block-cta-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: .8rem 1.75rem; border-radius: 9999px;
        font-size: .9375rem; font-weight: 600; line-height: 1;
        text-decoration: none; cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease, color .15s ease;
    }
    .block-cta-primary {
        background: var(--cta, var(--primary)); color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,.15);
    }
    .block-cta-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.22); }
    .block-cta-outline {
        background: transparent; color: var(--cta, var(--primary));
        border: 2px solid var(--cta, var(--primary));
    }
    .block-cta-outline:hover { background: var(--cta, var(--primary)); color: #fff; transform: translateY(-2px); }

    /* Lightbox */
    .lightbox-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,.92); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        padding: 1.5rem;
    }
    .lightbox-img {
        max-width: 100%; max-height: 90vh;
        border-radius: .75rem; object-fit: contain;
        box-shadow: 0 24px 80px rgba(0,0,0,.6);
    }
</style>
