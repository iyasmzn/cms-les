<?php

namespace App\Filament\Resources\AuthorRequests;

use App\Filament\Resources\AuthorRequests\Pages\EditAuthorRequest;
use App\Filament\Resources\AuthorRequests\Pages\ListAuthorRequests;
use App\Filament\Resources\AuthorRequests\Schemas\AuthorRequestForm;
use App\Filament\Resources\AuthorRequests\Tables\AuthorRequestsTable;
use App\Models\AuthorRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AuthorRequestResource extends Resource
{
    protected static ?string $model = AuthorRequest::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|UnitEnum|null $navigationGroup = 'Interaksi';

    protected static ?string $navigationLabel = 'Permintaan Author';

    protected static ?string $modelLabel = 'Permintaan Author';

    protected static ?string $pluralModelLabel = 'Permintaan Author';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return AuthorRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuthorRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthorRequests::route('/'),
            'edit' => EditAuthorRequest::route('/{record}/edit'),
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
