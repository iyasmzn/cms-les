<?php

namespace App\Filament\Resources\Popups\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Popups\PopupResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePopup extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = PopupResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers($data, ['image']);
    }
}
