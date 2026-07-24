@extends('layouts.public')

@section('content')

{{-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ --}}
<section class="-mt-17 pt-32 pb-16 sm:pt-36 sm:pb-20 relative overflow-hidden"
         style="background:linear-gradient(135deg,#082828 0%,#08484A 55%,#0a6060 100%)">
    <div class="absolute inset-0 pointer-events-none"
         style="background:
            radial-gradient(ellipse 55% 70% at 10% 60%,rgba(255,255,255,.06) 0%,transparent 55%),
            radial-gradient(ellipse 35% 50% at 90% 10%,rgba(255,255,255,.04) 0%,transparent 55%)">
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 text-xs font-bold px-3.5 py-1.5 rounded-full mb-5"
                 style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.18)">
                🛍️ Toko
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 leading-tight">
                Produk Pilihan
            </h1>
            <p class="text-sm sm:text-base leading-relaxed mb-8" style="color:rgba(255,255,255,.68)">
                Beragam produk berkualitas dengan harga terbaik dari {{ setting('site_name', config('app.name')) }}.
            </p>

            {{-- ── Search bar inside hero ── --}}
            <form method="GET" action="{{ route('products.index') }}" id="search-form">
                {{-- preserve active filters when searching --}}
                @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
                @if($sort !== 'default') <input type="hidden" name="sort" value="{{ $sort }}"> @endif
                @if($minPrice !== null) <input type="hidden" name="min_price" value="{{ $minPrice }}"> @endif
                @if($maxPrice !== null) <input type="hidden" name="max_price" value="{{ $maxPrice }}"> @endif
                @if($inStock) <input type="hidden" name="in_stock" value="1"> @endif

                <div class="relative flex items-center">
                    <svg class="absolute left-4 w-5 h-5 pointer-events-none" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" style="color:rgba(0,0,0,.4)">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="search" name="q" value="{{ $q }}"
                           placeholder="Cari nama produk, merek, atau deskripsi…"
                           autocomplete="off"
                           class="w-full pl-12 pr-32 py-3.5 rounded-2xl text-sm font-medium outline-none transition-shadow focus:shadow-lg"
                           style="background:rgba(255,255,255,.96);color:var(--text);box-shadow:0 4px 24px rgba(0,0,0,.18)">
                    <button type="submit"
                            class="absolute right-1.5 px-5 py-2.5 rounded-xl text-sm font-bold transition-colors"
                            style="background:var(--primary);color:#fff">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     MAIN — sidebar + content
