<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Pages\TeacherImportHistoryPage;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Services\TeacherImportHistory;
use App\Services\TeacherImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    /**
     * The most recent import run (history entry), used to populate the
     * results preview modal shown right after an import completes.
     *
     * @var array<string, mixed>|null
     */
    public ?array $lastImport = null;

    protected function getHeaderActions(): array
    {
        return [
            $this->historyAction(),
            $this->templateAction(),
            $this->exportAction(),
            $this->importAction(),
            CreateAction::make(),
            // Only after an import: lets the user reopen the results preview; it is
            // also mounted programmatically right after the import completes.
            $this->importResultAction(),
        ];
    }

    private function importAction(): Action
    {
        return Action::make('import')
            ->label('Import Excel')
            ->icon(Heroicon::ArrowUpTray)
            ->color('gray')
            ->modalHeading('Import Data Guru')
            ->modalDescription('Unggah file Excel (.xlsx) sesuai template. Baris dengan NIP yang sama akan diperbarui.')
            ->modalSubmitActionLabel('Import')
            ->schema([
                FileUpload::make('file')
                    ->label('File Excel (.xlsx)')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->disk('local')
                    ->directory('teacher-imports')
                    ->storeFiles()
                    ->storeFileNamesIn('original_filename'),
            ])
            ->action(function (array $data): void {
                $disk = Storage::disk('local');
                $path = $data['file'];

                try {
                    $result = app(TeacherImportService::class)->import($disk->path($path));
                } finally {
                    $disk->delete($path);
                }

                if ($result->total() === 0) {
                    Notification::make()
                        ->title('Tidak ada data')
                        ->body('File tidak berisi baris data untuk diimpor.')
                        ->warning()
                        ->send();

                    return;
                }

                $filename = is_string($data['original_filename'] ?? null)
                    ? $data['original_filename']
                    : basename((string) $path);

                $this->lastImport = app(TeacherImportHistory::class)->record($result, $filename);

                // Swap the upload modal for a results preview without closing it.
                $this->replaceMountedAction('importResult');
            });
    }

    private function importResultAction(): Action
    {
        return Action::make('importResult')
            ->label('Hasil Import Terakhir')
            ->icon(Heroicon::OutlinedDocumentArrowUp)
            ->color('gray')
            ->visible(fn (): bool => filled($this->lastImport))
            ->modalHeading(fn (): string => $this->importResultHeading())
            ->modalDescription('Rincian perubahan dari import terakhir. Catatan ini juga tersimpan di halaman Riwayat Import.')
            ->modalIcon(Heroicon::OutlinedDocumentArrowUp)
            ->modalIconColor($this->importResultColor())
            ->modalWidth(Width::FiveExtraLarge)
            ->modalContent(fn (): ?View => filled($this->lastImport)
                ? view('filament.teacher.import-detail', ['entry' => $this->lastImport])
                : null)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->extraModalFooterActions([
                Action::make('openHistory')
                    ->label('Buka Riwayat Import')
                    ->icon(Heroicon::OutlinedClock)
                    ->color('gray')
                    ->url(TeacherImportHistoryPage::getUrl()),
            ]);
    }

    private function historyAction(): Action
    {
        return Action::make('history')
            ->label('Riwayat Import')
            ->icon(Heroicon::OutlinedClock)
            ->color('gray')
            ->url(TeacherImportHistoryPage::getUrl());
    }

    private function importResultHeading(): string
    {
        $failed = (int) ($this->lastImport['failed'] ?? 0);
        $total = (int) ($this->lastImport['total'] ?? 0);

        return match (true) {
            $failed === 0 => 'Import Berhasil',
            $failed === $total => 'Import Gagal',
            default => 'Import Selesai — Sebagian Gagal',
        };
    }

    private function importResultColor(): string
    {
        $failed = (int) ($this->lastImport['failed'] ?? 0);
        $total = (int) ($this->lastImport['total'] ?? 0);

        return match (true) {
            $failed === 0 => 'success',
            $failed === $total => 'danger',
            default => 'warning',
        };
    }

    private function templateAction(): Action
    {
        return Action::make('downloadTemplate')
            ->label('Template')
            ->icon(Heroicon::ArrowDownTray)
            ->color('gray')
            ->action(function (): StreamedResponse {
                return response()->streamDownload(function (): void {
                    $temp = tempnam(sys_get_temp_dir(), 'teacher_template_').'.xlsx';

                    app(TeacherImportService::class)->writeTemplate($temp);

                    readfile($temp);

                    @unlink($temp);
                }, 'template-import-guru.xlsx', [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            });
    }

    private function exportAction(): Action
    {
        return Action::make('export')
            ->label('Export Excel')
            ->icon(Heroicon::ArrowDownTray)
            ->color('gray')
            ->action(function (): StreamedResponse {
                return response()->streamDownload(function (): void {
                    $temp = tempnam(sys_get_temp_dir(), 'teacher_export_').'.xlsx';

                    app(TeacherImportService::class)->writeExport($temp);

                    readfile($temp);

                    @unlink($temp);
                }, 'data-guru-'.now()->format('Y-m-d').'.xlsx', [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            });
    }
}
