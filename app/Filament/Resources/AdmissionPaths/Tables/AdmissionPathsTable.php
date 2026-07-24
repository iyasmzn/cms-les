<?php

namespace App\Filament\Resources\AdmissionPaths\Tables;

use App\Models\AdmissionPath;
use App\Models\Institution;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdmissionPathsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('institutions'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jalur')
                    ->badge()
                    ->color(fn (AdmissionPath $record): string => $record->color)
                    ->formatStateUsing(fn (AdmissionPath $record): string => trim("{$record->icon} {$record->name}"))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('institutions')
                    ->label('Khusus Jenjang')
                    ->badge()
                    ->color('gray')
                    ->state(fn (AdmissionPath $record): array => $record->institutions->isEmpty()
                        ? ['Semua jenjang']
                        : $record->institutions->map(fn (Institution $institution): string => $institution->short_name ?: $institution->name)->all()),

                TextColumn::make('registrations_count')
                    ->label('Pendaftar')
                    ->counts('registrations')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
