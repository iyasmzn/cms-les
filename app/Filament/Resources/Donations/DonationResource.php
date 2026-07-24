<?php

namespace App\Filament\Resources\Donations;

use App\Filament\Concerns\IndicatesFeatureStatus;
use App\Filament\Resources\Donations\Pages\EditDonation;
use App\Filament\Resources\Donations\Pages\ListDonations;
use App\Filament\Resources\Donations\Schemas\DonationForm;
use App\Filament\Resources\Donations\Tables\DonationsTable;
use App\Models\Donation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DonationResource extends Resource
{
    use IndicatesFeatureStatus;

    protected static string $feature = 'donasi';

    protected static ?string $model = Donation::class;

    protected static string|UnitEnum|null $navigationGroup = 'Donasi';

    protected static ?string $navigationLabel = 'Data Donasi';

    protected static ?string $modelLabel = 'Donasi';

    protected static ?string $pluralModelLabel = 'Data Donasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DonationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonations::route('/'),
            'edit' => EditDonation::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadgeWhenEnabled(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    protected static function getNavigationBadgeColorWhenEnabled(): string|array|null
    {
        return 'warning';
    }
}
