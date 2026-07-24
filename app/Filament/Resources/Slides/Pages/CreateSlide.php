<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Slides\SlideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSlide extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = SlideResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers($data, ['image']);
    }
}
