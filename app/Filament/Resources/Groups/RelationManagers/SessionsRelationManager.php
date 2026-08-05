<?php

namespace App\Filament\Resources\Groups\RelationManagers;

use App\Models\GroupSession;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static ?string $title = 'Sessions';

    protected static ?string $modelLabel = 'Session';

    protected static ?string $pluralModelLabel = 'Sessions';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->native(false),

                TextInput::make('topic')
                    ->label('Topic')
                    ->maxLength(255)
                    ->placeholder('e.g. Freestyle technique'),
            ]),

            Grid::make(3)->schema([
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->seconds(false),

                TimePicker::make('end_time')
                    ->label('End Time')
                    ->seconds(false)
                    ->after('start_time'),

                TextInput::make('fee')
                    ->label('Fee')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->helperText('Optional session fee.'),
            ]),

            TextInput::make('location')
                ->label('Location')
                ->maxLength(255)
                ->placeholder($this->getOwnerRecord()->location ?: 'Same as group')
                ->columnSpanFull(),

            Select::make('status')
                ->label('Status')
                ->options(GroupSession::statusOptions())
                ->default('scheduled')
                ->required()
                ->native(false),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->maxLength(1000)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('D, d M Y')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Time')
                    ->state(fn (GroupSession $record): ?string => $record->timeLabel())
                    ->placeholder('—'),

                TextColumn::make('location')
                    ->label('Location')
                    ->state(fn (GroupSession $record): ?string => $record->resolvedLocation())
                    ->placeholder('—'),

                TextColumn::make('fee')
                    ->label('Fee')
                    ->formatStateUsing(fn ($state): string => $state !== null ? 'Rp'.number_format((float) $state, 0, ',', '.') : '—')
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GroupSession::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GroupSession::statusOptions()),
            ])
            ->headerActions([
                Action::make('generate')
                    ->label('Generate from schedule')
                    ->icon('heroicon-o-sparkles')
                    ->color('gray')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->required()
                                ->default(now())
                                ->native(false),

                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->required()
                                ->after('start_date')
                                ->native(false),
                        ]),

                        TextInput::make('fee')
                            ->label('Fee per session')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->helperText('Optional. Applied to every generated session.'),
                    ])
                    ->action(function (array $data): void {
                        $group = $this->getOwnerRecord();

                        if (blank($group->days)) {
                            Notification::make()
                                ->warning()
                                ->title('No weekly schedule set')
                                ->body('Set the group\'s days first, then generate sessions.')
                                ->send();

                            return;
                        }

                        $count = $group->generateSessions(
                            Carbon::parse($data['start_date']),
                            Carbon::parse($data['end_date']),
                            $data['fee'] !== null && $data['fee'] !== '' ? (float) $data['fee'] : null,
                        );

                        Notification::make()
                            ->success()
                            ->title($count > 0 ? "{$count} session(s) generated" : 'No new sessions')
                            ->body($count > 0 ? null : 'All matching dates already have a session.')
                            ->send();
                    }),

                CreateAction::make()->label('Add Session'),
            ])
            ->recordActions([
                Action::make('markCompleted')
                    ->label('Mark completed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (GroupSession $record): bool => $record->status !== 'completed')
                    ->action(fn (GroupSession $record) => $record->update(['status' => 'completed'])),

                Action::make('markCancelled')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Mark this session as cancelled.')
                    ->visible(fn (GroupSession $record): bool => $record->status !== 'cancelled')
                    ->action(fn (GroupSession $record) => $record->update(['status' => 'cancelled'])),

                Action::make('billMembers')
                    ->label('Bill members')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Create an unpaid bill at this session\'s fee for every active member not yet billed.')
                    ->visible(fn (GroupSession $record): bool => $record->fee !== null)
                    ->action(function (GroupSession $record): void {
                        $count = $record->billActiveMembers();

                        Notification::make()
                            ->success()
                            ->title($count > 0 ? "{$count} member(s) billed" : 'No new bills')
                            ->body($count > 0 ? null : 'All active members are already billed for this session.')
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markCompleted')
                        ->label('Mark completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'completed']))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
