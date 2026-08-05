<?php

namespace App\Filament\Resources\Groups\Schemas;

use App\Models\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Group')
                ->description('Read-only overview. Use Edit to change these details.')
                ->icon('heroicon-o-rectangle-stack')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('institution.name')
                            ->label('Course')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('name')
                            ->label('Group')
                            ->weight('bold'),

                        TextEntry::make('level')
                            ->label('Level')
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                    ]),

                    Grid::make(3)->schema([
                        TextEntry::make('teacher.name')
                            ->label('Coach')
                            ->placeholder('—'),

                        TextEntry::make('schedule')
                            ->label('Schedule')
                            ->state(fn (Group $record): ?string => $record->scheduleLabel())
                            ->placeholder('—'),

                        TextEntry::make('location')
                            ->label('Location')
                            ->placeholder('—'),
                    ]),

                    Grid::make(3)->schema([
                        TextEntry::make('seats')
                            ->label('Members')
                            ->state(fn (Group $record): string => $record->capacity
                                ? "{$record->takenSeats()} / {$record->capacity}"
                                : (string) $record->takenSeats())
                            ->badge()
                            ->color(fn (Group $record): string => $record->remainingSeats() === 0 ? 'danger' : 'success'),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),

                        TextEntry::make('open')
                            ->label('Accepting Members')
                            ->state(fn (Group $record): string => $record->isOpen() ? 'Open' : 'Closed')
                            ->badge()
                            ->color(fn (Group $record): string => $record->isOpen() ? 'success' : 'gray'),
                    ]),

                    TextEntry::make('description')
                        ->label('Description')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
