<?php

namespace App\Filament\Resources\ContactItems\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->width('60px'),

                ImageColumn::make('icon_image')
                    ->label('Ikon (gambar)')
                    ->disk('public')
                    ->height(32)
                    ->width(32),

                TextColumn::make('label')
                    ->label('Judul')
                    ->sortable()
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(50)
                    ->color('gray'),

                TextColumn::make('link')
                    ->label('Tautan')
                    ->limit(40)
                    ->url(fn ($record) => $record->link)
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
