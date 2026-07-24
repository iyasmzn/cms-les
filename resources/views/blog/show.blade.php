@extends('layouts.public')

{{-- ── JSON-LD Article Schema ──────────────────────────────── --}}
@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $post->title,
    'description'   => $post->meta_description,
    'image'         => $post->thumbnail_url,
    'url'           => $post->canonical_url,
    'datePublished' => $post->published_at?->toIso8601String(),
    'dateModified'  => $post->updated_at?->toIso8601String(),
    'author'        => ['@type' => 'Person', 'name' => $post->author],
    'publisher'     => [
        '@type' => 'EducationalOrganization',
        'name'  => setting('site_name', config('app.name')),
        'url'   => url('/'),
    ],
    'articleSection' => $post->category,
    'wordCount'      => str_word_count(strip_tags($post->content)),
    'inLanguage'     => 'id-ID',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

{{-- ── BreadcrumbList Schema ──── --}}
<script type="application/ld+json">
{!! json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog',    'item' => route('blog.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $post->canonical_url],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush


@push('head')
<style>
    /* Reading progress bar */
    #reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #d97706, #fbbf24);
        z-index: 9999;
        width: 0%;
        transition: width .1s linear;
    }

    /* Hero */
    .article-hero {
        position: relative;
        height: 20rem;
        overflow: hidden;
    }
    @media(min-width: 640px) { .article-hero { height: 26rem; } }
    @media(min-width: 1024px) { .article-hero { height: 32rem; } }

    .article-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .article-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.35) 50%, rgba(0,0,0,.1) 100%);
    }

    /* Prose typography */
    .article-prose h2 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-top: 2.75rem;
        margin-bottom: 1rem;
        color: #030712;
        line-height: 1.3;
        padding-bottom: .5rem;
        border-bottom: 2px solid #fde68a;
        display: inline-block;
    }
    .article-prose h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: .75rem;
        color: #1f2937;
    }
    .article-prose p {
        margin-bottom: 1.375rem;
        line-height: 1.85;
        color: #374151;
        font-size: 1.0625rem;
    }
    .article-prose ul, .article-prose ol {
        padding-left: 1.5rem;
        margin-bottom: 1.25rem;
        color: #374151;
    }
    .article-prose ul { list-style: disc; }
    .article-prose ol { list-style: decimal; }
    .article-prose li { margin-bottom: .5rem; line-height: 1.75; }
    .article-prose blockquote {
        border-left: 4px solid #d97706;
        padding: .75rem 1.25rem;
        margin: 1.75rem 0;
        background: #fffbeb;
        border-radius: 0 .5rem .5rem 0;
        color: #78350f;
        font-style: italic;
    }
    .article-prose a {
        color: #d97706;
        text-decoration: underline;
        text-underline-offset: 3px;
        font-weight: 500;
    }
    .article-prose strong { color: #030712; font-weight: 700; }
    .article-prose img {
        border-radius: .75rem;
        margin: 1.5rem 0;
        max-width: 100%;
    }
    .article-prose code {
        background: #f3f4f6;
        padding: .125rem .375rem;
        border-radius: .25rem;
        font-size: .875em;
        color: #d97706;
    }

    /* Sidebar sticky */
    .sidebar-sticky {
        position: sticky;
        top: 5rem;
    }

    /* Share buttons */
    .share-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .55rem 1rem;
        border-radius: .625rem;
        font-size: .8125rem;
        font-weight: 600;
        color: #fff;
        transition: opacity .15s, transform .15s;
    }
    .share-btn:hover {
        opacity: .88;
        transform: translateY(-1px);
    }

    /* TOC */
    .toc-link {
        display: flex;
        gap: .625rem;
        font-size: .75rem;
        color: #6b7280;
        padding: .3rem 0;
        line-height: 1.5;
        transition: color .15s;
        cursor: pointer;
    }
    .toc-link:hover { color: #d97706; }
    .toc-num {
        flex-shrink: 0;
        font-weight: 700;
        color: #fbbf24;
        font-size: .6875rem;
    }

    /* Related cards */
    .related-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .25s, box-shadow .25s, border-color .25s;
    }
    .related-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0,0,0,.1);
        border-color: #fcd34d;
    }
    .related-card:hover .related-img {
        transform: scale(1.06);
    }
    .related-img { transition: transform .5s ease; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bar = document.getElementById('reading-progress');
        if (!bar) { return; }
        window.addEventListener('scroll', function () {
            const doc = document.documentElement;
            const scrolled = doc.scrollTop;
            const total = doc.scrollHeight - doc.clientHeight;
            bar.style.width = total > 0 ? (scrolled / total * 100) + '%' : '0%';
        }, { passive: true });
    });
