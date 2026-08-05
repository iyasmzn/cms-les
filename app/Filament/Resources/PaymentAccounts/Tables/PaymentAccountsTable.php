<?php

namespace App\Filament\Resources\PaymentAccounts\Tables;

use App\Models\PaymentAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PaymentAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PaymentAccount::typeOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'qris' ? 'warning' : 'primary'),

                TextColumn::make('label')
                    ->label('Destination')
                    ->state(fn (PaymentAccount $record): string => $record->displayName())
                    ->weight('bold')
                    ->searchable(['label', 'bank_name']),

                TextColumn::make('account_number')
                    ->label('Account Number')
                    ->placeholder('—')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('account_holder')
                    ->label('Holder')
                    ->placeholder('—')
                    ->searchable(),

                ImageColumn::make('qris_image')
                    ->label('QRIS')
                    ->disk('public')
                    ->height(40)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(PaymentAccount::typeOptions()),

                TernaryFilter::make('is_active')
                    ->label('Active'),
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
