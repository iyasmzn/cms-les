<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')
                    ->label('Pengguna')
                    ->description(fn ($record): ?string => $record->is_guest ? 'Tamu' : null)
                    ->state(fn ($record): string => $record->author_name)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('guest_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"))),

                TextColumn::make('post.title')
                    ->label('Artikel')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(60)
                    ->searchable(),

                IconColumn::make('admin_reply')
                    ->label('Dibalas')
                    ->boolean()
                    ->state(fn ($record): bool => filled($record->admin_reply)),

                IconColumn::make('is_approved')
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
                TernaryFilter::make('is_approved')
                    ->label('Visibilitas')
                    ->trueLabel('Ditampilkan')
                    ->falseLabel('Disembunyikan'),

                Filter::make('unreplied')
                    ->label('Belum Dibalas')
                    ->query(fn (Builder $query) => $query->whereNull('admin_reply')),
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
