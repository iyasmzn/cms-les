<?php

namespace App\Filament\Resources\Institutions\RelationManagers;

use App\Models\Group;
use App\Models\Institution;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Groups';

    protected static ?string $modelLabel = 'Group';

    protected static ?string $pluralModelLabel = 'Groups';

    /**
     * Groups only apply to institutions run as a course (les).
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Institution && $ownerRecord->has_groups;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Group Name')
                ->required()
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Used in the public URL (/courses/institution/slug).'),

                TextInput::make('level')
                    ->label('Level')
                    ->maxLength(60)
                    ->placeholder('Beginner / Intermediate / Advanced'),
            ]),

            Grid::make(2)->schema([
                Select::make('teacher_id')
                    ->label('Coach / Instructor')
                    ->relationship(
                        name: 'teacher',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('capacity')
                    ->label('Capacity (seats)')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Leave empty for unlimited.'),
            ]),

            CheckboxList::make('days')
                ->label('Days')
                ->options(Group::dayOptions())
                ->columns(4)
                ->gridDirection('row'),

            Grid::make(3)->schema([
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->seconds(false),

                TimePicker::make('end_time')
                    ->label('End Time')
                    ->seconds(false)
                    ->after('start_time'),

                TextInput::make('location')
                    ->label('Location')
                    ->maxLength(255)
                    ->placeholder('Main Pool'),
            ]),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->onColor('success')
                ->helperText('Inactive groups are hidden from the public page and closed for registration.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Group')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('teacher.name')
                    ->label('Coach')
                    ->placeholder('—'),

                TextColumn::make('schedule')
                    ->label('Schedule')
                    ->state(fn (Group $record): ?string => $record->scheduleLabel())
                    ->placeholder('—'),

                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge()
                    ->formatStateUsing(fn (int $state, $record): string => $record->capacity ? "{$state} / {$record->capacity}" : (string) $state),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Group'),
            ])
            ->recordActions([
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
