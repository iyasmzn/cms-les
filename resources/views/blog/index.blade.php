@extends('layouts.public')

{{-- ── Structured Data (JSON-LD) ─────────────────────────── --}}
@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context'    => 'https://schema.org',
    '@type'       => 'CollectionPage',
    'name'        => $seo['title'],
    'description' => $seo['description'],
    'url'         => $seo['canonical'],
    'publisher'   => [
        '@type' => 'EducationalOrganization',
        'name'  => setting('site_name', config('app.name')),
        'url'   => url('/'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush


@push('head')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0c1a14 100%);
        position: relative;
        overflow: hidden;
    }
    .blog-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 10% 50%, rgba(217,119,6,.22) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 90% 10%, rgba(251,191,36,.1) 0%, transparent 50%);
    }

    /* Category filter pills */
    .cat-pill {
        font-size: .8125rem;
        font-weight: 600;
        padding: .5rem 1.125rem;
        border-radius: 9999px;
        border: 1.5px solid var(--border);
        color: var(--muted);
        background: var(--card);
        transition: all .2s;
        white-space: nowrap;
    }
    .cat-pill:hover {
        border-color: var(--primary-300);
        color: var(--primary-800);
        background: var(--primary-50);
    }
    .cat-pill.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    /* Featured post */
    .featured-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.5rem;
        overflow: hidden;
        transition: box-shadow .3s ease, transform .3s ease;
    }
    .featured-card:hover {
        box-shadow: 0 24px 60px rgba(0,0,0,.12);
        transform: translateY(-2px);
    }
    .featured-card:hover .featured-img {
        transform: scale(1.04);
    }
    .featured-img {
        transition: transform .7s ease;
    }

    /* Grid cards */
    .blog-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.5rem;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }
    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(0,0,0,.12);
        border-color: var(--primary-200);
    }
    .blog-card:hover .blog-card-img {
        transform: scale(1.06);
    }
    .blog-card-img {
        transition: transform .6s ease;
    }

    /* Author avatar */
    .author-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .6875rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .author-avatar.lg {
        width: 2.25rem;
        height: 2.25rem;
        font-size: .8125rem;
    }
</style>
@endpush

