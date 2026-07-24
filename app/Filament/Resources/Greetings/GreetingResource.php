<?php

namespace App\Filament\Resources\Greetings;

use App\Filament\Resources\Greetings\Pages\CreateGreeting;
use App\Filament\Resources\Greetings\Pages\EditGreeting;
use App\Filament\Resources\Greetings\Pages\ListGreetings;
use App\Filament\Resources\Greetings\Schemas\GreetingForm;
use App\Filament\Resources\Greetings\Tables\GreetingsTable;
use App\Models\Greeting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class GreetingResource extends Resource
{
    protected static ?string $model = Greeting::class;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $navigationLabel = 'Sambutan Tokoh';

    protected static ?string $modelLabel = 'Sambutan Tokoh';

    protected static ?string $pluralModelLabel = 'Sambutan Tokoh';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return GreetingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GreetingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGreetings::route('/'),
            'create' => CreateGreeting::route('/create'),
            'edit' => EditGreeting::route('/{record}/edit'),
        ];
    }
}
