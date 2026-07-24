<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ── Core SEO ────────────────────────────────────────── --}}
    <title>{{ $seo['title'] ?? setting('site_name', config('app.name')) }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">

    {{-- ── Favicon ─────────────────────────────────────────── --}}
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    {{-- ── Open Graph ──────────────────────────────────────── --}}
    <meta property="og:type"        content="{{ $seo['og_type'] ?? 'website' }}">
    <meta property="og:title"       content="{{ $seo['title'] ?? setting('site_name', config('app.name')) }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:url"         content="{{ $seo['canonical'] ?? url()->current() }}">
    <meta property="og:site_name"   content="{{ setting('site_name', config('app.name')) }}">
    @if(!empty($seo['og_image']))
        <meta property="og:image"        content="{{ $seo['og_image'] }}">
        <meta property="og:image:width"  content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt"    content="{{ $seo['title'] ?? setting('site_name', config('app.name')) }}">
    @endif
    @if(!empty($seo['published']))
        <meta property="article:published_time" content="{{ $seo['published'] }}">
    @endif
    @if(!empty($seo['author']))
        <meta property="article:author" content="{{ $seo['author'] }}">
    @endif
    @if(!empty($seo['category']))
        <meta property="article:section" content="{{ $seo['category'] }}">
    @endif

    {{-- ── Twitter Card ────────────────────────────────────── --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $seo['title'] ?? setting('site_name', config('app.name')) }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['og_image']))
        <meta name="twitter:image" content="{{ $seo['og_image'] }}">
    @endif

    {{-- ── Structured Data (JSON-LD) ──────────────────────── --}}
    @stack('structured-data')

    {{-- ── Fonts & Assets ─────────────────────────────────── --}}
    @fonts

    {{-- Dynamic Google Font loading based on admin setting --}}
    @php
        $font = resolved_font();
    @endphp
    @if($font['href'])
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="{{ $font['href'] }}">
    @endif

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <style>
        /* ── Design tokens — Apple-inspired ─────────────────────── */
        :root {
            --bg:     #f5f5f7;
            --bg-alt: #ffffff;
            --card:   #ffffff;
            --border: rgba(0,0,0,.08);
            --text:   #1d1d1f;
            --muted:  #6e6e73;

            /* Override Tailwind's baked font token so the admin-selected font applies everywhere */
            --font-sans: {!! $font['family'] !!};
            --default-font-family: {!! $font['family'] !!};

            --primary: {{ setting('theme_primary_color', '#08484A') }};

            --primary-50:  color-mix(in oklab, var(--primary)  8%, white);
            --primary-100: color-mix(in oklab, var(--primary) 15%, white);
            --primary-200: color-mix(in oklab, var(--primary) 28%, white);
            --primary-300: color-mix(in oklab, var(--primary) 45%, white);
            --primary-400: color-mix(in oklab, var(--primary) 68%, white);
            --primary-500: color-mix(in oklab, var(--primary) 85%, white);
            --primary-600: var(--primary);
            --primary-700: color-mix(in oklab, var(--primary) 78%, black);
            --primary-800: color-mix(in oklab, var(--primary) 58%, black);
            --primary-900: color-mix(in oklab, var(--primary) 42%, black);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: {!! $font['family'] !!};
            -webkit-font-smoothing: antialiased;
        }

        /* ── Cards — Apple large radius ─────────────────────────── */
        .fi-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            transition: box-shadow .3s ease, transform .3s ease, border-color .3s ease;
        }
        .fi-card-hover:hover {
            box-shadow: 0 20px 60px rgba(0,0,0,.12);
            transform: translateY(-3px);
            border-color: var(--primary-200);
        }

        /* ── Buttons ─────────────────────────────────────────────── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .75rem 1.75rem;
            border-radius: .875rem;
            font-size: .9375rem; font-weight: 600;
            background: var(--primary); color: #fff;
            transition: background .2s, box-shadow .2s, transform .15s;
            box-shadow: 0 4px 16px color-mix(in oklab, var(--primary) 35%, transparent);
        }
        .btn-primary:hover {
            background: var(--primary-700);
            box-shadow: 0 6px 24px color-mix(in oklab, var(--primary) 45%, transparent);
            transform: translateY(-1px);
        }
        .btn-outline {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .75rem 1.75rem;
            border-radius: .875rem;
            font-size: .9375rem; font-weight: 600;
            border: 1.5px solid var(--border);
            color: var(--text); background: var(--card);
            transition: border-color .2s, color .2s, box-shadow .2s, transform .15s;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 4px 16px rgba(0,0,0,.06);
            transform: translateY(-1px);
        }

        /* ── Labels & badges ─────────────────────────────────────── */
        .fi-label {
            font-size: .6875rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--primary);
        }

        /* ── Accent bar ──────────────────────────────────────────── */
        .amber-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--primary), color-mix(in oklab, var(--primary) 55%, white) 60%, transparent);
        }

        /* ── Prose content ───────────────────────────────────────── */
        .prose h2 { font-size: 1.375rem; font-weight: 700; margin-top: 2rem; margin-bottom: .875rem; color: var(--text); }
        .prose h3 { font-size: 1.125rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: .625rem; color: var(--text); }
        .prose p  { margin-bottom: 1.125rem; line-height: 1.85; color: var(--muted); }
        .prose ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; color: var(--muted); }
        .prose ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; color: var(--muted); }
        .prose li { margin-bottom: .5rem; line-height: 1.7; }
        .prose blockquote { border-left: 3px solid var(--primary); padding-left: 1.25rem; margin: 1.75rem 0; color: var(--muted); font-style: italic; }
        .prose a  { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
        .prose strong { color: var(--text); }
    </style>

    @stack('head')
</head>

<body class="min-h-screen antialiased overflow-x-clip">

    {{-- Navbar — transparent over hero, solid on scroll --}}
    <x-navbar />

    {{-- ── Page Content ─────────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

    {{-- ── Footer ───────────────────────────────────────────── --}}
    <footer style="background:#111827;border-top:1px solid rgba(255,255,255,.07)">

        {{-- Top accent bar --}}
        <div style="height:3px;background:linear-gradient(90deg,var(--primary),color-mix(in oklab,var(--primary) 50%,white) 60%,transparent)"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4 mb-12">

                {{-- ── Col 1: Brand ── --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        @if(setting('site_logo'))
                            <img src="{{ asset('storage/'.setting('site_logo')) }}"
                                 alt="{{ setting('site_name', config('app.name')) }}"
                                 class="w-10 h-10 rounded-2xl object-contain">
                        @else
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shadow-lg"
                                 style="background:var(--primary)">
                                <span class="text-white font-extrabold text-base">
                                    {{ strtoupper(substr(setting('site_name', config('app.name', 'S')), 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <div class="font-bold text-white text-sm leading-tight">
                                {{ setting('site_name', config('app.name')) }}
                            </div>
                            <div class="text-[10px] font-semibold uppercase tracking-widest mt-0.5"
                                 style="color:color-mix(in oklab,var(--primary) 80%,white)">
                                {{ setting('site_tagline', 'Unggul · Berkarakter') }}
                            </div>
                        </div>
                    </div>

                    @if(setting('site_description'))
                    <p class="text-xs leading-relaxed mb-5 text-white/45 max-w-xs">
                        {{ Str::limit(setting('site_description'), 120) }}
                    </p>
                    @endif

                    {{-- Social icons --}}
                    <div class="flex gap-2">
                        @if(setting('social_facebook'))
                        <a href="{{ setting('social_facebook') }}" target="_blank" rel="noopener"
                           title="Facebook"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        @endif
                        @if(setting('social_instagram'))
                        <a href="{{ setting('social_instagram') }}" target="_blank" rel="noopener"
                           title="Instagram"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                        @endif
                        @if(setting('social_youtube'))
                        <a href="{{ setting('social_youtube') }}" target="_blank" rel="noopener"
                           title="YouTube"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                            </svg>
                        </a>
                        @endif
                        @if(setting('social_tiktok'))
                        <a href="{{ setting('social_tiktok') }}" target="_blank" rel="noopener"
                           title="TikTok"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>
                        @endif
                        @if(setting('social_telegram'))
                        <a href="{{ setting('social_telegram') }}" target="_blank" rel="noopener"
                           title="Telegram"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </a>
                        @endif
                        @if(setting('social_whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('social_whatsapp')) }}"
                           target="_blank" rel="noopener" title="WhatsApp"
                           class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                           style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- ── Col 2: Navigasi ── --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4"
                        style="color:color-mix(in oklab,var(--primary) 80%,white)">Navigasi</h4>
                    <ul class="space-y-2.5">
                        @php
                            $pubNavItems = collect(json_decode(setting('nav_items', ''), true) ?: [
                                ['label' => 'Beranda',  'url' => '/',         'target' => '_self', 'is_active' => true],
                                ['label' => 'Profil',   'url' => '#profil',   'target' => '_self', 'is_active' => true],
                                ['label' => 'SPMB',     'url' => '#spmb',     'target' => '_self', 'is_active' => true],
                                ['label' => 'Akademik', 'url' => '#akademik', 'target' => '_self', 'is_active' => true],
                                ['label' => 'Guru',     'url' => '/guru',     'target' => '_self', 'is_active' => true],
                                ['label' => 'Blog',     'url' => '/blog',     'target' => '_self', 'is_active' => true],
                                ['label' => 'Kontak',   'url' => '#kontak',   'target' => '_self', 'is_active' => true],
                            ])->where('is_active', true)->values();
                        @endphp
                        @foreach($pubNavItems as $item)
                        <li>
                            <a href="{{ str_starts_with($item['url'], '#') ? '/'.$item['url'] : $item['url'] }}"
                               target="{{ $item['target'] ?? '_self' }}"
                               class="text-sm text-white/50 hover:text-white transition-colors flex items-center gap-2 group">
                                <span class="w-1 h-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                                      style="background:var(--primary)"></span>
                                {{ $item['label'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ── Col 3: Layanan ── --}}
                @php
                    $footerServiceLinks = collect(json_decode(setting('footer_service_links', ''), true) ?: [
                        ['label' => 'Toko Buku',       'url' => '/buku',      'feature' => 'toko', 'is_active' => true],
                        ['label' => 'Daftar Santri',   'url' => '/ppdb',      'feature' => '',     'is_active' => true],
                        ['label' => 'Blog & Berita',   'url' => '/blog',      'feature' => '',     'is_active' => true],
                        ['label' => 'Unduhan',         'url' => '/unduhan',   'feature' => '',       'is_active' => true],
                        ['label' => 'Tenaga Pendidik', 'url' => '/guru',      'feature' => '',       'is_active' => true],
                        ['label' => 'Donasi',          'url' => '/donasi',    'feature' => 'donasi', 'is_active' => true],
                        ['label' => 'Keranjang',       'url' => '/keranjang', 'feature' => 'toko',   'is_active' => true],
                    ])
                        ->filter(fn ($l) => ($l['is_active'] ?? true) && (blank($l['feature'] ?? '') || feature_enabled($l['feature'])))
                        ->values();
                @endphp
                @if(setting_bool('footer_services_enabled', true) && $footerServiceLinks->isNotEmpty())
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4"
                        style="color:color-mix(in oklab,var(--primary) 80%,white)">{{ setting('footer_services_title', 'Layanan') }}</h4>
                    <ul class="space-y-2.5">
                        @foreach($footerServiceLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                               class="text-sm text-white/50 hover:text-white transition-colors flex items-center gap-2 group">
                                <span class="w-1 h-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                                      style="background:var(--primary)"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- ── Col 4: Kontak ── --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4"
                        style="color:color-mix(in oklab,var(--primary) 80%,white)">Kontak</h4>
                    <ul class="space-y-3">
                        @if(setting('contact_address'))
                        <li class="flex gap-3 text-xs text-white/50 leading-relaxed">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:color-mix(in oklab,var(--primary) 70%,white)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ setting('contact_address') }}</span>
                        </li>
                        @endif
                        @if(setting('contact_phone'))
                        <li class="flex gap-3 text-xs text-white/50">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:color-mix(in oklab,var(--primary) 70%,white)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ setting('contact_phone') }}</span>
                        </li>
                        @endif
                        @if(setting('contact_email'))
                        <li class="flex gap-3 text-xs text-white/50">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:color-mix(in oklab,var(--primary) 70%,white)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ setting('contact_email') }}</span>
                        </li>
                        @endif
                        @if(setting('contact_hours'))
                        <li class="flex gap-3 text-xs text-white/50">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:color-mix(in oklab,var(--primary) 70%,white)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ setting('contact_hours') }}</span>
                        </li>
                        @endif
                        @if(setting('shop_whatsapp') || setting('social_whatsapp'))
                        @php $waNum = preg_replace('/\D/', '', setting('shop_whatsapp', setting('social_whatsapp', ''))); @endphp
                        <li class="pt-1">
                            <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all hover:opacity-90 hover:scale-[1.02]"
                               style="background:var(--primary)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Hubungi via WhatsApp
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

            </div>

            {{-- Bottom bar --}}
            @php
                $footerCopyright = trim((string) setting('footer_copyright'));
                $footerCopyright = $footerCopyright !== ''
                    ? strtr($footerCopyright, [
                        '{tahun}' => date('Y'),
                        '{nama_situs}' => setting('site_name', config('app.name')),
                    ])
                    : '© '.date('Y').' '.setting('site_name', config('app.name')).'. Semua hak dilindungi.';
            @endphp
            <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-white/30">{{ $footerCopyright }}</p>
                @if(setting_bool('footer_credit_enabled', true) && filled(setting('footer_credit_text', 'Dibuat dengan penuh semangat')))
                <div class="flex items-center gap-1.5 text-white/20 text-xs">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ setting('footer_credit_text', 'Dibuat dengan penuh semangat') }}</span>
                </div>
                @endif
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init({ once: true, duration: 700, easing: 'ease-out-quart', offset: 60 });</script>

    @stack('scripts')

    <x-popup-overlay />
    <x-floating-buttons />
</body>
</html>
