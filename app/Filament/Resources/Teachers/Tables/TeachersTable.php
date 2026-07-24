<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(40),

                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name={$record->name}&background=d97706&color=fff&size=80")
                    ->circular()
                    ->width(40)
                    ->height(40),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->nip),

                TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('institution.name')
                    ->label('Instansi / Unit')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('subject')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('education')
                    ->label('Pendidikan')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('institution')
                    ->label('Instansi / Unit')
                    ->relationship('institution', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('position')
                    ->label('Jabatan')
                    ->options(fn () => Category::optionsForType(Category::TYPE_TEACHER))
                    ->native(false),

                Filter::make('is_active')
                    ->label('Aktif saja')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),

                Filter::make('not_active')
                    ->label('Tidak aktif')
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
