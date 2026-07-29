<?php

namespace App\Filament\Resources\CoursePayments\Pages;

use App\Filament\Resources\CoursePayments\CoursePaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoursePayment extends EditRecord
{
    protected static string $resource = CoursePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
