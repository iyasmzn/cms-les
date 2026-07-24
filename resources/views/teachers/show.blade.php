@extends('layouts.public')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context'  => 'https://schema.org',
    '@type'     => 'Person',
    'name'      => $teacher->name,
    'jobTitle'  => $teacher->position,
    'image'     => $teacher->photo_url,
    'url'       => $seo['canonical'],
    'telephone' => $teacher->phone ?: null,
    'email'     => $teacher->email ?: null,
    'worksFor'  => [
        '@type' => 'EducationalOrganization',
        'name'  => setting('site_name', config('app.name')),
        'url'   => url('/'),
    ],
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush


@push('head')
<style>
    .profile-photo-wrap {
        position: relative;
        overflow: hidden;
    }
    .profile-photo-wrap::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.55) 0%, rgba(0,0,0,.1) 50%, transparent 100%);
        pointer-events: none;
    }
    .detail-row {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        padding: .875rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-icon-wrap {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        background: #fffbeb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #d97706;
    }
    .contact-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .6rem 1.1rem;
        border-radius: .625rem;
        font-size: .8125rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        color: #374151;
        background: #fff;
        transition: all .15s;
    }
    .contact-btn:hover {
        border-color: #fcd34d;
        background: #fffbeb;
        color: #92400e;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(217,119,6,.15);
    }
    .contact-btn.wa:hover {
        border-color: #86efac;
        background: #f0fdf4;
        color: #166534;
        box-shadow: 0 4px 12px rgba(34,197,94,.15);
    }
</style>
@endpush

@section('content')

    {{-- ── Hero band — dark backing for the transparent navbar ──── --}}
    <div class="-mt-17" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0c1a14 100%);border-bottom:1px solid rgba(255,255,255,.08)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-5 sm:pt-28 sm:pb-6">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm" style="color:rgba(255,255,255,.6)">
                <a href="/" class="hover:text-white transition-colors">Beranda</a>
                <span class="opacity-50">/</span>
                <a href="{{ route('teachers.index') }}" class="hover:text-white transition-colors">Tenaga Pendidik</a>
                <span class="opacity-50">/</span>
                <span class="font-medium line-clamp-1" style="color:#fff">{{ $teacher->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- ── Main Card ────────────────────────────────────── --}}
        <div class="fi-card overflow-hidden" data-aos="fade-up" itemscope itemtype="https://schema.org/Person">

            <div class="grid md:grid-cols-5 gap-0">

                {{-- ── Left: Photo ──────────────────────────── --}}
                <div class="md:col-span-2 profile-photo-wrap" style="min-height:360px">
                    <img src="{{ $teacher->photo_url }}"
                         alt="{{ $teacher->name }}"
                         loading="eager"
                         class="w-full h-full object-cover absolute inset-0"
                         itemprop="image">

                    {{-- Info overlay at bottom (mobile) --}}
                    <div class="absolute bottom-0 inset-x-0 p-5 md:hidden" style="z-index:1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-amber-500 text-white mb-2">
                            {{ $teacher->position }}
                        </span>
                        <h1 class="text-xl font-extrabold text-white leading-snug" itemprop="name">
                            {{ $teacher->name }}
                        </h1>
                    </div>
                </div>

                {{-- ── Right: Info ───────────────────────────── --}}
                <div class="md:col-span-3 p-7 lg:p-10 flex flex-col justify-center">

                    {{-- Position badge (desktop) --}}
                    <span class="hidden md:inline-flex items-center gap-1.5 self-start px-3 py-1.5 rounded-full text-[11px] font-bold mb-4 bg-amber-50 text-amber-700 border border-amber-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0121 16.75c0 2.485-4.03 4.5-9 4.5s-9-2.015-9-4.5a12.083 12.083 0 012.84-5.172L12 14z"/>
                        </svg>
                        {{ $teacher->position }}
                    </span>

                    <h1 class="hidden md:block text-2xl sm:text-3xl font-extrabold leading-snug mb-6 text-gray-900" itemprop="name">
                        {{ $teacher->name }}
                    </h1>

                    {{-- Detail list --}}
                    <div>
                        @if($teacher->nip)
                            <div class="detail-row">
                                <div class="detail-icon-wrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-0.5">NIP</div>
                                    <div class="text-sm font-mono text-gray-700" itemprop="identifier">{{ $teacher->nip }}</div>
                                </div>
                            </div>
                        @endif

                        @if($teacher->subject)
                            <div class="detail-row">
                                <div class="detail-icon-wrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-0.5">Mata Pelajaran</div>
                                    <div class="text-sm text-gray-700" itemprop="jobTitle">{{ $teacher->subject }}</div>
                                </div>
                            </div>
                        @endif

                        @if($teacher->education)
                            <div class="detail-row">
                                <div class="detail-icon-wrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-0.5">Pendidikan</div>
                                    <div class="text-sm text-gray-700">{{ $teacher->education }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-icon-wrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-0.5">Status</div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                    Aktif Mengajar
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ── Kontak ──────────────────────────────── --}}
                    @if($teacher->phone || $teacher->email || $teacher->whatsapp)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-3">Hubungi</div>
                            <div class="flex flex-wrap gap-2">
                                @if($teacher->phone)
                                    <a href="tel:{{ $teacher->phone }}" class="contact-btn">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $teacher->phone }}
                                    </a>
                                @endif

                                @if($teacher->email)
                                    <a href="mailto:{{ $teacher->email }}" class="contact-btn" itemprop="email">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $teacher->email }}
                                    </a>
                                @endif

                                @if($teacher->whatsapp)
                                    <a href="https://wa.me/{{ $teacher->whatsapp }}" target="_blank" rel="noopener" class="contact-btn wa">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Back button --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <a href="{{ route('teachers.index') }}" class="btn-outline text-sm group">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Daftar Guru
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
