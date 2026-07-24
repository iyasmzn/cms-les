<?php

namespace App\Filament\Resources\SpmbRegistrations;

use App\Filament\Resources\SpmbRegistrations\Pages\EditSpmbRegistration;
use App\Filament\Resources\SpmbRegistrations\Pages\ListSpmbRegistrations;
use App\Filament\Resources\SpmbRegistrations\Schemas\SpmbRegistrationForm;
use App\Filament\Resources\SpmbRegistrations\Tables\SpmbRegistrationsTable;
use App\Models\SpmbRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SpmbRegistrationResource extends Resource
{
    protected static ?string $model = SpmbRegistration::class;

    protected static string|UnitEnum|null $navigationGroup = 'PPDB / SPMB';

    protected static ?string $navigationLabel = 'Data Pendaftar';

    protected static ?string $modelLabel = 'Pendaftar';

    protected static ?string $pluralModelLabel = 'Data Pendaftar';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SpmbRegistrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpmbRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpmbRegistrations::route('/'),
            'edit' => EditSpmbRegistration::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }
}
