<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = EventResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::applyImagePickers($data, ['image']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Kegiatan'),
        );

        return $data;
    }
}
