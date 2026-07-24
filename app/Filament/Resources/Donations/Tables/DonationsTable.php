<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Models\Donation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Donatur')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Donation $record): string => $record->email ?? $record->phone ?? ''),

                IconColumn::make('is_anonymous')
                    ->label('Anonim')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedEyeSlash)
                    ->falseIcon(Heroicon::OutlinedEye)
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.')),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => Donation::paymentMethodOptions()[$state] ?? $state),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Donation::statusOptions()[$state] ?? $state),

                TextColumn::make('created_at')
                    ->label('Tgl. Donasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options(Donation::paymentMethodOptions())
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Donation::statusOptions())
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
