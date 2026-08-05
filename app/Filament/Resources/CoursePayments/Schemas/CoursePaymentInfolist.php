<?php

namespace App\Filament\Resources\CoursePayments\Schemas;

use App\Models\CoursePayment;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CoursePaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bill')
                ->description('Read-only overview. Use Change status to settle it, or Edit to correct the details.')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('member.full_name')
                            ->label('Member')
                            ->weight('bold'),

                        TextEntry::make('member.group.name')
                            ->label('Group')
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),

                        TextEntry::make('member.registration_number')
                            ->label('Registration No.')
                            ->fontFamily('mono')
                            ->placeholder('—'),
                    ]),

                    Grid::make(3)->schema([
                        TextEntry::make('session.date')
                            ->label('Session')
                            ->date('D, d M Y')
                            ->placeholder('General / ad-hoc'),

                        TextEntry::make('amount')
                            ->label('Amount')
                            ->weight('bold')
                            ->formatStateUsing(fn ($state): string => 'Rp'.number_format((float) $state, 0, ',', '.')),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => CoursePayment::statusOptions()[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'waived' => 'gray',
                                'review' => 'info',
                                default => 'warning',
                            }),
                    ]),

                    TextEntry::make('notes')
                        ->label('Admin Notes')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Section::make('Member Confirmation')
                ->description('What the member reported from their portal.')
                ->icon('heroicon-o-document-check')
                ->visible(fn (CoursePayment $record): bool => $record->submitted_at !== null)
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('method')
                            ->label('Method')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => $state ? (CoursePayment::methodOptions()[$state] ?? $state) : '—'),

                        TextEntry::make('paymentAccount.bank_name')
                            ->label('Paid To')
                            ->state(fn (CoursePayment $record): ?string => $record->paymentAccount?->displayName())
                            ->placeholder('—'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted')
                            ->dateTime('d M Y H:i')
                            ->placeholder('—'),
                    ]),

                    TextEntry::make('payer_note')
                        ->label('Member Note')
                        ->placeholder('—')
                        ->columnSpanFull(),

                    ImageEntry::make('proof_path')
                        ->label('Proof of Payment')
                        ->disk('public')
                        ->height(320)
                        ->placeholder('No proof attached')
                        ->columnSpanFull(),
                ]),

            Section::make('Settlement')
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('paid_at')
                            ->label('Paid At')
                            ->dateTime('d M Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('rejected_at')
                            ->label('Last Rejected')
                            ->dateTime('d M Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->color('danger')
                            ->placeholder('—'),
                    ]),
                ]),
        ]);
    }
}
