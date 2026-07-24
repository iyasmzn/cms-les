<?php

namespace App\Filament\Resources\SpmbRegistrations\Pages;

use App\Filament\Resources\SpmbRegistrations\SpmbRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpmbRegistration extends EditRecord
{
    protected static string $resource = SpmbRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
