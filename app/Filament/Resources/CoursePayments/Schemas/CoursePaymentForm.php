<?php

namespace App\Filament\Resources\CoursePayments\Schemas;

use App\Models\CoursePayment;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\PaymentAccount;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CoursePaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Select::make('group_member_id')
                        ->label('Member')
                        ->relationship('member', 'full_name')
                        ->getOptionLabelFromRecordUsing(fn (GroupMember $record): string => "{$record->full_name} — ".($record->group?->name ?? 'Group'))
                        ->searchable(['full_name'])
                        ->preload()
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Select::make('group_session_id')
                        ->label('Session (optional)')
                        ->options(function (Get $get): array {
                            $memberId = $get('group_member_id');

                            if (! $memberId) {
                                return [];
                            }

                            $groupId = GroupMember::whereKey($memberId)->value('group_id');

                            return GroupSession::query()
                                ->where('group_id', $groupId)
                                ->orderByDesc('date')
                                ->get()
                                ->mapWithKeys(fn (GroupSession $session): array => [
                                    $session->id => $session->date->format('D, d M Y').($session->timeLabel() ? ' · '.$session->timeLabel() : ''),
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->placeholder('General / ad-hoc payment')
                        ->helperText('Leave empty for a payment not tied to a specific session.')
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('Rp'),

                        Select::make('status')
                            ->label('Status')
                            ->options(CoursePayment::statusOptions())
                            ->default('unpaid')
                            ->required()
                            ->native(false)
                            ->live(),

                        Select::make('method')
                            ->label('Method')
                            ->options(CoursePayment::methodOptions())
                            ->native(false)
                            ->placeholder('—'),
                    ]),

                    Select::make('payment_account_id')
                        ->label('Paid To')
                        ->relationship('paymentAccount', 'id')
                        ->getOptionLabelFromRecordUsing(fn (PaymentAccount $record): string => $record->displayName())
                        ->native(false)
                        ->placeholder('—')
                        ->helperText('The destination the member says they transferred to.')
                        ->visible(fn (Get $get): bool => in_array($get('method'), ['transfer', 'qris'], true))
                        ->columnSpanFull(),

                    DateTimePicker::make('paid_at')
                        ->label('Paid At')
                        ->seconds(false)
                        ->visible(fn (Get $get): bool => $get('status') === 'paid')
                        ->helperText('Leave empty to stamp the current time on save.'),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            Section::make('Member Confirmation')
                ->description('Filled in by the member from their portal. Verify or reject it from the payments table.')
                ->icon('heroicon-o-document-check')
                ->visible(fn (?CoursePayment $record): bool => $record?->submitted_at !== null)
                ->schema([
                    FileUpload::make('proof_path')
                        ->label('Proof of Payment')
                        ->image()
                        ->disk('public')
                        ->directory('payment-proofs')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->automaticallyResizeImagesToWidth('1600')
                        ->maxSize(4096)
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),

                    Textarea::make('payer_note')
                        ->label('Member Note')
                        ->rows(2)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        DateTimePicker::make('submitted_at')
                            ->label('Submitted At')
                            ->seconds(false)
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('rejection_reason')
                            ->label('Last Rejection Reason')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('—'),
                    ]),
                ]),
        ]);
    }
}
