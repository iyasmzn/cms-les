<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Post;
use App\Models\Story;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PostsChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Aktivitas Konten (6 Bulan Terakhir)';

    protected ?string $description = 'Postingan, event, dan kisah alumni yang diterbitkan';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));

        $labels = $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();

        $posts = $months->map(fn ($m) => Post::where('is_published', true)
            ->whereYear('published_at', $m->year)
            ->whereMonth('published_at', $m->month)
            ->count()
        )->toArray();

        $events = $months->map(fn ($m) => Event::where('is_published', true)
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->count()
        )->toArray();

        $stories = $months->map(fn ($m) => Story::where('is_published', true)
            ->whereYear('published_at', $m->year)
            ->whereMonth('published_at', $m->month)
            ->count()
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Postingan',
                    'data' => $posts,
                    'borderColor' => '#08484A',
                    'backgroundColor' => 'rgba(8,72,74,0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Event',
                    'data' => $events,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Kisah Alumni',
                    'data' => $stories,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
