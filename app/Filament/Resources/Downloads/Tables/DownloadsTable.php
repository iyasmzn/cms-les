<?php

namespace App\Filament\Resources\Downloads\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DownloadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->description(fn ($record) => $record->original_filename),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('file_size_label')
                    ->label('Ukuran')
                    ->state(fn ($record) => $record->file_size_label)
                    ->toggleable(),

                TextColumn::make('download_count')
                    ->label('Unduhan')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn () => Category::optionsForType(Category::TYPE_DOWNLOAD))
                    ->native(false),

                Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),

                Filter::make('is_inactive')
                    ->label('Hanya Non-Aktif')
                    ->query(fn (Builder $query) => $query->where('is_active', false)),
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
