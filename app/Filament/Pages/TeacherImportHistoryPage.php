<?php

namespace App\Filament\Pages;

use App\Services\TeacherImportHistory;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TeacherImportHistoryPage extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.teacher-import-history-page';

    // Grup 'Profil Sekolah' sudah punya ikon; Filament v5 melarang grup & itemnya
    // sama-sama ber-ikon, jadi halaman ini sengaja tanpa navigationIcon.
    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $navigationLabel = 'Riwayat Import Guru';

    protected static ?string $title = 'Riwayat Import Guru';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'riwayat-import-guru';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'entries' => app(TeacherImportHistory::class)->all(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear')
                ->label('Hapus Semua Riwayat')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->outlined()
                ->visible(fn (): bool => app(TeacherImportHistory::class)->all() !== [])
                ->requiresConfirmation()
                ->modalHeading('Hapus semua riwayat import?')
                ->modalDescription('Seluruh riwayat import akan dihapus permanen dari penyimpanan. Tindakan ini tidak dapat dibatalkan.')
                ->action(function (): void {
                    app(TeacherImportHistory::class)->clear();

                    Notification::make()
                        ->title('Riwayat import dihapus')
                        ->success()
                        ->send();
                }),
        ];
    }
}
