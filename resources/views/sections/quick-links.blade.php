@php
    $quickLinks = collect(json_decode(setting('quick_links', ''), true) ?: [
        ['icon' => '📋', 'label' => 'SPMB',      'url' => '#spmb',       'is_active' => true],
        ['icon' => '📥', 'label' => 'Unduhan',    'url' => '/unduhan',    'is_active' => true],
        ['icon' => '📅', 'label' => 'Jadwal',     'url' => '#jadwal',     'is_active' => true],
        ['icon' => '🏆', 'label' => 'Prestasi',   'url' => '#prestasi',   'is_active' => true],
        ['icon' => '👥', 'label' => 'Alumni',     'url' => '#alumni',     'is_active' => true],
        ['icon' => '📞', 'label' => 'Kontak',     'url' => '#kontak',     'is_active' => true],
    ])->where('is_active', true)->values();

    $palette = [
        'background:var(--color-amber-100)',
        'background:#dbeafe',
        'background:#ede9fe',
        'background:#dcfce7',
        'background:#fce7f3',
        'background:#cffafe',
    ];
@endphp

@if($quickLinks->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 pb-10">
    <div x-data="{
            expanded: false,
            hiddenCount: 0,
            layout() {
                const items = [...$refs.wrap.children];
                if (!items.length) { this.hiddenCount = 0; return; }
                items.forEach(el => el.style.display = '');
                const topRow = items[0].offsetTop;
                let count = 0;
                items.forEach(el => {
                    if (el.offsetTop > topRow + 1) {
                        count++;
                        if (!this.expanded) { el.style.display = 'none'; }
                    }
                });
                this.hiddenCount = count;
            }
         }"
         x-init="$nextTick(() => { layout(); requestAnimationFrame(() => layout()); })"
         @resize.window.debounce.150ms="layout()"
         data-aos="fade-up" data-aos-duration="600">

        {{-- Tiles --}}
        <div x-ref="wrap" class="flex flex-wrap justify-center gap-3 py-2">
            @foreach($quickLinks as $item)
                <a href="{{ str_starts_with($item['url'], '#') ? '/'.$item['url'] : $item['url'] }}"
                   class="fi-card fi-card-hover group relative shrink-0 w-30 sm:w-32 flex flex-col items-center justify-center gap-2.5 py-5 px-2 overflow-hidden">

                    {{-- Hover accent bar --}}
                    <span class="absolute inset-x-0 top-0 h-1 scale-x-0 origin-left transition-transform duration-300 group-hover:scale-x-100"
                          style="background:linear-gradient(90deg, var(--primary), var(--color-amber-300))"></span>

                    @if(!empty($item['icon_image']))
                        <span class="flex items-center justify-center w-12 h-12 rounded-2xl overflow-hidden shrink-0 p-1.5 transition-transform duration-300 group-hover:scale-110 group-hover:-translate-y-0.5"
                              style="background:var(--bg-alt); border:1px solid var(--border)">
                            <img src="{{ \Illuminate\Support\Str::startsWith($item['icon_image'], ['http://', 'https://', '/']) ? $item['icon_image'] : asset('storage/'.$item['icon_image']) }}"
                                 alt="{{ $item['label'] }}" loading="lazy"
                                 class="w-full h-full object-contain">
                        </span>
                    @elseif(!empty($item['icon']))
                        <span class="flex items-center justify-center w-12 h-12 rounded-2xl text-2xl leading-none shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:-translate-y-0.5"
                              style="{{ $palette[$loop->index % count($palette)] }}">
                            {{ $item['icon'] }}
                        </span>
                    @endif

                    <span class="text-xs font-semibold text-center leading-tight transition-colors duration-200 group-hover:text-amber-700"
                          style="color:var(--text)">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Expand toggle --}}
        <div class="mt-3 flex justify-center" x-show="hiddenCount > 0" x-cloak>
            <button type="button"
                    @click="expanded = !expanded; $nextTick(() => layout())"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold transition-all hover:scale-105"
                    style="color:var(--primary); background:var(--card); border:1px solid var(--border)">
                <span x-text="expanded ? 'Tampilkan lebih sedikit' : 'Lihat semua (' + hiddenCount + ' lainnya)'"></span>
                <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': expanded }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>
</section>
@endif
