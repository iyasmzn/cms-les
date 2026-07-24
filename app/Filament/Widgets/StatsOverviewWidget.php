<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Media;
use App\Models\Post;
use App\Models\SpmbRegistration;
use App\Models\Teacher;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $thisMonth = $now->month;
        $thisYear = $now->year;
        $lastMonth = $now->copy()->subMonth();

        $postsThisMonth = Post::query()
            ->whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();

        $postsLastMonth = Post::query()
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $postsTrend = collect(
            range(5, 0)
        )->map(fn ($monthsAgo) => Post::query()
            ->whereYear('created_at', $now->copy()->subMonths($monthsAgo)->year)
            ->whereMonth('created_at', $now->copy()->subMonths($monthsAgo)->month)
            ->count()
        )->toArray();

        $spmbTotal = SpmbRegistration::count();
        $spmbThisMonth = SpmbRegistration::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();
        $spmbPending = SpmbRegistration::where('status', 'pending')->count();

        $spmbTrend = collect(range(5, 0))->map(fn ($monthsAgo) => SpmbRegistration::query()
            ->whereYear('created_at', $now->copy()->subMonths($monthsAgo)->year)
            ->whereMonth('created_at', $now->copy()->subMonths($monthsAgo)->month)
            ->count()
        )->toArray();

        $pendingComments = Comment::where('is_approved', false)->count();

        $upcomingEvents = Event::where('is_published', true)
            ->where('starts_at', '>=', $now)
            ->count();

        $mediaTotal = Media::count();

        $teacherTotal = Teacher::where('is_active', true)->count();

        return array_values(array_filter([
            Stat::make('Total Postingan', Post::where('is_published', true)->count())
                ->description($postsThisMonth.' baru bulan ini'.($postsLastMonth > 0 ? ' (bulan lalu: '.$postsLastMonth.')' : ''))
                ->descriptionIcon(Heroicon::Newspaper)
                ->chart($postsTrend)
                ->color('primary'),

            Stat::make('Pendaftaran SPMB', $spmbTotal)
                ->description($spmbThisMonth.' bulan ini · '.$spmbPending.' menunggu verifikasi')
                ->descriptionIcon(Heroicon::ClipboardDocumentCheck)
                ->chart($spmbTrend)
                ->color('warning'),

            Stat::make('Komentar Perlu Moderasi', $pendingComments)
                ->description('Dari total '.Comment::count().' komentar masuk')
                ->descriptionIcon(Heroicon::ChatBubbleBottomCenterText)
                ->color($pendingComments > 0 ? 'warning' : 'success'),

            Stat::make('Event Mendatang', $upcomingEvents)
                ->description('Dari total '.Event::where('is_published', true)->count().' event aktif')
                ->descriptionIcon(Heroicon::CalendarDays)
                ->color('info'),

            Stat::make('Guru & Staf Aktif', $teacherTotal)
                ->description($mediaTotal.' file media tersimpan')
                ->descriptionIcon(Heroicon::Users)
                ->color('success'),
        ]));
    }
}
