<?php

namespace App\Filament\Widgets;

use App\Models\SpmbRegistration;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SpmbRegistrationsChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Pendaftaran SPMB per Bulan';

    protected ?string $description = 'Jumlah pendaftar berdasarkan status verifikasi';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));

        $labels = $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();

        $pending = $months->map(fn ($m) => SpmbRegistration::where('status', 'pending')
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->count()
        )->toArray();

        $verified = $months->map(fn ($m) => SpmbRegistration::where('status', 'verified')
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->count()
        )->toArray();

        $rejected = $months->map(fn ($m) => SpmbRegistration::where('status', 'rejected')
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->count()
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Menunggu Verifikasi',
                    'data' => $pending,
                    'backgroundColor' => 'rgba(245,158,11,0.8)',
                ],
                [
                    'label' => 'Terverifikasi',
                    'data' => $verified,
                    'backgroundColor' => 'rgba(8,72,74,0.8)',
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $rejected,
                    'backgroundColor' => 'rgba(239,68,68,0.8)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
