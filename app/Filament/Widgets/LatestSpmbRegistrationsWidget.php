<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SpmbRegistrations\SpmbRegistrationResource;
use App\Models\SpmbRegistration;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSpmbRegistrationsWidget extends TableWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pendaftar SPMB Terbaru';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat_semua')
                ->label('Lihat Semua')
                ->url(SpmbRegistrationResource::getUrl('index'))
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->size('sm'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => SpmbRegistration::query()
                ->with('admissionPath')
                ->orderByDesc('created_at')
                ->limit(8)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->description(fn (SpmbRegistration $record): string => $record->previous_school ?? '-'),

                TextColumn::make('phone')
                    ->label('No. HP'),

                TextColumn::make('admissionPath.name')
                    ->label('Jalur')
                    ->badge()
                    ->color(fn (SpmbRegistration $record): string => $record->admissionPath?->color ?? 'gray')
                    ->formatStateUsing(fn (?string $state, SpmbRegistration $record): string => trim(($record->admissionPath?->icon ?? '').' '.($state ?? '—')))
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => SpmbRegistration::statusOptions()[$state] ?? $state),

                TextColumn::make('created_at')
                    ->label('Tgl. Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
