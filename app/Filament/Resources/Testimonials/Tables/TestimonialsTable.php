<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialsTable
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
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&background=3b82f6&color=fff&size=80')
                    ->circular()
                    ->width(40)
                    ->height(40),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => collect(array_filter([
                        $record->class_year ? 'Angkatan '.$record->class_year : null,
                        $record->graduation_year ? 'Lulus '.$record->graduation_year : null,
                    ]))->implode(' · ')),

                TextColumn::make('message')
                    ->label('Kesan & Pesan')
                    ->limit(80)
                    ->searchable()
                    ->wrap(),

                IconColumn::make('is_published')
                    ->label('Tampil')
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
                Filter::make('is_published')
                    ->label('Ditampilkan saja')
                    ->query(fn (Builder $query) => $query->where('is_published', true)),

                Filter::make('not_published')
                    ->label('Disembunyikan')
                    ->query(fn (Builder $query) => $query->where('is_published', false)),
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
