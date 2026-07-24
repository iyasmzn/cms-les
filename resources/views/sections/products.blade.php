<section id="toko" class="py-20 sm:py-28" style="background:var(--bg-alt)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_products_eyebrow', 'Toko');
            $subtitle = setting('section_products_subtitle', 'Beragam produk berkualitas dengan harga terbaik, siap dipesan.');
        @endphp
        <div class="flex items-end justify-between gap-6 mb-14" data-aos="fade-up">
            <div>
                @if($eyebrow)
                    <div class="fi-label mb-3">{{ $eyebrow }}</div>
                @endif
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">{{ setting('section_products_title') ?: 'Produk Pilihan' }}</h2>
                @if($subtitle)
                    <p class="mt-2 text-base max-w-md leading-relaxed" style="color:var(--muted)">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <a href="{{ route('products.index') }}"
               class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-75"
               style="color:var(--primary)">
                Lihat Semua Produk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredProducts as $product)
            <div class="fi-card fi-card-hover flex flex-col overflow-hidden group"
                 data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 70 }}">

                {{-- Cover --}}
                <a href="{{ route('products.show', $product) }}"
                   class="block relative overflow-hidden shrink-0" style="height:180px">
                    <img src="{{ $product->cover_url }}"
                         alt="{{ $product->title }}"
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-linear-to-t from-black/20 to-transparent"></div>
                    @if($product->stock === 0)
                    <span class="absolute top-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full"
                          style="background:rgba(239,68,68,.9);color:#fff">Habis</span>
                    @endif
                    @if($product->category)
                    <span class="absolute bottom-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full backdrop-blur-sm"
                          style="background:rgba(8,72,74,.85);color:#fff">{{ $product->category }}</span>
                    @endif
                </a>

                {{-- Info --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-sm leading-snug mb-1 line-clamp-2 flex-1" style="color:var(--text)">
                        <a href="{{ route('products.show', $product) }}" class="hover:opacity-70 transition-opacity">
                            {{ $product->title }}
                        </a>
                    </h3>

                    @if($product->brand)
                    <p class="text-xs mb-3" style="color:var(--muted)">{{ $product->brand }}</p>
                    @endif

                    <div class="mt-auto pt-3 border-t" style="border-color:var(--border)">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="font-extrabold text-base" style="color:var(--primary)">
                                {{ $product->formatted_price }}
                            </span>
                            <span class="text-xs" style="color:var(--muted)">
                                Stok: {{ $product->stock }}
                            </span>
                        </div>

                        {{-- Add to cart --}}
                        @if($product->is_available && $product->stock > 0)
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-xs font-bold py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5"
                                    style="background:var(--primary);color:#fff">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Tambah ke Keranjang
                            </button>
                        </form>
                        @else
                        <span class="w-full text-xs font-bold py-2 rounded-xl flex items-center justify-center"
                              style="background:var(--border);color:var(--muted)">
                            Stok Habis
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center" data-aos="fade-up">
            <a href="{{ route('products.index') }}" class="btn-outline">
                Lihat Semua Produk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @else
        <div class="text-center py-20 fi-card">
            <div class="text-5xl mb-4">🛍️</div>
            <p class="text-base font-medium" style="color:var(--muted)">Belum ada produk yang tersedia.</p>
            <a href="{{ route('products.index') }}" class="btn-outline mt-6 inline-flex">Lihat Toko</a>
        </div>
        @endif

    </div>
</section>
