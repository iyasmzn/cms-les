<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->width(40),

                ImageColumn::make('icon_image')
                    ->label('Ikon (gambar)')
                    ->disk('public')
                    ->height(32)
                    ->width(32),

                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(56)
                    ->width(80),

                TextColumn::make('title')
                    ->label('Nama Program')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('excerpt')
                    ->label('Ringkasan')
                    ->limit(50)
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn () => Category::optionsForType(Category::TYPE_PROGRAM)),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
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
