<?php

namespace App\Filament\Resources\CoursePayments\Pages;

use App\Filament\Resources\CoursePayments\CoursePaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoursePayments extends ListRecords
{
    protected static string $resource = CoursePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