@section('content')

    {{-- ── Page Hero ──────────────────────────────────────────── --}}
    <section class="blog-hero -mt-17 pt-30 pb-14 sm:pt-36 sm:pb-20">
        <x-hero-geo />
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Mobile breadcrumb --}}
            <nav aria-label="Breadcrumb" class="md:hidden flex items-center gap-1.5 text-xs mb-5 text-white/50">
                <a href="/" class="hover:text-white transition-colors">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-amber-400 font-medium">Blog</span>
            </nav>

            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-400 mb-3" data-aos="fade-up">
                <span class="w-4 h-px bg-amber-400 inline-block"></span>
                Blog & Berita
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight mb-3" data-aos="fade-up" data-aos-delay="60">
                {{ $category ? "Kategori: {$category}" : 'Semua Artikel' }}
            </h1>
            <p class="text-white/60 text-sm sm:text-base max-w-lg leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                Informasi terkini, prestasi siswa, dan cerita inspiratif dari komunitas {{ setting('site_name', config('app.name')) }}.
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- ── Category Filter ─────────────────────────────── --}}
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8" data-aos="fade-up">
                <a href="{{ route('blog.index') }}" class="cat-pill {{ !$category ? 'active' : '' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.index', ['category' => $cat]) }}"
                       class="cat-pill {{ $category === $cat ? 'active' : '' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── Post Grid ───────────────────────────────────── --}}
        @if($posts->isEmpty())
            <div class="text-center py-24" data-aos="fade-up">
                <div class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-5 text-4xl">📭</div>
                <p class="text-base font-semibold text-gray-700 mb-1">Belum ada artikel</p>
                <p class="text-sm text-gray-400 mb-5">Belum ada artikel untuk kategori ini.</p>
                <a href="{{ route('blog.index') }}" class="btn-outline inline-flex">Lihat Semua Artikel</a>
            </div>
        @else
            {{-- Featured (first post) --}}
            @php $featured = $posts->first(); @endphp
            <article class="featured-card mb-8 group" itemscope itemtype="https://schema.org/Article"
                     data-aos="fade-up" data-aos-delay="50">
                <meta itemprop="author" content="{{ $featured->author }}">
                <meta itemprop="datePublished" content="{{ $featured->published_at?->toIso8601String() }}">
                <div class="grid lg:grid-cols-5">
                    <a href="{{ route('blog.show', $featured->slug) }}" class="lg:col-span-2 block h-60 lg:h-auto relative overflow-hidden">
                        <img src="{{ $featured->thumbnail_url }}"
                             alt="{{ $featured->title }}"
                             class="featured-img w-full h-full object-cover"
                             loading="eager"
                             itemprop="image">
                        <div class="absolute inset-0 bg-linear-to-t from-black/40 to-transparent"></div>
                        {{-- Featured label --}}
                        <div class="absolute top-4 left-4">
                            <span class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg bg-amber-500 text-white uppercase tracking-wider">
                                ✦ Unggulan
                            </span>
                        </div>
                    </a>
                    <div class="lg:col-span-3 p-7 lg:p-10 flex flex-col justify-center">
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $featured->category }}
                            </span>
                            <time class="text-xs text-gray-400" datetime="{{ $featured->published_at?->toIso8601String() }}">
                                {{ $featured->formatted_date }}
                            </time>
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $featured->read_time }} menit baca
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold leading-snug mb-3 text-gray-900" itemprop="headline">
                            <a href="{{ route('blog.show', $featured->slug) }}" class="hover:text-amber-700 transition-colors">
                                {{ $featured->title }}
                            </a>
                        </h2>
                        <p class="text-sm leading-relaxed mb-6 line-clamp-3 text-gray-500" itemprop="description">
                            {{ $featured->excerpt }}
                        </p>
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-2.5">
                                <div class="author-avatar lg">{{ $featured->author_initials }}</div>
                                <div>
                                    <div class="text-xs font-semibold text-gray-700">{{ $featured->author }}</div>
                                    <div class="text-[10px] text-gray-400">Penulis</div>
                                </div>
                            </div>
                            <a href="{{ route('blog.show', $featured->slug) }}" class="btn-primary text-xs group/btn">
                                Baca Selengkapnya
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Remaining grid --}}
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts->skip(1) as $post)
                    <article class="blog-card group flex flex-col"
                             itemscope itemtype="https://schema.org/Article"
                             data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <meta itemprop="author" content="{{ $post->author }}">
                        <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">

                        {{-- Thumbnail --}}
                        <a href="{{ route('blog.show', $post->slug) }}" class="relative h-48 block overflow-hidden">
                            <img src="{{ $post->thumbnail_url }}"
                                 alt="{{ $post->title }}"
                                 class="blog-card-img w-full h-full object-cover"
                                 loading="lazy"
                                 itemprop="image">
                            <div class="absolute inset-0 bg-linear-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute top-3 left-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-sm text-amber-700">
                                    {{ $post->category }}
                                </span>
                            </div>
                        </a>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-2 mb-3 text-[11px] text-gray-400">
                                <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->formatted_date }}</time>
                                <span class="w-1 h-1 rounded-full bg-gray-300 inline-block"></span>
                                <span>{{ $post->read_time }} mnt</span>
                            </div>

                            <h2 class="font-bold text-sm leading-snug mb-2 line-clamp-2 text-gray-900 hover:text-amber-700 transition-colors flex-1" itemprop="headline">
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h2>

                            <p class="text-xs leading-relaxed line-clamp-2 text-gray-400 mb-4" itemprop="description">
                                {{ $post->excerpt }}
                            </p>

                            <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <div class="author-avatar">{{ $post->author_initials }}</div>
                                    <span class="text-[11px] text-gray-500 truncate max-w-28">{{ $post->author }}</span>
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}"
                                   class="text-xs font-bold text-amber-600 hover:text-amber-700 shrink-0 flex items-center gap-1 group/link">
                                    Baca
                                    <svg class="w-3 h-3 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ── Pagination ─────────────────────────────── --}}
            @if($posts->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $posts->links('pagination::simple-tailwind') }}
                </div>
            @endif
        @endif

        {{-- ── Ajakan menjadi author (tamu) ───────────── --}}
        <div class="mt-16">
            <x-author-cta />
        </div>

    </div>

@endsection
