<?php

namespace App\Filament\Resources\Groups\Schemas;

use App\Models\Group;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Group')
                ->description('A group (kelompok) inside a course institution.')
                ->icon('heroicon-o-rectangle-stack')
                ->schema([
                    Select::make('institution_id')
                        ->label('Course Institution')
                        ->relationship(
                            name: 'institution',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->where('has_groups', true),
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Group Name')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(120),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('level')
                            ->label('Level')
                            ->maxLength(60)
                            ->placeholder('Beginner / Intermediate / Advanced'),

                        Select::make('teacher_id')
                            ->label('Coach / Instructor')
                            ->relationship(name: 'teacher', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),

                    CheckboxList::make('days')
                        ->label('Days')
                        ->options(Group::dayOptions())
                        ->columns(4)
                        ->gridDirection('row')
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->seconds(false),

                        TimePicker::make('end_time')
                            ->label('End Time')
                            ->seconds(false)
                            ->after('start_time'),

                        TextInput::make('capacity')
                            ->label('Capacity')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Empty = unlimited'),
                    ]),

                    TextInput::make('location')
                        ->label('Location')
                        ->maxLength(255)
                        ->placeholder('Main Pool')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->onColor('success'),
                    ]),
                ]),
        ]);
    }
}
