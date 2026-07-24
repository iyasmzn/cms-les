<?php

namespace App\Filament\Resources\ContactItems;

use App\Filament\Resources\ContactItems\Pages\CreateContactItem;
use App\Filament\Resources\ContactItems\Pages\EditContactItem;
use App\Filament\Resources\ContactItems\Pages\ListContactItems;
use App\Filament\Resources\ContactItems\Schemas\ContactItemForm;
use App\Filament\Resources\ContactItems\Tables\ContactItemsTable;
use App\Models\ContactItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ContactItemResource extends Resource
{
    protected static ?string $model = ContactItem::class;

    /**
     * Retired: contact data is now managed centrally in GeneralSettings
     * (the `contact_*` / `social_*` settings) to avoid duplicate sources.
     * The resource is hidden from navigation but kept so existing data is
     * not lost; it can be removed once the legacy table is dropped.
     */
    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = 'Interaksi';

    protected static ?string $navigationLabel = 'Kontak';

    protected static ?string $modelLabel = 'Item Kontak';

    protected static ?string $pluralModelLabel = 'Informasi Kontak';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ContactItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactItems::route('/'),
            'create' => CreateContactItem::route('/create'),
            'edit' => EditContactItem::route('/{record}/edit'),
        ];
    }
}
