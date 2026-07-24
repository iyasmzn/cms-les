<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\SpmbRegistration;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RegistrationsPerYearStats extends Widget
{
    use HasWidgetShield;

    protected string $view = 'filament.widgets.registrations-per-year-stats';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    /**
     * Selected inactive academic year for the "total" card. Null = all years.
     */
    public ?int $totalYearId = null;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $active = AcademicYear::active();

        /** @var Collection<int, AcademicYear> $inactiveYears */
        $inactiveYears = AcademicYear::query()
            ->where('is_active', false)
            ->orderByDesc('year_start')
            ->get();

        $selected = $this->totalYearId
            ? $inactiveYears->firstWhere('id', $this->totalYearId)
            : null;

        return [
            'activeLabel' => $active ? "T.A. {$active->label}" : 'Tahun Ajaran Aktif',
            'activeCount' => $active ? $active->registrations()->count() : 0,
            'hasActive' => $active !== null,
            'totalLabel' => $selected ? "T.A. {$selected->label}" : 'Total Semua Tahun',
            'totalCount' => $selected ? $selected->registrations()->count() : SpmbRegistration::count(),
            'isFiltered' => $selected !== null,
            'inactiveYears' => $inactiveYears,
        ];
    }
}
