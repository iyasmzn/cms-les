@props([
    'code' => '500',
    'defaultTitle' => 'Terjadi Kesalahan',
    'defaultMessage' => 'Maaf, terjadi kesalahan yang tidak terduga.',
])

@php
    /**
     * Reads a setting defensively — error pages (especially 500/503) may render
     * while the database or cache is unavailable, so never let a lookup throw.
     */
    $safe = function (string $key, mixed $default = null) {
        try {
            return setting($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    };

    $title = $safe("error_{$code}_title") ?: $defaultTitle;
    $message = $safe("error_{$code}_message") ?: $defaultMessage;

    $showHome = (bool) $safe('error_show_home_button', true);
    $showBack = (bool) $safe('error_show_back_button', true);
    $supportEmail = $safe('error_support_email') ?: $safe('contact_email');

    $siteName = $safe('site_name', config('app.name'));
    $tagline = $safe('site_tagline');
    $logo = $safe('site_logo');
    $favicon = $safe('site_favicon');
    $primary = $safe('theme_primary_color', '#08484A');
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $code }} — {{ $title }} · {{ $siteName }}</title>

    @if($favicon)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    <style>
        :root {
            --bg:      #f5f5f7;
            --card:    #ffffff;
            --border:  rgba(0,0,0,.08);
            --text:    #1d1d1f;
            --muted:   #6e6e73;
            --primary: {{ $primary }};
            --primary-700: color-mix(in oklab, var(--primary) 78%, black);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: var(--bg);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow: hidden;
        }

        /* Soft radial glow behind the card */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(60rem 40rem at 50% -10%, color-mix(in oklab, var(--primary) 16%, transparent), transparent 70%),
                radial-gradient(40rem 30rem at 90% 110%, color-mix(in oklab, var(--primary) 10%, transparent), transparent 70%);
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 34rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.10);
            padding: 3rem 2.5rem;
            text-align: center;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .625rem;
            margin-bottom: 2rem;
        }
        .brand img,
        .brand .brand-mark {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: .875rem;
            object-fit: contain;
        }
        .brand-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            font-size: .95rem;
        }
        .brand-name { font-weight: 700; font-size: .9375rem; }

        .code {
            font-size: clamp(4.5rem, 18vw, 7.5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.04em;
            background: linear-gradient(160deg, var(--primary), color-mix(in oklab, var(--primary) 45%, #000));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .accent-bar {
            width: 3.5rem;
            height: 4px;
            margin: 1.25rem auto 1.75rem;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), color-mix(in oklab, var(--primary) 55%, white));
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -.01em;
            margin-bottom: .75rem;
        }
        .message {
            font-size: .9375rem;
            line-height: 1.7;
            color: var(--muted);
            max-width: 26rem;
            margin: 0 auto;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            border-radius: .875rem;
            font-size: .9375rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: transform .15s, box-shadow .2s, background .2s, border-color .2s, color .2s;
        }
        .btn svg { width: 1.05rem; height: 1.05rem; }
        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 16px color-mix(in oklab, var(--primary) 35%, transparent);
        }
        .btn-primary:hover {
            background: var(--primary-700);
            transform: translateY(-1px);
            box-shadow: 0 6px 24px color-mix(in oklab, var(--primary) 45%, transparent);
        }
        .btn-outline {
            background: var(--card);
            color: var(--text);
            border-color: var(--border);
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .support {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            font-size: .8125rem;
            color: var(--muted);
        }
        .support a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .support a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { padding: 2.25rem 1.5rem; }
        }
    </style>
</head>
<body>
    <main class="card">
        <a href="/" class="brand" aria-label="{{ $siteName }}">
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}">
            @else
                <span class="brand-mark">{{ strtoupper(substr($siteName ?: 'S', 0, 1)) }}</span>
            @endif
            <span class="brand-name">{{ $siteName }}</span>
        </a>

        <div class="code">{{ $code }}</div>
        <div class="accent-bar"></div>

        <h1 class="title">{{ $title }}</h1>
        <p class="message">{{ $message }}</p>

        <div class="actions">
            @if($showHome)
                <a href="/" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            @endif

            @if($showBack)
                <a href="javascript:history.back()" class="btn btn-outline">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Halaman Sebelumnya
                </a>
            @endif
        </div>

        @if($code === '500' && $supportEmail)
            <p class="support">
                Butuh bantuan? Hubungi kami di
                <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
            </p>
        @endif
    </main>
</body>
</html>
