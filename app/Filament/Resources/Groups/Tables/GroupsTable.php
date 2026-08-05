<?php

namespace App\Filament\Resources\Groups\Tables;

use App\Models\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('institution.name')
                    ->label('Course')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

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
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge()
                    ->formatStateUsing(fn (int $state, $record): string => $record->capacity ? "{$state} / {$record->capacity}" : (string) $state),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('institution')
                    ->relationship('institution', 'name')
                    ->label('Course'),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
