<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Programs\ProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgram extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = ProgramResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::applyImagePickers($data, ['image']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Program'),
        );

        return $data;
    }
}
