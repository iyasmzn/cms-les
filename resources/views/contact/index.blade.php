@extends('layouts.public')

@push('head')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #082828 0%, #08484A 60%, #0a5a5c 100%);
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 60% at 80% 20%, rgba(255,255,255,.06) 0%, transparent 55%),
            radial-gradient(ellipse 40% 40% at 10% 80%, rgba(255,255,255,.04) 0%, transparent 50%);
    }
    .contact-item-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: 1rem;
        border: 1.5px solid var(--border);
        background: var(--card);
        transition: border-color .2s, transform .2s;
    }
    .contact-item-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }
    .contact-icon {
        flex-shrink: 0;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: .75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: rgba(8,72,74,.08);
    }
    .map-container {
        border-radius: 1rem;
        overflow: hidden;
        border: 1.5px solid var(--border);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────── --}}
<section class="contact-hero -mt-17 pt-36 pb-20 sm:pt-44 sm:pb-28">
    <x-hero-geo />
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-6"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            📍 Kontak
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
            Hubungi Kami
        </h1>
        <p class="text-lg max-w-xl mx-auto" style="color:rgba(255,255,255,.75)">
            Kami siap membantu. Temukan informasi kontak dan cara terbaik menghubungi {{ setting('site_name', config('app.name')) }}.
        </p>
    </div>
</section>

{{-- ── Content ──────────────────────────────────────────── --}}
<section class="py-16 sm:py-20" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($contactItems->isNotEmpty())
        <div class="grid sm:grid-cols-2 gap-4 mb-14" data-aos="fade-up">
            @foreach($contactItems as $item)
            <div>
                @if($item->link)
                    <a href="{{ $item->link }}" target="_blank" rel="noopener noreferrer" class="contact-item-card block">
                        <div class="contact-icon">
                            @if($url = icon_url($item->icon_image))
                                <img src="{{ $url }}" alt="{{ $item->label }}" loading="lazy" style="width:1.5rem;height:1.5rem;object-fit:contain">
                            @else
                                {{ $item->icon }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold mb-0.5" style="color:var(--muted)">{{ $item->label }}</p>
                            <p class="text-sm font-bold leading-snug" style="color:var(--text)">{{ $item->value }}</p>
                        </div>
                    </a>
                @else
                    <div class="contact-item-card">
                        <div class="contact-icon">
                            @if($url = icon_url($item->icon_image))
                                <img src="{{ $url }}" alt="{{ $item->label }}" loading="lazy" style="width:1.5rem;height:1.5rem;object-fit:contain">
                            @else
                                {{ $item->icon }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold mb-0.5" style="color:var(--muted)">{{ $item->label }}</p>
                            <p class="text-sm font-bold leading-snug" style="color:var(--text)">{{ $item->value }}</p>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Maps Embed --}}
        @php $mapsUrl = setting('contact_map_url') @endphp
        @if($mapsUrl)
        <div data-aos="fade-up" data-aos-delay="100">
            <h2 class="text-xl font-extrabold mb-4" style="color:var(--text)">Lokasi Kami</h2>
            <div class="map-container">
                <iframe
                    src="{{ $mapsUrl }}"
                    width="100%"
                    height="400"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        @elseif(feature_enabled('pertanyaan'))
        {{-- Tanya Jawab CTA --}}
        <div class="text-center py-12 rounded-2xl border-2 border-dashed" style="border-color:var(--border)" data-aos="fade-up">
            <div class="text-5xl mb-4">💬</div>
            <h3 class="text-lg font-extrabold mb-2" style="color:var(--text)">Punya Pertanyaan Lain?</h3>
            <p class="text-sm mb-6" style="color:var(--muted)">Kirim pertanyaan Anda di halaman Tanya Jawab dan tim kami akan segera menjawab.</p>
            <a href="{{ route('questions.index') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white transition-opacity hover:opacity-80"
               style="background:var(--primary)">
                Ke Halaman Tanya Jawab
            </a>
        </div>
        @endif

    </div>
</section>

@endsection
