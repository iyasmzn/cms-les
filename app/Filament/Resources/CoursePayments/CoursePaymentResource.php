<?php

namespace App\Filament\Resources\CoursePayments;

use App\Filament\Resources\CoursePayments\Pages\CreateCoursePayment;
use App\Filament\Resources\CoursePayments\Pages\EditCoursePayment;
use App\Filament\Resources\CoursePayments\Pages\ListCoursePayments;
use App\Filament\Resources\CoursePayments\Schemas\CoursePaymentForm;
use App\Filament\Resources\CoursePayments\Tables\CoursePaymentsTable;
use App\Models\CoursePayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CoursePaymentResource extends Resource
{
    protected static ?string $model = CoursePayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $pluralModelLabel = 'Payments';

    public static function form(Schema $schema): Schema
    {
        return CoursePaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursePaymentsTable::configure($table);
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
            'index' => ListCoursePayments::route('/'),
            'create' => CreateCoursePayment::route('/create'),
            'edit' => EditCoursePayment::route('/{record}/edit'),
        ];
    }
}