</script>
@include('partials.content-block-styles')
@endpush

@section('content')

    {{-- Reading progress bar --}}
    <div id="reading-progress"></div>

    {{-- ── Hero Image ───────────────────────────────────────── --}}
    <div class="article-hero -mt-17">
        <img src="{{ $post->thumbnail_url }}"
             alt="{{ $post->title }}"
             loading="eager">
        <div class="article-hero-overlay"></div>

        {{-- Overlay content --}}
        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-10">
            <div class="max-w-7xl mx-auto">
                {{-- Mobile breadcrumb --}}
                <nav aria-label="Breadcrumb" class="md:hidden flex items-center gap-1.5 text-xs text-white/60 mb-3">
                    <a href="/" class="hover:text-white transition-colors">Beranda</a>
                    <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                </nav>
                <span class="inline-block text-[11px] font-bold px-3 py-1.5 rounded-full bg-amber-500 text-white mb-3 uppercase tracking-wide">
                    {{ $post->category }}
                </span>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-snug tracking-tight max-w-3xl">
                    {{ $post->title }}
                </h1>
            </div>
        </div>
    </div>

    {{-- ── Article Body ─────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" data-aos="fade-up" data-aos-duration="500">
        <div class="grid lg:grid-cols-4 gap-10">

            {{-- ── Main Content ──────────────────────────────── --}}
            <div class="lg:col-span-3">
              <div class="fi-card overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,#d97706,#fbbf24 60%,transparent)"></div>
                <div class="p-6 sm:p-8 lg:p-10">

                {{-- Meta bar --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3 pb-6 mb-8 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                             style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            {{ $post->author_initials }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $post->author }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider">Penulis</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->formatted_date }}</time>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $post->read_time }} menit baca</span>
                    </div>
                </div>

                {{-- Excerpt --}}
                @if($post->excerpt)
                    <p class="text-base font-medium leading-relaxed mb-8 p-5 rounded-xl border-l-4 border-amber-400 bg-amber-50 text-amber-900">
                        {{ $post->excerpt }}
                    </p>
                @endif

                {{-- Article content --}}
                <div class="article-prose" itemprop="articleBody">
                    {!! $post->content !!}
                </div>

                {{-- Konten tambahan (blocks) --}}
                @include('partials.content-blocks', ['blocks' => $post->blocks, 'title' => $post->title])

                {{-- Share buttons --}}
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Bagikan artikel ini</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="https://wa.me/?text={{ urlencode($post->title.' - '.$post->canonical_url) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn" style="background:#25d366">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->canonical_url) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn" style="background:#1877f2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode($post->canonical_url) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn" style="background:#000">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.63 5.905-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            X / Twitter
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ $post->canonical_url }}').then(()=>{ this.textContent='✓ Disalin!'; setTimeout(()=>{ this.textContent='Salin Link'; },2000); })"
                                class="share-btn" style="background:#d97706">
                            Salin Link
                        </button>
                    </div>
                </div>
                </div>
              </div>
            </div>

            {{-- ── Sidebar ────────────────────────────────────── --}}
            <aside class="lg:col-span-1">
                <div class="sidebar-sticky space-y-4">

                    {{-- Category info --}}
                    <div class="fi-card p-5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-3">Kategori</div>
                        <a href="{{ route('blog.index', ['category' => $post->category]) }}"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-800 hover:underline transition-colors">
                            <span class="w-6 h-6 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-xs">📂</span>
                            {{ $post->category }}
                        </a>
                    </div>

                    {{-- Table of contents --}}
                    @php
                        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $post->content, $headings);
                    @endphp
                    @if(count($headings[1]) > 0)
                        <div class="fi-card p-5">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-3">Daftar Isi</div>
                            <ol class="space-y-0.5">
                                @foreach($headings[1] as $i => $heading)
                                    <li class="toc-link">
                                        <span class="toc-num">{{ $i + 1 }}.</span>
                                        <span>{{ strip_tags($heading) }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    {{-- Back to blog --}}
                    <a href="{{ route('blog.index') }}" class="btn-outline w-full justify-center text-xs group">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Semua Artikel
                    </a>
                </div>
            </aside>
        </div>
    </div>

    {{-- ── Q&A Section ──────────────────────────────────────── --}}
    @if(feature_enabled('pertanyaan'))
    <section class="border-t py-14" style="border-color:#f3f4f6;background:#f9fafb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 mb-2">
                <span class="w-4 h-px bg-amber-500 inline-block"></span>
                Diskusi
            </span>
            <h2 class="text-xl font-extrabold mb-8 text-gray-900">Tanya Jawab Artikel Ini</h2>

            {{-- Q&A List --}}
            @if($postQuestions->isNotEmpty())
            <div class="space-y-4 mb-10">
                @foreach($postQuestions as $qa)
                <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-extrabold text-xs text-white"
                             style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            {{ strtoupper(substr($qa->display_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-sm text-gray-900">{{ $qa->display_name }}</span>
                                <span class="text-xs text-gray-400">{{ $qa->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $qa->question }}</p>
                        </div>
                    </div>
                    @if($qa->answer)
                    <div class="flex items-start gap-3 pl-3 pt-4 border-t border-gray-100">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                             style="background:#fef3c7">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-sm text-amber-700">{{ setting('site_name', config('app.name')) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Dijawab
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($qa->answer)) !!}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Submit Form --}}
            @if($post->allow_questions)
                @auth
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <h3 class="font-bold text-base text-gray-900 mb-1">Punya Pertanyaan?</h3>
                    <p class="text-xs text-gray-400 mb-5">Pertanyaan yang dijawab akan ditampilkan di sini.</p>

                    @if(session('success') && request()->is('blog/*'))
                    <div class="flex items-center gap-2 p-3 rounded-lg mb-5 bg-amber-50 border border-amber-200">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-medium text-amber-700">{{ session('success') }}</p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('questions.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <div class="flex items-center gap-2.5 mb-1">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                 class="w-7 h-7 rounded-full object-cover">
                            <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                            <textarea name="question" rows="3" required
                                      @class(['w-full px-3 py-2 text-sm rounded-lg border bg-gray-50 focus:outline-none focus:ring-2 transition-colors', 'border-red-400 focus:border-red-400 focus:ring-red-100' => $errors->has('question'), 'border-gray-200 focus:border-amber-400 focus:ring-amber-100' => !$errors->has('question')])
                                      placeholder="Tulis pertanyaan Anda tentang artikel ini...">{{ old('question') }}</textarea>
                            @error('question')<p class="text-xs mt-1 text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous'))
                                   class="rounded border-gray-300 text-amber-600 focus:ring-amber-400">
                            Tampilkan sebagai anonim (sembunyikan nama saya)
                        </label>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold text-white transition-opacity hover:opacity-80"
                                style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim Pertanyaan
                        </button>
                    </form>
                </div>
                @else
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm text-center">
                    <p class="text-sm text-gray-600 mb-4">Anda harus masuk terlebih dahulu untuk mengajukan pertanyaan.</p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold text-white transition-opacity hover:opacity-80"
                       style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                        Masuk untuk Bertanya
                    </a>
                </div>
                @endauth
            @else
            <div class="bg-gray-50 rounded-xl border border-gray-100 p-6 text-center">
                <p class="text-sm text-gray-500">Kolom tanya jawab untuk artikel ini ditutup.</p>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ── Komentar ─────────────────────────────────────────── --}}
    <section id="komentar" class="border-t py-14" style="border-color:#f3f4f6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 mb-2">
                <span class="w-4 h-px bg-amber-500 inline-block"></span>
                Komentar
            </span>
            <h2 class="text-xl font-extrabold mb-8 text-gray-900">
                Komentar
                <span class="text-gray-400 font-semibold">({{ $comments->count() }})</span>
            </h2>

            {{-- Daftar komentar --}}
            @if($comments->isNotEmpty())
            <div class="space-y-4 mb-10">
                @foreach($comments as $comment)
                <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <img src="{{ $comment->author_avatar_url }}"
                             alt="{{ $comment->author_name }}"
                             class="shrink-0 w-8 h-8 rounded-full object-cover"
                             loading="lazy">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-sm text-gray-900">{{ $comment->author_name }}</span>
                                @if($comment->is_guest)
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">Tamu</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    </div>

                    {{-- Balasan admin --}}
                    @if($comment->admin_reply)
                    <div class="flex items-start gap-3 pl-3 mt-4 pt-4 border-t border-gray-100">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                             style="background:#fef3c7">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-sm text-amber-700">{{ setting('site_name', config('app.name')) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                                    Admin
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($comment->admin_reply)) !!}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 mb-10">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
            @endif

            {{-- Form komentar --}}
            @if($post->allow_comments)
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <h3 class="font-bold text-base text-gray-900 mb-5">Tinggalkan Komentar</h3>

                    @if(session('comment_success'))
                    <div class="flex items-center gap-2 p-3 rounded-lg mb-5 bg-amber-50 border border-amber-200">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-medium text-amber-700">{{ session('comment_success') }}</p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-4">
                        @csrf
                        @auth
                        <div class="flex items-center gap-2.5 mb-1">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                 class="w-7 h-7 rounded-full object-cover">
                            <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                        @else
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   @class(['w-full px-3 py-2 text-sm rounded-lg border bg-gray-50 focus:outline-none focus:ring-2 transition-colors', 'border-red-400 focus:border-red-400 focus:ring-red-100' => $errors->has('name'), 'border-gray-200 focus:border-amber-400 focus:ring-amber-100' => !$errors->has('name')])
                                   placeholder="Nama Anda">
                            @error('name')<p class="text-xs mt-1 text-red-500">{{ $message }}</p>@enderror
                        </div>
                        @endauth
                        <div>
                            <textarea name="body" rows="3" required
                                      @class(['w-full px-3 py-2 text-sm rounded-lg border bg-gray-50 focus:outline-none focus:ring-2 transition-colors', 'border-red-400 focus:border-red-400 focus:ring-red-100' => $errors->has('body'), 'border-gray-200 focus:border-amber-400 focus:ring-amber-100' => !$errors->has('body')])
                                      placeholder="Tulis komentar Anda...">{{ old('body') }}</textarea>
                            @error('body')<p class="text-xs mt-1 text-red-500">{{ $message }}</p>@enderror
                        </div>
                        @guest
                        <x-spam-guard />
                        @if(feature_enabled('login_register'))
                        <p class="text-xs text-gray-400">
                            Sudah punya akun? <a href="{{ route('login') }}" class="text-amber-600 font-semibold hover:underline">Masuk</a> untuk berkomentar dengan nama akun Anda.
                        </p>
                        @endif
                        @endguest
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold text-white transition-opacity hover:opacity-80"
                                style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim Komentar
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 rounded-xl border border-gray-100 p-6 text-center">
                    <p class="text-sm text-gray-500">Kolom komentar untuk artikel ini ditutup.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ── Related Articles ─────────────────────────────────── --}}
    @if($related->isNotEmpty())
        <section class="border-t py-14" style="border-color:#f3f4f6;background:#f9fafb">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 mb-2">
                    <span class="w-4 h-px bg-amber-500 inline-block"></span>
                    Selanjutnya
                </span>
                <h2 class="text-xl font-extrabold mb-8 text-gray-900">Artikel Terkait</h2>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($related as $rel)
                        <article class="related-card group flex flex-col"
                                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                            <a href="{{ route('blog.show', $rel->slug) }}" class="relative h-44 block overflow-hidden">
                                <img src="{{ $rel->thumbnail_url }}"
                                     alt="{{ $rel->title }}"
                                     class="related-img w-full h-full object-cover"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-linear-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute top-3 left-3">
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-sm text-amber-700">
                                        {{ $rel->category }}
                                    </span>
                                </div>
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <time class="text-[11px] mb-2 block text-gray-400" datetime="{{ $rel->published_at?->toIso8601String() }}">
                                    {{ $rel->formatted_date }}
                                </time>
                                <h3 class="font-bold text-sm leading-snug line-clamp-2 flex-1 text-gray-900 hover:text-amber-700 transition-colors mb-3">
                                    <a href="{{ route('blog.show', $rel->slug) }}">{{ $rel->title }}</a>
                                </h3>
                                <a href="{{ route('blog.show', $rel->slug) }}"
                                   class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 group/link">
                                    Baca artikel
                                    <svg class="w-3 h-3 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── Ajakan menjadi author (tamu) ───────────── --}}
    @guest
        <section class="py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-author-cta />
            </div>
        </section>
    @endguest

@endsection
