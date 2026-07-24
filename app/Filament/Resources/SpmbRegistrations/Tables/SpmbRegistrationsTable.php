<?php

namespace App\Filament\Resources\SpmbRegistrations\Tables;

use App\Models\AcademicYear;
use App\Models\RegistrationWave;
use App\Models\SpmbRegistration;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpmbRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')
                    ->label('No. Daftar')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->description(fn (SpmbRegistration $record): string => $record->previous_school),

                TextColumn::make('institution.short_name')
                    ->label('Jenjang')
                    ->state(fn (SpmbRegistration $record): ?string => $record->institution?->short_name ?? $record->institution?->name)
                    ->badge()
                    ->color(fn (SpmbRegistration $record): string => $record->institution?->color ?? 'gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable(),

                TextColumn::make('admissionPath.name')
                    ->label('Jalur')
                    ->badge()
                    ->color(fn (SpmbRegistration $record): string => $record->admissionPath?->color ?? 'gray')
                    ->formatStateUsing(fn (?string $state, SpmbRegistration $record): string => trim(($record->admissionPath?->icon ?? '').' '.($state ?? '—')))
                    ->placeholder('—'),

                TextColumn::make('academicYear.label')
                    ->label('Tahun Ajaran')
                    ->state(fn (SpmbRegistration $record): ?string => $record->academicYear?->label)
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('registrationWave.name')
                    ->label('Gelombang')
                    ->toggleable()
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('institution_id')
                    ->label('Jenjang')
                    ->relationship('institution', 'name')
                    ->native(false),

                Filter::make('periode')
                    ->label('Tahun Ajaran & Gelombang')
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(fn (): array => AcademicYear::query()
                                ->orderByDesc('is_active')
                                ->orderByDesc('year_start')
                                ->get()
                                ->mapWithKeys(fn (AcademicYear $year): array => [
                                    $year->id => $year->label.($year->is_active ? ' (Aktif)' : ''),
                                ])
                                ->all())
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('registration_wave_id', null)),

                        Select::make('registration_wave_id')
                            ->label('Gelombang')
                            ->options(fn (Get $get): array => blank($get('academic_year_id'))
                                ? []
                                : RegistrationWave::query()
                                    ->where('academic_year_id', $get('academic_year_id'))
                                    ->orderBy('start_date')
                                    ->pluck('name', 'id')
                                    ->all())
                            ->native(false)
                            ->disabled(fn (Get $get): bool => blank($get('academic_year_id')))
                            ->placeholder('Pilih tahun ajaran dahulu'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['academic_year_id'] ?? null, fn (Builder $q, $id) => $q->where('academic_year_id', $id))
                        ->when($data['registration_wave_id'] ?? null, fn (Builder $q, $id) => $q->where('registration_wave_id', $id)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($year = AcademicYear::find($data['academic_year_id'] ?? null)) {
                            $indicators[] = Indicator::make("T.A. {$year->label}")->removeField('academic_year_id');
                        }

                        if ($wave = RegistrationWave::find($data['registration_wave_id'] ?? null)) {
                            $indicators[] = Indicator::make("Gelombang: {$wave->name}")->removeField('registration_wave_id');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('admission_path_id')
                    ->label('Jalur')
                    ->relationship('admissionPath', 'name')
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SpmbRegistration::statusOptions())
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()->label('Kelola'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
