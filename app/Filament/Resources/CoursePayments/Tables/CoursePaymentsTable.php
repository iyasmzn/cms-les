<?php

namespace App\Filament\Resources\CoursePayments\Tables;

use App\Models\CoursePayment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CoursePaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('member.full_name')
                    ->label('Member')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('member.group.name')
                    ->label('Group')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('session.date')
                    ->label('Session')
                    ->date('d M Y')
                    ->placeholder('General'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state): string => 'Rp'.number_format((float) $state, 0, ',', '.'))
                    ->alignEnd()
                    ->summarize(Sum::make()
                        ->formatStateUsing(fn ($state): string => 'Rp'.number_format((float) $state, 0, ',', '.'))),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CoursePayment::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'waived' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('method')
                    ->label('Method')
                    ->formatStateUsing(fn (?string $state): string => $state ? (CoursePayment::methodOptions()[$state] ?? $state) : '—'),

                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CoursePayment::statusOptions()),

                SelectFilter::make('group')
                    ->label('Group')
                    ->relationship('member.group', 'name'),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CoursePayment $record): bool => $record->status !== 'paid')
                    ->schema([
                        Select::make('method')
                            ->label('Method')
                            ->options(CoursePayment::methodOptions())
                            ->default('cash')
                            ->native(false),
                    ])
                    ->action(fn (CoursePayment $record, array $data) => $record->markPaid($data['method'] ?? null)),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markPaid')
                        ->label('Mark paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->markPaid())
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
