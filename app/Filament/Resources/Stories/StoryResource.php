<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStory;
use App\Filament\Resources\Stories\Pages\EditStory;
use App\Filament\Resources\Stories\Pages\ListStories;
use App\Filament\Resources\Stories\Schemas\StoryForm;
use App\Filament\Resources\Stories\Tables\StoriesTable;
use App\Models\Story;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StoryResource extends Resource
{
    protected static ?string $model = Story::class;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $navigationLabel = 'Cerita Santri';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Cerita';

    protected static ?string $pluralModelLabel = 'Cerita Santri';

    public static function form(Schema $schema): Schema
    {
        return StoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStories::route('/'),
            'create' => CreateStory::route('/create'),
            'edit' => EditStory::route('/{record}/edit'),
        ];
    }
}
