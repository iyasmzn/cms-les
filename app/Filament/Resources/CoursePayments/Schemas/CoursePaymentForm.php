<?php

namespace App\Filament\Resources\CoursePayments\Schemas;

use App\Models\CoursePayment;
use App\Models\GroupMember;
use App\Models\GroupSession;
use Filament\Forms\Components\DateTimePicker;
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
        ]);
    }
}
