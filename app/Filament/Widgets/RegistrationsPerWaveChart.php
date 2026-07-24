<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\RegistrationWave;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class RegistrationsPerWaveChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1];

    public ?string $filter = null;

    /**
     * Palette cycled across waves.
     *
     * @var array<int, string>
     */
    private const PALETTE = ['#d97706', '#3b82f6', '#16a34a', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#6b7280'];

    public function mount(): void
    {
        // Selalu terfilter ke satu tahun ajaran; default ke tahun ajaran aktif.
        $this->filter ??= (string) (AcademicYear::active()?->id
            ?? AcademicYear::query()->orderByDesc('year_start')->value('id')
            ?? '');

        parent::mount();
    }

    public function getHeading(): ?string
    {
        return 'Pendaftar per Gelombang';
    }

    protected function getFilters(): ?array
    {
        $filters = [];

        $years = AcademicYear::query()
            ->orderByDesc('is_active')
            ->orderByDesc('year_start')
            ->get();

        foreach ($years as $year) {
            $filters[(string) $year->id] = "T.A. {$year->label}".($year->is_active ? ' (Aktif)' : '');
        }

        return $filters;
    }

    protected function getData(): array
    {
        $waves = RegistrationWave::query()
            ->withCount('registrations')
            ->where('academic_year_id', $this->filter)
            ->orderBy('start_date')
            ->get();

        $labels = $waves->map(fn (RegistrationWave $wave): string => $wave->name);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $waves->pluck('registrations_count')->all(),
                    'backgroundColor' => $waves->keys()->map(fn (int $i): string => self::PALETTE[$i % count(self::PALETTE)])->all(),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
