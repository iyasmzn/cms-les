@if(isset($courses) && $courses->isNotEmpty())
<section id="courses" class="py-20 sm:py-28" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        @php
            $eyebrow  = setting('section_courses_eyebrow', 'Courses');
            $subtitle = setting('section_courses_subtitle', 'Join one of our courses (les) — each organised into skill-based groups with its own schedule and coach.');
        @endphp
        <div class="text-center mb-14" data-aos="fade-up">
            @if($eyebrow)
                <div class="fi-label mb-3">{{ $eyebrow }}</div>
            @endif
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" style="color:var(--text)">
                {{ setting('section_courses_title') ?: 'Our Courses' }}
            </h2>
            @if($subtitle)
                <p class="mt-3 text-base max-w-lg mx-auto leading-relaxed" style="color:var(--muted)">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($courses as $course)
            <a href="{{ route('courses.show', $course) }}"
               class="fi-card fi-card-hover group flex flex-col p-6"
               data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">

                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0">
                        @if($url = icon_url($course->icon_image))
                            <img src="{{ $url }}" alt="{{ $course->name }}" loading="lazy" class="w-8 h-8 object-contain">
                        @else
                            <span class="text-3xl">{{ $course->icon ?: '🏊' }}</span>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $course->groups_count }} {{ \Illuminate\Support\Str::plural('group', $course->groups_count) }}
                    </span>
                </div>

                @if($course->short_name)
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-1">{{ $course->short_name }}</span>
                @endif
                <h3 class="font-extrabold text-lg leading-snug mb-2" style="color:var(--text)">{{ $course->name }}</h3>
                @if($course->description)
                    <p class="text-sm leading-relaxed line-clamp-2 mb-5" style="color:var(--muted)">{{ $course->description }}</p>
                @endif

                <span class="inline-flex items-center gap-1.5 text-sm font-semibold mt-auto" style="color:var(--primary)">
                    View Groups
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('courses.index') }}" class="btn-outline">View All Courses</a>
        </div>

    </div>
</section>
@endif
