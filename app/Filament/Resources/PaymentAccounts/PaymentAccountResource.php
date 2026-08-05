<?php

namespace App\Filament\Resources\PaymentAccounts;

use App\Filament\Resources\PaymentAccounts\Pages\CreatePaymentAccount;
use App\Filament\Resources\PaymentAccounts\Pages\EditPaymentAccount;
use App\Filament\Resources\PaymentAccounts\Pages\ListPaymentAccounts;
use App\Filament\Resources\PaymentAccounts\Schemas\PaymentAccountForm;
use App\Filament\Resources\PaymentAccounts\Tables\PaymentAccountsTable;
use App\Models\PaymentAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PaymentAccountResource extends Resource
{
    protected static ?string $model = PaymentAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?string $navigationLabel = 'Payment Accounts';

    protected static ?string $modelLabel = 'Payment Account';

    protected static ?string $pluralModelLabel = 'Payment Accounts';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return PaymentAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentAccounts::route('/'),
            'create' => CreatePaymentAccount::route('/create'),
            'edit' => EditPaymentAccount::route('/{record}/edit'),
        ];
    }
}
