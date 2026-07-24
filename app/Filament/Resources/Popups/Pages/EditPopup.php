<?php

namespace App\Filament\Resources\Popups\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Popups\PopupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPopup extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = PopupResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return self::applyImagePickers($data, ['image']);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
