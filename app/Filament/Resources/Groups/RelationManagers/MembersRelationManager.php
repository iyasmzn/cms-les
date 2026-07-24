<?php

namespace App\Filament\Resources\Groups\RelationManagers;

use App\Models\GroupMember;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Members';

    protected static ?string $modelLabel = 'Member';

    protected static ?string $pluralModelLabel = 'Members';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(120),

                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->native(false),
            ]),

            Grid::make(2)->schema([
                DatePicker::make('birth_date')
                    ->label('Birth Date'),

                TextInput::make('birth_place')
                    ->label('Birth Place')
                    ->maxLength(120),
            ]),

            Grid::make(2)->schema([
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(30),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(120),
            ]),

            Textarea::make('address')
                ->label('Address')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name')
                    ->maxLength(120),

                TextInput::make('parent_phone')
                    ->label('Parent/Guardian Phone')
                    ->tel()
                    ->maxLength(30),
            ]),

            Grid::make(2)->schema([
                Select::make('status')
                    ->label('Status')
                    ->options(GroupMember::statusOptions())
                    ->default('pending')
                    ->required()
                    ->native(false),

                DatePicker::make('joined_at')
                    ->label('Joined At'),
            ]),

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
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('registration_number')
                    ->label('Reg. No.')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GroupMember::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GroupMember::statusOptions()),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Member'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (GroupMember $record): bool => $record->status !== 'active')
                    ->action(function (GroupMember $record): void {
                        $record->update([
                            'status' => 'active',
                            'joined_at' => $record->joined_at ?? now(),
                        ]);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (GroupMember $record): bool => $record->status !== 'inactive')
                    ->action(fn (GroupMember $record) => $record->update(['status' => 'inactive'])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
