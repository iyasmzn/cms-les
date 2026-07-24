<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Slides\SlideResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSlide extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = SlideResource::class;

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
