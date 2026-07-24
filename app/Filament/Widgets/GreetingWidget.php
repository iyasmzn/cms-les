<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class GreetingWidget extends Widget
{
    protected string $view = 'filament.widgets.greeting-widget';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    /**
     * Shown to every authenticated panel user, regardless of role or
     * permissions, so everyone gets a welcome card on the dashboard.
     */
    public static function canView(): bool
    {
        return Filament::auth()?->check() ?? false;
    }

    /**
     * Timezone used for the greeting and date. The application stores
     * timestamps in UTC, so the dashboard is presented in Western
     * Indonesian Time (WIB) to match the school's local time.
     */
    private const TIMEZONE = 'Asia/Jakarta';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        /** @var User|null $user */
        $user = Filament::auth()?->user();

        $now = Carbon::now(self::TIMEZONE);

        return [
            'name' => $user?->name ?? '',
            'avatarUrl' => $user?->avatar_url,
            'roles' => $user?->getRoleNames()->all() ?? [],
            'greeting' => $this->timeGreeting($now),
            'greetingIcon' => $this->greetingIcon($now),
            'date' => $this->formattedDate($now),
            'time' => $now->format('H:i').' WIB',
            'siteName' => setting('site_name', config('app.name')),
        ];
    }

    private function timeGreeting(Carbon $now): string
    {
        return match (true) {
            $now->hour < 11 => 'Selamat pagi',
            $now->hour < 15 => 'Selamat siang',
            $now->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    /**
     * A heroicon name matching the time of day, shown beside the greeting.
     */
    private function greetingIcon(Carbon $now): string
    {
        return match (true) {
            $now->hour < 11 => 'heroicon-o-sun',
            $now->hour < 15 => 'heroicon-o-sun',
            $now->hour < 18 => 'heroicon-o-cloud',
            default => 'heroicon-o-moon',
        };
    }

    private function formattedDate(Carbon $now): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $days[$now->dayOfWeek].', '.$now->day.' '.$months[$now->month].' '.$now->year;
    }
}