══════════════════════════════════════════ --}}
<div x-data="{ filterOpen: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Mobile filter toggle bar ── --}}
    <div class="flex items-center gap-3 mb-6 lg:hidden">
        <button @click="filterOpen = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border relative"
                style="background:var(--card);color:var(--text);border-color:var(--border)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
            @if($activeFilters > 0)
                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full text-xs font-bold text-white flex items-center justify-center"
                      style="background:var(--primary)">{{ $activeFilters }}</span>
            @endif
        </button>

        {{-- Sort select — mobile --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex-1">
            @if($q) <input type="hidden" name="q" value="{{ $q }}"> @endif
            @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
            @if($minPrice !== null) <input type="hidden" name="min_price" value="{{ $minPrice }}"> @endif
            @if($maxPrice !== null) <input type="hidden" name="max_price" value="{{ $maxPrice }}"> @endif
            @if($inStock) <input type="hidden" name="in_stock" value="1"> @endif
            <select name="sort" onchange="this.form.submit()"
                    class="w-full px-3 py-2.5 rounded-xl text-sm font-medium border outline-none"
                    style="background:var(--card);color:var(--text);border-color:var(--border)">
                <option value="default"    {{ $sort === 'default'    ? 'selected' : '' }}>Urutan Default</option>
                <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Terbaru</option>
                <option value="price_asc"  {{ $sort === 'price_asc'  ? 'selected' : '' }}>Harga: Terendah</option>
                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi</option>
                <option value="name_asc"   {{ $sort === 'name_asc'   ? 'selected' : '' }}>Nama A–Z</option>
            </select>
        </form>
    </div>

    <div class="flex gap-8">

        {{-- ════════════════════════════════════
             SIDEBAR (desktop + mobile drawer)
        ════════════════════════════════════ --}}

        {{-- Mobile overlay --}}
        <div x-show="filterOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="filterOpen = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

        {{-- Sidebar panel --}}
        <aside class="
                w-64 shrink-0 hidden lg:block
                lg:static lg:z-auto lg:translate-x-0 lg:rounded-none lg:shadow-none
                fixed inset-y-0 left-0 z-40 bg-white overflow-y-auto rounded-r-3xl shadow-2xl p-6 lg:p-0 lg:bg-transparent
            "
            :class="filterOpen ? '!block' : ''"
            x-show="filterOpen || window.innerWidth >= 1024"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            style="display:block">

            {{-- Mobile close button --}}
            <div class="flex items-center justify-between mb-6 lg:hidden">
                <span class="font-extrabold text-base" style="color:var(--text)">Filter</span>
                <button @click="filterOpen = false"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-100"
                        style="color:var(--muted)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                @if($q) <input type="hidden" name="q" value="{{ $q }}"> @endif
                <input type="hidden" name="sort" value="{{ $sort }}">

                {{-- ── Kategori ── --}}
                <div class="fi-card p-5 mb-4">
                    <h3 class="font-extrabold text-sm mb-4 flex items-center gap-2" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Kategori
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="category" value=""
                                   {{ $category === '' ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="w-4 h-4 accent-[var(--primary)]">
                            <span class="text-sm font-medium transition-colors group-hover:opacity-75"
                                  style="color:{{ $category === '' ? 'var(--primary)' : 'var(--text)' }}">
                                Semua Kategori
                            </span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="category" value="{{ $cat }}"
                                   {{ $category === $cat ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="w-4 h-4 accent-[var(--primary)]">
                            <span class="text-sm font-medium transition-colors group-hover:opacity-75"
                                  style="color:{{ $category === $cat ? 'var(--primary)' : 'var(--text)' }}">
                                {{ $cat }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── Rentang Harga ── --}}
                <div class="fi-card p-5 mb-4">
                    <h3 class="font-extrabold text-sm mb-4 flex items-center gap-2" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Rentang Harga
                    </h3>

                    {{-- Quick price presets --}}
                    @php
                        $presets = [
                            ['label' => '< Rp 50rb',      'min' => null, 'max' => 50000],
                            ['label' => 'Rp 50–100rb',    'min' => 50000, 'max' => 100000],
                            ['label' => 'Rp 100–150rb',   'min' => 100000, 'max' => 150000],
                            ['label' => '> Rp 150rb',     'min' => 150000, 'max' => null],
                        ];
                    @endphp
                    <div class="space-y-2 mb-4">
                        @foreach($presets as $p)
                        @php
                            $pActive = ($minPrice === $p['min'] && $maxPrice === $p['max']);
                        @endphp
                        <a href="{{ route('products.index', array_filter([
                                'q'        => $q ?: null,
                                'category' => $category ?: null,
                                'sort'     => $sort !== 'default' ? $sort : null,
                                'min_price'=> $pActive ? null : $p['min'],
                                'max_price'=> $pActive ? null : $p['max'],
                                'in_stock' => $inStock ? '1' : null,
                            ], fn($v) => $v !== null)) }}"
                           class="flex items-center gap-2.5 text-sm font-medium transition-colors"
                           style="color:{{ $pActive ? 'var(--primary)' : 'var(--muted)' }}">
                            <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                                  style="{{ $pActive ? 'border-color:var(--primary)' : 'border-color:var(--border)' }}">
                                @if($pActive)
                                <span class="w-2 h-2 rounded-full" style="background:var(--primary)"></span>
                                @endif
                            </span>
                            {{ $p['label'] }}
                        </a>
                        @endforeach
                    </div>

                    {{-- Custom range inputs --}}
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price"
                               value="{{ $minPrice ?? '' }}"
                               placeholder="Min"
                               min="{{ $priceMin }}" max="{{ $priceMax }}" step="5000"
                               class="w-full px-3 py-2 rounded-xl border text-xs text-center outline-none"
                               style="background:var(--bg);color:var(--text);border-color:var(--border)">
                        <span class="text-xs shrink-0" style="color:var(--muted)">—</span>
                        <input type="number" name="max_price"
                               value="{{ $maxPrice ?? '' }}"
                               placeholder="Maks"
                               min="{{ $priceMin }}" max="{{ $priceMax }}" step="5000"
                               class="w-full px-3 py-2 rounded-xl border text-xs text-center outline-none"
                               style="background:var(--bg);color:var(--text);border-color:var(--border)">
                    </div>
                    <button type="submit" form="filter-form"
                            class="w-full mt-2.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            style="background:var(--primary-50);color:var(--primary)">
                        Terapkan Harga
                    </button>
                </div>

                {{-- ── Ketersediaan ── --}}
                <div class="fi-card p-5 mb-4">
                    <h3 class="font-extrabold text-sm mb-3 flex items-center gap-2" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        Ketersediaan
                    </h3>
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" name="in_stock" value="1"
                                   {{ $inStock ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="sr-only peer">
                            <div class="w-10 h-6 rounded-full border-2 transition-colors peer-checked:border-transparent"
                                 style="background:{{ $inStock ? 'var(--primary)' : 'var(--bg)' }};border-color:{{ $inStock ? 'var(--primary)' : 'var(--border)' }}">
                                <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200"
                                     style="transform:translateX({{ $inStock ? '18px' : '1px' }})"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium" style="color:var(--text)">Stok tersedia saja</span>
                    </label>
                </div>

                {{-- ── Sort (desktop) ── --}}
                <div class="fi-card p-5 mb-4">
                    <h3 class="font-extrabold text-sm mb-3 flex items-center gap-2" style="color:var(--text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                        Urutkan
                    </h3>
                    @php
                        $sorts = [
                            'default'    => 'Urutan Default',
                            'newest'     => 'Terbaru',
                            'price_asc'  => 'Harga: Terendah',
                            'price_desc' => 'Harga: Tertinggi',
                            'name_asc'   => 'Nama A–Z',
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($sorts as $val => $label)
                        <a href="{{ route('products.index', array_filter([
                                'q'        => $q ?: null,
                                'category' => $category ?: null,
                                'sort'     => $val !== 'default' ? $val : null,
                                'min_price'=> $minPrice,
                                'max_price'=> $maxPrice,
                                'in_stock' => $inStock ? '1' : null,
                            ], fn($v) => $v !== null)) }}"
                           class="flex items-center gap-2.5 text-sm font-medium transition-colors"
                           style="color:{{ $sort === $val ? 'var(--primary)' : 'var(--muted)' }}">
                            <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                                  style="{{ $sort === $val ? 'border-color:var(--primary)' : 'border-color:var(--border)' }}">
                                @if($sort === $val)
                                <span class="w-2 h-2 rounded-full" style="background:var(--primary)"></span>
                                @endif
                            </span>
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- ── Reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('products.index') }}"
                   class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold transition-colors border"
                   style="background:var(--card);color:var(--muted);border-color:var(--border)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Semua Filter
                </a>
                @endif

            </form>

        </aside>

        {{-- ════════════════════════════════════
             CONTENT AREA
        ════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- ── Toolbar: results count + active chips + sort (desktop) ── --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-aos="fade-up">

                {{-- Results info --}}
                <div>
                    <p class="text-sm font-medium" style="color:var(--muted)">
                        Menampilkan
                        <strong style="color:var(--text)">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                        dari <strong style="color:var(--text)">{{ $products->total() }}</strong> produk
                        @if($q)
                            untuk <em style="color:var(--primary)">"{{ $q }}"</em>
                        @endif
                    </p>
                </div>

                {{-- Sort dropdown (desktop only) --}}
                <div class="hidden lg:flex items-center gap-2">
                    <span class="text-xs font-semibold" style="color:var(--muted)">Urutkan:</span>
                    <div x-data="{ sortOpen: false }" class="relative">
                        <button @click="sortOpen = !sortOpen" @click.outside="sortOpen = false"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all"
                                style="background:var(--card);color:var(--text);border-color:var(--border)">
                            {{ $sorts[$sort] ?? 'Urutan Default' }}
                            <svg class="w-4 h-4 transition-transform" :class="sortOpen ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="sortOpen"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 top-full mt-1.5 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-20 py-1">
                            @foreach($sorts as $val => $label)
                            <a href="{{ route('products.index', array_filter([
                                    'q'        => $q ?: null,
                                    'category' => $category ?: null,
                                    'sort'     => $val !== 'default' ? $val : null,
                                    'min_price'=> $minPrice,
                                    'max_price'=> $maxPrice,
                                    'in_stock' => $inStock ? '1' : null,
                                ], fn($v) => $v !== null)) }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors
                                      {{ $sort === $val ? 'font-bold' : 'font-medium' }}"
                               style="color:{{ $sort === $val ? 'var(--primary)' : 'var(--muted)' }};
                                      background:{{ $sort === $val ? 'var(--primary-50)' : 'transparent' }}">
                                @if($sort === $val)
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="w-3.5 shrink-0"></span>
                                @endif
                                {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Active filter chips ── --}}
            @if($activeFilters > 0)
            <div class="flex flex-wrap gap-2 mb-5" data-aos="fade-up">
                @if($q)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
                      style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                    🔍 "{{ Str::limit($q, 20) }}"
                    <a href="{{ route('products.index', array_filter(['category' => $category ?: null, 'sort' => $sort !== 'default' ? $sort : null, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'in_stock' => $inStock ? '1' : null], fn($v) => $v !== null)) }}"
                       class="hover:opacity-70 transition-opacity ml-0.5">✕</a>
                </span>
                @endif
                @if($category)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
                      style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                    📂 {{ $category }}
                    <a href="{{ route('products.index', array_filter(['q' => $q ?: null, 'sort' => $sort !== 'default' ? $sort : null, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'in_stock' => $inStock ? '1' : null], fn($v) => $v !== null)) }}"
                       class="hover:opacity-70 transition-opacity ml-0.5">✕</a>
                </span>
                @endif
                @if($minPrice !== null || $maxPrice !== null)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
                      style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                    💰
                    @if($minPrice !== null && $maxPrice !== null)
                        Rp {{ number_format($minPrice, 0, ',', '.') }} – {{ number_format($maxPrice, 0, ',', '.') }}
                    @elseif($minPrice !== null)
                        ≥ Rp {{ number_format($minPrice, 0, ',', '.') }}
                    @else
                        ≤ Rp {{ number_format($maxPrice, 0, ',', '.') }}
                    @endif
                    <a href="{{ route('products.index', array_filter(['q' => $q ?: null, 'category' => $category ?: null, 'sort' => $sort !== 'default' ? $sort : null, 'in_stock' => $inStock ? '1' : null], fn($v) => $v !== null)) }}"
                       class="hover:opacity-70 transition-opacity ml-0.5">✕</a>
                </span>
                @endif
                @if($inStock)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
                      style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                    ✅ Stok tersedia
                    <a href="{{ route('products.index', array_filter(['q' => $q ?: null, 'category' => $category ?: null, 'sort' => $sort !== 'default' ? $sort : null, 'min_price' => $minPrice, 'max_price' => $maxPrice], fn($v) => $v !== null)) }}"
                       class="hover:opacity-70 transition-opacity ml-0.5">✕</a>
                </span>
                @endif
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors"
                   style="color:var(--muted)">
                    Reset semua ✕
                </a>
            </div>
            @endif

            {{-- Flash --}}
            @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-green-800 bg-green-50 border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            {{-- ── Product grid ── --}}
            @if($products->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($products as $product)
                <div class="fi-card fi-card-hover flex flex-col overflow-hidden group"
                     data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 50 }}">

                    {{-- Cover --}}
                    <a href="{{ route('products.show', $product) }}"
                       class="block relative overflow-hidden shrink-0" style="height:200px">
                        <img src="{{ $product->cover_url }}"
                             alt="{{ $product->title }}"
                             loading="lazy"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-linear-to-t from-black/25 to-transparent"></div>

                        @if(!$product->is_available || $product->stock === 0)
                        <span class="absolute top-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:rgba(239,68,68,.9);color:#fff">Habis</span>
                        @endif
                        @if($product->category)
                        <span class="absolute bottom-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full backdrop-blur-sm"
                              style="background:rgba(8,72,74,.85);color:#fff">{{ $product->category }}</span>
                        @endif
                    </a>

                    {{-- Info --}}
                    <div class="p-4 flex flex-col flex-1 gap-1">
                        <h3 class="font-bold text-sm leading-snug line-clamp-2 flex-1" style="color:var(--text)">
                            <a href="{{ route('products.show', $product) }}" class="hover:opacity-70 transition-opacity">
                                {{ $product->title }}
                            </a>
                        </h3>
                        @if($product->brand)
                        <p class="text-xs" style="color:var(--muted)">{{ $product->brand }}</p>
                        @endif

                        {{-- Price row --}}
                        <div class="mt-auto pt-3 border-t" style="border-color:var(--border)">
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <span class="font-extrabold text-base" style="color:var(--primary)">
                                    {{ $product->formatted_price }}
                                </span>
                                @if($product->stock > 0)
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                      style="background:var(--primary-50);color:var(--primary-800)">
                                    Stok {{ $product->stock }}
                                </span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                @if($product->is_available && $product->stock > 0)
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-xs font-bold py-2.5 rounded-xl transition-all flex items-center justify-center gap-1.5"
                                            style="background:var(--primary);color:#fff">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        + Keranjang
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-xs font-semibold px-3 py-2.5 rounded-xl transition-all shrink-0 flex items-center"
                                   style="background:var(--bg);color:var(--text);border:1px solid var(--border)">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="mt-10">
                {{ $products->links() }}
            </div>
            @endif

            @else
            {{-- Empty state --}}
            <div class="fi-card text-center py-20" data-aos="fade-up">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-lg font-extrabold mb-2" style="color:var(--text)">
                    Produk tidak ditemukan
                </h3>
                <p class="text-sm mb-6 max-w-xs mx-auto" style="color:var(--muted)">
                    @if($q)
                        Tidak ada produk yang cocok dengan pencarian <strong>"{{ $q }}"</strong>.
                    @else
                        Tidak ada produk yang cocok dengan filter yang dipilih.
                    @endif
                </p>
                <a href="{{ route('products.index') }}" class="btn-outline text-sm">
                    Hapus Semua Filter
                </a>
            </div>
            @endif

        </div>
        {{-- end content --}}
    </div>
    {{-- end flex --}}
</div>

@endsection
