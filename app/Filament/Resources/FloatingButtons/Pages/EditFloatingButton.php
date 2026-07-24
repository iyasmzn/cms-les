<?php

namespace App\Filament\Resources\FloatingButtons\Pages;

use App\Filament\Resources\FloatingButtons\FloatingButtonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFloatingButton extends EditRecord
{
    protected static string $resource = FloatingButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
