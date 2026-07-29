@php
    use App\Filament\Resources\Groups\GroupResource;
    $today = \Illuminate\Support\Carbon::today();
    $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Course Sessions</x-slot>

        {{-- Month navigation --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <x-filament::icon-button icon="heroicon-o-chevron-left" wire:click="previousMonth" label="Previous month" />
                <x-filament::button size="xs" color="gray" wire:click="goToday">Today</x-filament::button>
                <x-filament::icon-button icon="heroicon-o-chevron-right" wire:click="nextMonth" label="Next month" />
            </div>
            <h3 class="text-base font-bold text-gray-950 dark:text-white">{{ $calendar->label() }}</h3>
        </div>

        {{-- Weekday header --}}
        <div class="grid grid-cols-7 gap-px mb-px text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            @foreach($weekdays as $wd)
                <div class="py-1.5">{{ $wd }}</div>
            @endforeach
        </div>

        {{-- Weeks --}}
        <div class="overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
            @foreach($calendar->weeks() as $week)
            <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-white/10">
                @foreach($week as $day)
                @php
                    $daySessions = $sessions[$day->toDateString()] ?? collect();
                    $inMonth = $calendar->isCurrentMonth($day);
                    $isToday = $day->isSameDay($today);
                @endphp
                <div @class([
                        'min-h-[84px] p-1.5 flex flex-col gap-1 bg-white dark:bg-gray-900',
                        'opacity-40' => ! $inMonth,
                    ])>
                    <div @class([
                            'text-xs font-semibold',
                            'flex items-center justify-center w-5 h-5 rounded-full bg-primary-600 text-white' => $isToday,
                            'text-gray-500 dark:text-gray-400' => ! $isToday,
                        ])>{{ $day->day }}</div>

                    @foreach($daySessions as $session)
                        <a href="{{ GroupResource::getUrl('edit', ['record' => $session->group_id]) }}"
                           class="block rounded px-1.5 py-0.5 text-[10px] leading-tight bg-primary-50 text-primary-700 ring-1 ring-primary-200 hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-300 dark:ring-primary-500/20"
                           title="{{ $session->group?->name }}{{ $session->timeLabel() ? ' · '.$session->timeLabel() : '' }}{{ $session->resolvedLocation() ? ' @ '.$session->resolvedLocation() : '' }}">
                            <span class="block font-semibold truncate">{{ $session->group?->name }}</span>
                            @if($session->timeLabel())
                                <span class="block opacity-80">{{ $session->timeLabel() }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
