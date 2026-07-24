<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn ($record): ?string => $record->is_anonymous ? 'Tampil anonim' : null)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->limit(60)
                    ->searchable(),

                IconColumn::make('is_answered')
                    ->label('Dijawab')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_answered')
                    ->label('Status Jawaban')
                    ->trueLabel('Sudah Dijawab')
                    ->falseLabel('Belum Dijawab'),

                TernaryFilter::make('is_published')
                    ->label('Visibilitas')
                    ->trueLabel('Ditampilkan')
                    ->falseLabel('Disembunyikan'),

                Filter::make('unanswered')
                    ->label('Perlu Dijawab')
                    ->query(fn (Builder $query) => $query->where('is_answered', false)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
