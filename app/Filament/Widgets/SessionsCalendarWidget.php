<?php

namespace App\Filament\Widgets;

use App\Models\GroupSession;
use App\Support\CalendarMonth;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class SessionsCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.sessions-calendar';

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * The month currently shown, as "YYYY-MM".
     */
    public ?string $month = null;

    /**
     * Only shown when there is at least one course group to schedule, so
     * schools not running courses don't get an empty calendar.
     */
    public static function canView(): bool
    {
        return (Filament::auth()?->check() ?? false)
            && GroupSession::query()->exists();
    }

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = CalendarMonth::fromString($this->month)->previousParam();
    }

    public function nextMonth(): void
    {
        $this->month = CalendarMonth::fromString($this->month)->nextParam();
    }

    public function goToday(): void
    {
        $this->month = now()->format('Y-m');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $calendar = CalendarMonth::fromString($this->month);

        $sessions = GroupSession::query()
            ->whereBetween('date', [$calendar->rangeStart()->toDateString(), $calendar->rangeEnd()->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('group.institution')
            ->ordered()
            ->get()
            ->groupBy(fn (GroupSession $session): string => $session->date->toDateString());

        return compact('calendar', 'sessions');
    }
}
