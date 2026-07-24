<?php

namespace App\Filament\Resources\FloatingButtons\Tables;

use App\Models\FloatingButton;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FloatingButtonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(40),

                TextColumn::make('icon')
                    ->label('Ikon')
                    ->width(50),

                ImageColumn::make('icon_image')
                    ->label('Ikon (gambar)')
                    ->disk('public')
                    ->height(32)
                    ->width(32),

                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->color('gray'),

                TextColumn::make('color')
                    ->label('Warna')
                    ->state(fn (FloatingButton $record): string => $record->color)
                    ->badge()
                    ->color(fn (FloatingButton $record): string => 'gray'),

                IconColumn::make('open_in_new_tab')
                    ->label('Tab Baru')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([])
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
