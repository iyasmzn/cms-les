<x-filament-widgets::widget class="fi-wi-stats-overview">
    <x-filament::section heading="Pendaftar per Tahun Ajaran">
        <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(15rem,1fr));">

            {{-- Kartu: tahun ajaran aktif --}}
            <div class="fi-wi-stats-overview-stat">
                <div class="fi-wi-stats-overview-stat-content">
                    <div class="fi-wi-stats-overview-stat-label-ctn">
                        <span class="fi-wi-stats-overview-stat-label">{{ $activeLabel }}</span>
                    </div>
                    <div class="fi-wi-stats-overview-stat-value">{{ number_format($activeCount) }}</div>
                    <div class="fi-wi-stats-overview-stat-description">
                        <span>{{ $hasActive ? 'Tahun ajaran aktif' : 'Belum ada tahun ajaran aktif' }}</span>
                    </div>
                </div>
            </div>

            {{-- Kartu: total / filter tahun ajaran tidak aktif --}}
            <div class="fi-wi-stats-overview-stat">
                <div class="fi-wi-stats-overview-stat-content">
                    <div class="fi-wi-stats-overview-stat-label-ctn">
                        <span class="fi-wi-stats-overview-stat-label">{{ $totalLabel }}</span>
                    </div>
                    <div class="fi-wi-stats-overview-stat-value">{{ number_format($totalCount) }}</div>
                    <div class="fi-wi-stats-overview-stat-description">
                        <span>{{ $isFiltered ? 'Tahun ajaran terpilih' : 'Total seluruh pendaftar' }}</span>
                    </div>

                    @if($inactiveYears->isNotEmpty())
                    <div style="margin-top:.75rem;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="totalYearId">
                                <option value="">Total: semua tahun</option>
                                @foreach($inactiveYears as $year)
                                    <option value="{{ $year->id }}">T.A. {{ $year->label }}</option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
