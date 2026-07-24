<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => "https://picsum.photos/seed/post-{$record->id}/80/50")
                    ->width(80)
                    ->height(50)
                    ->extraImgAttributes(['class' => 'rounded-md object-cover']),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->description(fn ($record) => $record->category),

                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('read_time')
                    ->label('Baca')
                    ->suffix(' mnt')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Tayang')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Tanggal Tayang')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn () => Category::optionsForType(Category::TYPE_POST))
                    ->native(false),

                Filter::make('is_published')
                    ->label('Hanya Tayang')
                    ->query(fn (Builder $query) => $query->where('is_published', true)),

                Filter::make('is_draft')
                    ->label('Hanya Draft')
                    ->query(fn (Builder $query) => $query->where('is_published', false)),
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
