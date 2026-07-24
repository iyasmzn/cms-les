<section id="blog" class="py-20 sm:py-28" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_blog_eyebrow', 'Berita & Artikel');
            $subtitle = setting('section_blog_subtitle', 'Blog inspiratif dari berbagai sumber.');
        @endphp
        <div class="flex items-end justify-between gap-6 mb-14" data-aos="fade-up">
            <div>
                @if($eyebrow)
                    <div class="fi-label mb-3">{{ $eyebrow }}</div>
                @endif
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">{{ setting('section_blog_title') ?: 'Artikel' }}</h2>
                @if($subtitle)
                    <p class="mt-2 text-base max-w-md leading-relaxed" style="color:var(--muted)">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <a href="{{ route('blog.index') }}"
               class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-75"
               style="color:var(--primary)">
                Semua Artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($posts->isNotEmpty())
            @php $featured = $posts->first(); @endphp

            {{-- Featured post --}}
            <article class="fi-card overflow-hidden mb-6" data-aos="fade-up" data-aos-delay="60">
                <div class="grid lg:grid-cols-5">
                    <a href="{{ route('blog.show', $featured->slug) }}"
                       class="lg:col-span-2 h-56 lg:h-auto relative overflow-hidden block group">
                        <img src="{{ $featured->thumbnail_url }}"
                             alt="{{ $featured->title }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0"
                             style="background:linear-gradient(to top,rgba(0,0,0,.4),transparent)"></div>
                    </a>
                    <div class="lg:col-span-3 p-8 lg:p-10 flex flex-col justify-center">
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="text-xs font-bold px-3 py-1 rounded-full border"
                                  style="background:var(--primary-50);color:var(--primary-800);border-color:var(--primary-200)">
                                {{ $featured->category }}
                            </span>
                            <span class="text-xs" style="color:var(--muted)">
                                {{ $featured->formatted_date }} · {{ $featured->read_time }} mnt baca
                            </span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold leading-snug tracking-tight mb-3" style="color:var(--text)">
                            <a href="{{ route('blog.show', $featured->slug) }}" class="hover:opacity-75 transition-opacity">
                                {{ $featured->title }}
                            </a>
                        </h3>
                        <p class="text-base leading-relaxed mb-6 line-clamp-3" style="color:var(--muted)">
                            {{ $featured->excerpt }}
                        </p>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                     style="background:var(--primary)">
                                    {{ $featured->author_initials }}
                                </div>
                                <span class="text-sm font-medium" style="color:var(--muted)">{{ $featured->author }}</span>
                            </div>
                            <a href="{{ route('blog.show', $featured->slug) }}" class="btn-primary text-sm">
                                Baca Selengkapnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Post grid --}}
            @if($posts->count() > 1)
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts->skip(1) as $post)
                        <article class="fi-card fi-card-hover group flex flex-col overflow-hidden"
                                 data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                            <a href="{{ route('blog.show', $post->slug) }}" class="relative h-48 block overflow-hidden">
                                <img src="{{ $post->thumbnail_url }}"
                                     alt="{{ $post->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute top-4 left-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full backdrop-blur-md border"
                                          style="background:rgba(255,255,255,.88);color:var(--primary-800);border-color:var(--primary-200)">
                                        {{ $post->category }}
                                    </span>
                                </div>
                            </a>
                            <div class="p-6 flex flex-col flex-1">
                                <span class="text-xs mb-2.5 block" style="color:var(--muted)">{{ $post->formatted_date }}</span>
                                <h3 class="font-bold text-base leading-snug mb-2.5 line-clamp-2 flex-1 hover:opacity-70 transition-opacity"
                                    style="color:var(--text)">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-sm leading-relaxed line-clamp-2 mb-5" style="color:var(--muted)">{{ $post->excerpt }}</p>
                                <a href="{{ route('blog.show', $post->slug) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-75 transition-opacity"
                                   style="color:var(--primary)">
                                    Baca Selengkapnya
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        @else
            <div class="text-center py-20 fi-card">
                <div class="text-5xl mb-4">📰</div>
                <p class="text-base font-medium" style="color:var(--muted)">Belum ada artikel yang dipublikasikan.</p>
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('blog.index') }}" class="btn-outline">Lihat Semua Artikel</a>
        </div>
    </div>
</section>
