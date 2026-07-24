@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Testimonial> $testimonials */
    $testimonials = $testimonials ?? \App\Models\Testimonial::published()->get();
@endphp

@if($testimonials->isNotEmpty())
<section id="kesan-pesan" class="py-20 sm:py-28" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $eyebrow  = setting('section_testimonials_eyebrow', 'Kesan & Pesan');
            $subtitle = setting('section_testimonials_subtitle', 'Cerita dan harapan dari para alumni yang telah menempuh pendidikan bersama kami.');
        @endphp
        <div class="text-center mb-14" data-aos="fade-up">
            @if($eyebrow)
                <div class="fi-label mb-3">{{ $eyebrow }}</div>
            @endif
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">
                {{ setting('section_testimonials_title') ?: 'Apa Kata Alumni' }}
            </h2>
            @if($subtitle)
                <p class="mt-3 text-base max-w-lg mx-auto leading-relaxed" style="color:var(--muted)">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($testimonials as $index => $testimonial)
                @php
                    $years = collect([$testimonial->class_year, $testimonial->graduation_year])
                        ->filter()
                        ->implode(' – ');
                @endphp
                <div class="fi-card fi-card-hover p-7 flex flex-col"
                     data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 80 }}">

                    <svg class="w-9 h-9 mb-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"
                         style="color:var(--primary-300)">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>

                    <p class="text-base leading-relaxed flex-1" style="color:var(--muted)">
                        {{ $testimonial->message }}
                    </p>

                    <div class="mt-6 pt-6 border-t flex items-center gap-4" style="border-color:var(--border)">
                        <img src="{{ $testimonial->photo_url }}"
                             alt="Foto {{ $testimonial->name }}"
                             loading="lazy"
                             class="w-12 h-12 rounded-full object-cover shrink-0 ring-2"
                             style="--tw-ring-color:var(--primary-100)">
                        <div class="min-w-0">
                            <div class="font-bold text-sm truncate" style="color:var(--text)">{{ $testimonial->name }}</div>
                            @if($years)
                                <div class="text-xs" style="color:var(--muted)">Alumni {{ $years }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
