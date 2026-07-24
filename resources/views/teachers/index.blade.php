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
    .teacher-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0c1a0f 100%);
        position: relative;
        overflow: hidden;
    }
    .teacher-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 80% 80% at 20% 50%, rgba(217,119,6,.25) 0%, transparent 60%),
                    radial-gradient(ellipse 60% 60% at 80% 20%, rgba(251,191,36,.12) 0%, transparent 50%);
    }
    .teacher-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.5rem;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }
    .teacher-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 60px rgba(0,0,0,.14);
        border-color: var(--primary-200);
    }
    .teacher-card:hover .teacher-photo {
        transform: scale(1.06);
    }
    .teacher-photo {
        transition: transform .6s ease;
    }
    .search-input:focus {
        box-shadow: 0 0 0 3px color-mix(in oklab, var(--primary) 20%, transparent);
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
    .contact-pill {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .65rem;
        border-radius: 9999px;
        font-size: .6875rem;
        font-weight: 600;
        border: 1px solid var(--border);
        color: var(--muted);
        background: var(--bg);
        transition: all .2s;
    }
    .contact-pill:hover {
        background: var(--primary-50);
        border-color: var(--primary-300);
        color: var(--primary-800);
    }
    .contact-pill.wa:hover {
        background: #f0fdf4;
        border-color: #86efac;
        color: #166534;
    }
</style>
@endpush

@section('content')

    {{-- ── Page Hero ──────────────────────────────────────────── --}}
    <section class="teacher-hero -mt-17 pt-30 pb-14 sm:pt-36 sm:pb-20">
        <x-hero-geo />
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Mobile breadcrumb --}}
            <nav aria-label="Breadcrumb" class="md:hidden flex items-center gap-1.5 text-xs mb-5 text-white/50">
                <a href="/" class="hover:text-white transition-colors">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-amber-400 font-medium">Tenaga Pendidik</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-400 mb-3" data-aos="fade-up">
                        <span class="w-4 h-px bg-amber-400 inline-block"></span>
                        Profil Sekolah
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight mb-3" data-aos="fade-up" data-aos-delay="60">
                        Tenaga Pendidik
                    </h1>
                    <p class="text-white/60 text-sm sm:text-base max-w-lg leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                        Guru-guru profesional dan berdedikasi yang membimbing siswa menuju prestasi terbaik di {{ setting('site_name', config('app.name')) }}.
                    </p>
                </div>
                @if($teachers->total() > 0)
                    <div class="flex items-center gap-4 shrink-0" data-aos="fade-up" data-aos-delay="140">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-amber-400">{{ $teachers->total() }}</div>
                            <div class="text-xs text-white/50 font-medium uppercase tracking-wider">Guru</div>
                        </div>
                        @if($positions->isNotEmpty())
                            <div class="w-px h-10 bg-white/10"></div>
                            <div class="text-center">
                                <div class="text-3xl font-extrabold text-amber-400">{{ $positions->count() }}</div>
                                <div class="text-xs text-white/50 font-medium uppercase tracking-wider">Jabatan</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- ── Search & Filter Bar ──────────────────────────── --}}
        <form method="GET" action="{{ route('teachers.index') }}"
              class="fi-card p-4 mb-8 flex flex-col sm:flex-row gap-3" data-aos="fade-up">

            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari nama guru atau mata pelajaran..."
                       class="search-input w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-400 transition"
                       style="background:#fff;color:#030712">
            </div>

            {{-- Institution filter --}}
            @if($institutions->isNotEmpty())
                <select name="institution" class="filter-chip sm:w-52 focus:outline-none">
                    <option value="">Semua Instansi</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" @selected((string) $institution === (string) $inst->id)>{{ $inst->name }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Position filter --}}
            @if($positions->isNotEmpty())
                <select name="position" class="filter-chip sm:w-52 focus:outline-none">
                    <option value="">Semua Jabatan</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}" @selected($position === $pos)>{{ $pos }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Sort --}}
            <select name="sort" class="filter-chip sm:w-44 focus:outline-none">
                <option value="default"   @selected($sort === 'default')>Urutan Default</option>
                <option value="name_asc"  @selected($sort === 'name_asc')>Nama A–Z</option>
                <option value="name_desc" @selected($sort === 'name_desc')>Nama Z–A</option>
                <option value="position"  @selected($sort === 'position')>Jabatan</option>
            </select>

            <button type="submit" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>

            @if($search || $position || $institution || $sort !== 'default')
                <a href="{{ route('teachers.index') }}" class="btn-outline shrink-0 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
            @endif
        </form>

        {{-- ── Active filters summary ───────────────────────── --}}
        @if($search || $position || $institution)
            <p class="text-xs mb-6 text-gray-500">
                Menampilkan <strong class="text-amber-700">{{ $teachers->total() }}</strong> guru
                @if($search) yang mengandung "<strong class="text-amber-700">{{ $search }}</strong>" @endif
                @if($position) dengan jabatan "<strong class="text-amber-700">{{ $position }}</strong>" @endif
                @if($institution) di instansi "<strong class="text-amber-700">{{ optional($institutions->firstWhere('id', (int) $institution))->name }}</strong>" @endif
            </p>
        @endif

        {{-- ── Teacher Grid ─────────────────────────────────── --}}
        @if($teachers->isEmpty())
            <div class="text-center py-24" data-aos="fade-up">
                <div class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-5 text-4xl">👨‍🏫</div>
                <p class="text-base font-semibold text-gray-700 mb-1">Tidak ada guru ditemukan</p>
                <p class="text-sm text-gray-400 mb-5">Coba ubah kata kunci atau filter pencarian.</p>
                <a href="{{ route('teachers.index') }}" class="btn-outline inline-flex">Lihat Semua Guru</a>
            </div>
        @else
            <div class="grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($teachers as $teacher)
                    <div class="teacher-card group"
                         data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 60 }}"
                         itemscope itemtype="https://schema.org/Person">

                        {{-- Photo --}}
                        <a href="{{ route('teachers.show', $teacher) }}" class="block relative overflow-hidden h-52 sm:h-56" style="background:linear-gradient(135deg,var(--primary-50),var(--primary-100))">
                            <img src="{{ $teacher->photo_url }}"
                                 alt="{{ $teacher->name }}"
                                 loading="{{ $loop->index < 8 ? 'eager' : 'lazy' }}"
                                 class="teacher-photo w-full h-full object-cover"
                                 itemprop="image">
                            {{-- Dark gradient --}}
                            <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/20 to-transparent"></div>
                            {{-- Position badge --}}
                            <div class="absolute bottom-0 inset-x-0 px-3 pb-3">
                                <span class="inline-block text-[10px] font-bold text-amber-300 uppercase tracking-wider line-clamp-1">
                                    {{ $teacher->position }}
                                </span>
                            </div>
                            {{-- View overlay --}}
                            <div class="absolute inset-0 bg-amber-500/0 group-hover:bg-amber-500/10 transition-colors duration-300 flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full bg-white/0 group-hover:bg-white/90 flex items-center justify-center transition-all duration-300 scale-0 group-hover:scale-100">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                            </div>
                        </a>

                        {{-- Info --}}
                        <div class="p-4 flex flex-col flex-1">
                            <a href="{{ route('teachers.show', $teacher) }}"
                               class="font-bold text-sm leading-snug mb-1 hover:text-amber-700 transition-colors line-clamp-2 block text-gray-900"
                               itemprop="name">
                                {{ $teacher->name }}
                            </a>
                            @if($teacher->subject)
                                <p class="text-[11px] text-gray-400 line-clamp-1 mb-2" itemprop="jobTitle">
                                    {{ $teacher->subject }}
                                </p>
                            @endif

                            @if($teacher->institution)
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5 mb-3 w-fit line-clamp-1">
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $teacher->institution->name }}
                                </span>
                            @endif

                            {{-- Contact pills --}}
                            @if($teacher->phone || $teacher->email || $teacher->whatsapp)
                                <div class="flex flex-wrap gap-1.5 mt-auto pt-3 border-t border-gray-100">
                                    @if($teacher->phone)
                                        <a href="tel:{{ $teacher->phone }}" onclick="event.stopPropagation()" title="Telepon: {{ $teacher->phone }}" class="contact-pill">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            Telepon
                                        </a>
                                    @endif
                                    @if($teacher->email)
                                        <a href="mailto:{{ $teacher->email }}" onclick="event.stopPropagation()" title="{{ $teacher->email }}" class="contact-pill">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Email
                                        </a>
                                    @endif
                                    @if($teacher->whatsapp)
                                        <a href="https://wa.me/{{ $teacher->whatsapp }}" onclick="event.stopPropagation()" target="_blank" rel="noopener" class="contact-pill wa">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            WA
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{-- Profile link --}}
                            <a href="{{ route('teachers.show', $teacher) }}"
                               class="mt-3 text-[11px] font-bold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1 group/link">
                                Lihat Profil
                                <svg class="w-3 h-3 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Pagination ─────────────────────────────── --}}
            @if($teachers->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $teachers->links('pagination::simple-tailwind') }}
                </div>
            @endif
        @endif

    </div>

@endsection
