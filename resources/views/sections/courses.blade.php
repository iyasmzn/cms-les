@if(isset($courses) && $courses->isNotEmpty())
@php
    $eyebrow  = setting('section_courses_eyebrow', 'Courses');
    $subtitle = setting('section_courses_subtitle', 'Pilih les yang kamu minati — tiap course punya kelompok dengan jadwal, level, dan pelatihnya sendiri.');
    // Map the institution badge colour to an accent hex for the card top bar.
    $accents = [
        'primary' => '#08484A', 'info' => '#0ea5e9', 'success' => '#16a34a',
        'warning' => '#d97706', 'danger' => '#dc2626', 'gray' => '#64748b',
    ];
@endphp
<section id="courses" class="py-20 sm:py-28 relative overflow-hidden" style="background:var(--bg)">
    {{-- soft ambient accents --}}
    <div class="pointer-events-none absolute inset-0 opacity-[.5]"
         style="background:radial-gradient(ellipse 40% 40% at 12% 0%, color-mix(in oklab,var(--primary) 12%,transparent) 0%, transparent 60%),radial-gradient(ellipse 40% 40% at 90% 100%, rgba(217,119,6,.10) 0%, transparent 60%)"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14" data-aos="fade-up">
            @if($eyebrow)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full mb-4"
                     style="background:color-mix(in oklab,var(--primary) 10%,transparent);color:var(--primary)">
                    <span class="w-1.5 h-1.5 rounded-full" style="background:currentColor"></span>
                    <span class="text-xs font-bold uppercase tracking-widest">{{ $eyebrow }}</span>
                </div>
            @endif
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight" style="color:var(--text)">
                {{ setting('section_courses_title') ?: 'Our Courses' }}
            </h2>
            @if($subtitle)
                <p class="mt-4 text-base max-w-2xl mx-auto leading-relaxed" style="color:var(--muted)">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($courses as $course)
            @php $accent = $accents[$course->color] ?? '#d97706'; @endphp
            <a href="{{ route('courses.show', $course) }}"
               class="course-feature-card group relative flex flex-col rounded-3xl overflow-hidden bg-white border transition-all duration-300"
               style="border-color:var(--border)"
               data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 90 }}">

                {{-- accent top bar --}}
                <div class="h-1.5 w-full" style="background:linear-gradient(90deg,{{ $accent }},color-mix(in oklab,{{ $accent }} 45%,white))"></div>

                <div class="p-7 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 shadow-sm"
                             style="background:color-mix(in oklab,{{ $accent }} 12%,white);border:1px solid color-mix(in oklab,{{ $accent }} 25%,white)">
                            @if($url = icon_url($course->icon_image))
                                <img src="{{ $url }}" alt="{{ $course->name }}" loading="lazy" class="w-9 h-9 object-contain">
                            @else
                                <span class="text-4xl leading-none">{{ $course->icon ?: '📚' }}</span>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full"
                              style="background:color-mix(in oklab,{{ $accent }} 10%,white);color:{{ $accent }};border:1px solid color-mix(in oklab,{{ $accent }} 22%,white)">
                            {{ $course->groups_count }} kelompok
                        </span>
                    </div>

                    @if($course->short_name)
                        <span class="text-xs font-bold uppercase tracking-widest mb-1" style="color:{{ $accent }}">{{ $course->short_name }}</span>
                    @endif
                    <h3 class="font-extrabold text-xl leading-snug mb-2" style="color:var(--text)">{{ $course->name }}</h3>
                    @if($course->description)
                        <p class="text-sm leading-relaxed line-clamp-3 mb-6" style="color:var(--muted)">{{ $course->description }}</p>
                    @endif

                    <span class="mt-auto inline-flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold text-white transition-transform group-hover:gap-3"
                          style="background:{{ $accent }}">
                        Lihat Kelompok
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('courses.index') }}" class="btn-outline">Lihat Semua Course</a>
        </div>
    </div>

    @once
        <style>
            .course-feature-card:hover { transform: translateY(-6px); box-shadow: 0 18px 40px -18px rgba(0,0,0,.28); }
        </style>
    @endonce
</section>
@endif
