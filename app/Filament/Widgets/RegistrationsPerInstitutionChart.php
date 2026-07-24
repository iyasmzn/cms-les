<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Institution;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class RegistrationsPerInstitutionChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1];

    public ?string $filter = 'all';

    public function getHeading(): ?string
    {
        return 'Pendaftar per Jenjang';
    }

    /** @return array<string, string> */
    protected function getFilters(): ?array
    {
        $filters = ['all' => 'Semua Tahun Ajaran'];

        foreach (AcademicYear::query()->orderByDesc('year_start')->get() as $year) {
            $filters[(string) $year->id] = "T.A. {$year->label}";
        }

        return $filters;
    }

    /** @return array<string, mixed> */
    protected function getData(): array
    {
        $institutions = Institution::query()
            ->ordered()
            ->withCount(['registrations' => function (Builder $query): void {
                if ($this->filter !== null && $this->filter !== 'all') {
                    $query->where('academic_year_id', $this->filter);
                }
            }])
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $institutions->pluck('registrations_count')->all(),
                    'backgroundColor' => $institutions->map(fn (Institution $institution): string => $this->colorHex($institution->color))->all(),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $institutions->map(fn (Institution $institution): string => $institution->short_name ?: $institution->name)->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Map a Filament color name to a hex value usable by Chart.js.
     */
    private function colorHex(?string $color): string
    {
        return match ($color) {
            'primary' => '#d97706',
            'info' => '#3b82f6',
            'success' => '#16a34a',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            default => '#6b7280',
        };
    }
}
