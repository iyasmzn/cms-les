<?php

namespace App\Filament\Resources\Popups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PopupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(40),

                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->width(80)
                    ->height(60)
                    ->defaultImageUrl(fn ($record) => null)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->description(fn ($record) => Str::limit($record->content, 60))
                    ->placeholder('(tanpa judul)'),

                TextColumn::make('width')
                    ->label('Lebar')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sm' => 'gray',
                        'md' => 'info',
                        'lg' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'sm' => 'Kecil',
                        'md' => 'Sedang',
                        'lg' => 'Besar',
                        default => $state,
                    }),

                TextColumn::make('delay_seconds')
                    ->label('Tunda')
                    ->formatStateUsing(fn (int $state) => $state > 0 ? "{$state}d" : 'Langsung')
                    ->toggleable(),

                TextColumn::make('show_every_days')
                    ->label('Frekuensi')
                    ->formatStateUsing(fn (int $state) => $state === 0 ? 'Per sesi' : "Tiap {$state}h")
                    ->toggleable(),

                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->label('Sampai')
                    ->dateTime('d M Y')
                    ->placeholder('—')
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
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
