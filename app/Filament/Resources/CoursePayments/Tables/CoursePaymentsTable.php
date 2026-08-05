<?php

namespace App\Filament\Resources\CoursePayments\Tables;

use App\Models\CoursePayment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                        'review' => 'info',
                        default => 'warning',
                    })
                    ->description(fn (CoursePayment $record): ?string => $record->isAwaitingVerification()
                        ? 'Submitted '.$record->submitted_at?->diffForHumans()
                        : ($record->rejection_reason ? 'Rejected: '.$record->rejection_reason : null)),

                ImageColumn::make('proof_path')
                    ->label('Proof')
                    ->disk('public')
                    ->height(40)
                    ->placeholder('—'),

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

                Filter::make('awaiting_verification')
                    ->label('Awaiting verification')
                    ->query(fn (Builder $query) => $query->awaitingVerification()),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(fn (CoursePayment $record): bool => $record->isAwaitingVerification())
                    ->modalHeading('Verify payment')
                    ->modalDescription('Confirm the member really settled this bill. The proof they uploaded is shown in the Proof column.')
                    ->modalSubmitActionLabel('Mark as paid')
                    ->action(function (CoursePayment $record): void {
                        $record->markPaid();

                        Notification::make()
                            ->success()
                            ->title('Payment verified')
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CoursePayment $record): bool => $record->isAwaitingVerification())
                    ->modalHeading('Reject confirmation')
                    ->modalDescription('The bill goes back to unpaid and the member sees your reason in their portal.')
                    ->schema([
                        TextInput::make('reason')
                            ->label('Reason')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Transfer amount does not match the bill'),
                    ])
                    ->action(function (CoursePayment $record, array $data): void {
                        $record->reject($data['reason']);

                        Notification::make()
                            ->warning()
                            ->title('Confirmation rejected')
                            ->send();
                    }),

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
