@extends('layouts.public')

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
    .dl-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0a1628 100%);
        position: relative;
        overflow: hidden;
    }
    .dl-hero::before {
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
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
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

    /* File type icon badge */
    .file-icon {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .625rem;
        font-weight: 900;
        letter-spacing: .05em;
        flex-shrink: 0;
    }
    .file-icon.pdf  { background: #fef2f2; color: #b91c1c; }
    .file-icon.doc  { background: #eff6ff; color: #1d4ed8; }
    .file-icon.xls  { background: #f0fdf4; color: #15803d; }
    .file-icon.zip  { background: #fefce8; color: #a16207; }
    .file-icon.img  { background: #fdf4ff; color: #7e22ce; }
    .file-icon.file { background: var(--bg);  color: var(--muted); }

    /* Download card */
    .dl-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.125rem;
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }
    .dl-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 48px rgba(0,0,0,.1);
        border-color: var(--primary-200);
    }
    .dl-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.125rem;
        border-radius: .875rem;
        font-size: .8125rem;
        font-weight: 700;
        background: var(--primary);
        color: #fff;
        transition: background .2s, box-shadow .2s;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 3px 12px color-mix(in oklab, var(--primary) 30%, transparent);
    }
    .dl-btn:hover {
        background: var(--primary-700);
        box-shadow: 0 5px 16px color-mix(in oklab, var(--primary) 40%, transparent);
    }
    .search-input:focus {
        box-shadow: 0 0 0 3px color-mix(in oklab, var(--primary) 18%, transparent);
    }
    .filter-chip {
        border: 1.5px solid var(--border);
        border-radius: .875rem;
        padding: .6rem 1.125rem;
        font-size: .875rem;
        color: var(--muted);
        background: var(--card);
        transition: border-color .2s, color .2s, box-shadow .2s;
    }
    .filter-chip:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px color-mix(in oklab, var(--primary) 18%, transparent);
    }
</style>
@endpush

@section('content')

    {{-- ── Page Hero ──────────────────────────────────────────── --}}
    <section class="dl-hero -mt-17 pt-30 pb-14 sm:pt-36 sm:pb-20">
        <x-hero-geo />
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Mobile breadcrumb --}}
            <nav aria-label="Breadcrumb" class="md:hidden flex items-center gap-1.5 text-xs mb-5 text-white/50">
                <a href="/" class="hover:text-white transition-colors">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-amber-400 font-medium">Unduhan</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-400 mb-3" data-aos="fade-up">
                        <span class="w-4 h-px bg-amber-400 inline-block"></span>
                        Dokumen & Berkas
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight mb-3" data-aos="fade-up" data-aos-delay="60">
                        Unduhan
                    </h1>
                    <p class="text-white/60 text-sm sm:text-base max-w-lg leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                        Formulir, surat edaran, pengumuman, dan dokumen resmi {{ setting('site_name', config('app.name')) }} tersedia untuk diunduh.
                    </p>
                </div>
                @if($downloads->total() > 0)
                    <div class="flex items-center gap-4 shrink-0" data-aos="fade-up" data-aos-delay="140">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-amber-400">{{ $downloads->total() }}</div>
                            <div class="text-xs text-white/50 font-medium uppercase tracking-wider">Dokumen</div>
                        </div>
                        @if($categories->isNotEmpty())
                            <div class="w-px h-10 bg-white/10"></div>
                            <div class="text-center">
                                <div class="text-3xl font-extrabold text-amber-400">{{ $categories->count() }}</div>
                                <div class="text-xs text-white/50 font-medium uppercase tracking-wider">Kategori</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- ── Search Bar ──────────────────────────────────────── --}}
        <form method="GET" action="{{ route('downloads.index') }}"
              class="fi-card p-4 mb-6 flex flex-col sm:flex-row gap-3" data-aos="fade-up">

            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari judul dokumen..."
                       class="search-input w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-400 transition"
                       style="background:#fff;color:#030712">
                @if($category)
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
            </div>

            <button type="submit" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>

            @if($search || $category)
                <a href="{{ route('downloads.index') }}" class="btn-outline shrink-0 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
            @endif
        </form>

        {{-- ── Category Filter Pills ────────────────────────── --}}
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8" data-aos="fade-up">
                <a href="{{ route('downloads.index', array_filter(['search' => $search])) }}"
                   class="cat-pill {{ ! $category ? 'active' : '' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('downloads.index', array_filter(['search' => $search, 'category' => $cat])) }}"
                       class="cat-pill {{ $category === $cat ? 'active' : '' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── Active filter summary ────────────────────────── --}}
        @if($search || $category)
            <p class="text-xs mb-5 text-gray-500">
                Menampilkan <strong class="text-amber-700">{{ $downloads->total() }}</strong> dokumen
                @if($search) yang mengandung "<strong class="text-amber-700">{{ $search }}</strong>" @endif
                @if($category) di kategori "<strong class="text-amber-700">{{ $category }}</strong>" @endif
            </p>
        @endif

        {{-- ── Download List ────────────────────────────────── --}}
        @if($downloads->isEmpty())
            <div class="text-center py-24" data-aos="fade-up">
                <div class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-5 text-4xl">📂</div>
                <p class="text-base font-semibold text-gray-700 mb-1">Tidak ada dokumen ditemukan</p>
                <p class="text-sm text-gray-400 mb-5">Coba ubah kata kunci atau pilih kategori lain.</p>
                <a href="{{ route('downloads.index') }}" class="btn-outline inline-flex">Lihat Semua Dokumen</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($downloads as $item)
                    @php
                        $icon = $item->file_icon;
                        $iconLabels = [
                            'pdf'  => 'PDF',
                            'doc'  => 'DOC',
                            'xls'  => 'XLS',
                            'zip'  => 'ZIP',
                            'img'  => 'IMG',
                            'file' => 'FILE',
                        ];
                        $iconLabel = $iconLabels[$icon] ?? 'FILE';
                    @endphp
                    <article class="dl-card"
                             data-aos="fade-up"
                             data-aos-delay="{{ ($loop->index % 3) * 60 }}">

                        {{-- File type icon --}}
                        <div class="file-icon {{ $icon }}" aria-hidden="true">
                            {{ $iconLabel }}
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h2 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2">
                                    {{ $item->title }}
                                </h2>
                            </div>

                            @if($item->description)
                                <p class="text-xs text-gray-500 line-clamp-2 mb-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                            @endif

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-3">
                                @if($item->category)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5">
                                        {{ $item->category }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-gray-400">{{ $item->file_size_label }}</span>
                                <span class="text-[11px] text-gray-400">· {{ number_format($item->download_count) }}× diunduh</span>
                            </div>

                            <a href="{{ route('downloads.download', $item) }}"
                               class="dl-btn"
                               title="Unduh {{ $item->original_filename }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ── Pagination ───────────────────────────────── --}}
            @if($downloads->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $downloads->links('pagination::simple-tailwind') }}
                </div>
            @endif
        @endif

    </div>

@endsection
