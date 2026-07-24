<?php

namespace App\Filament\Resources\FloatingButtons;

use App\Filament\Resources\FloatingButtons\Pages\CreateFloatingButton;
use App\Filament\Resources\FloatingButtons\Pages\EditFloatingButton;
use App\Filament\Resources\FloatingButtons\Pages\ListFloatingButtons;
use App\Filament\Resources\FloatingButtons\Schemas\FloatingButtonForm;
use App\Filament\Resources\FloatingButtons\Tables\FloatingButtonsTable;
use App\Models\FloatingButton;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FloatingButtonResource extends Resource
{
    protected static ?string $model = FloatingButton::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tampilan Website';

    protected static ?string $navigationLabel = 'Floating Buttons';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Tombol';

    protected static ?string $pluralModelLabel = 'Floating Buttons';

    public static function form(Schema $schema): Schema
    {
        return FloatingButtonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FloatingButtonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFloatingButtons::route('/'),
            'create' => CreateFloatingButton::route('/create'),
            'edit' => EditFloatingButton::route('/{record}/edit'),
        ];
    }
}
