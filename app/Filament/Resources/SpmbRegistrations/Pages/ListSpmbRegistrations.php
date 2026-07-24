<?php

namespace App\Filament\Resources\SpmbRegistrations\Pages;

use App\Filament\Resources\SpmbRegistrations\SpmbRegistrationResource;
use App\Models\SpmbRegistration;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListSpmbRegistrations extends ListRecords
{
    protected static string $resource = SpmbRegistrationResource::class;

    /**
     * Header of the exported spreadsheet.
     *
     * @var array<int, string>
     */
    private const EXPORT_HEADINGS = [
        'No', 'Tahun Ajaran', 'Gelombang', 'Jalur', 'Nama Lengkap', 'NIK', 'Email', 'No. HP',
        'Tempat Lahir', 'Tanggal Lahir', 'Sekolah Asal', 'Kota Sekolah', 'Alamat',
        'Nama Orang Tua', 'No. HP Orang Tua', 'Status', 'Catatan', 'Tanggal Daftar',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(fn (): StreamedResponse => $this->exportToExcel()),
        ];
    }

    private function exportToExcel(): StreamedResponse
    {
        /** @var Builder<SpmbRegistration> $query */
        $query = $this->getFilteredTableQuery()
            ->with(['academicYear', 'registrationWave', 'admissionPath']);

        $filename = 'data-pendaftar-spmb-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($query): void {
            $writer = new Writer;
            $writer->openToFile('php://output');

            $writer->addRow(Row::fromValues(self::EXPORT_HEADINGS, (new Style)->setFontBold()));

            $number = 0;
            $statuses = SpmbRegistration::statusOptions();

            $query->orderBy('created_at')->chunk(200, function ($records) use ($writer, &$number, $statuses): void {
                foreach ($records as $record) {
                    $writer->addRow(Row::fromValues([
                        ++$number,
                        $record->academicYear?->label ?? '',
                        $record->registrationWave?->name ?? '',
                        $record->admissionPath?->name ?? '',
                        $record->full_name,
                        $record->nik ?? '',
                        $record->email ?? '',
                        $record->phone,
                        $record->birth_place ?? '',
                        $record->birth_date?->format('d/m/Y') ?? '',
                        $record->previous_school,
                        $record->previous_school_city ?? '',
                        $record->address ?? '',
                        $record->parent_name ?? '',
                        $record->parent_phone ?? '',
                        $statuses[$record->status] ?? $record->status,
                        $record->notes ?? '',
                        $record->created_at?->format('d/m/Y H:i') ?? '',
                    ]));
                }
            });

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
