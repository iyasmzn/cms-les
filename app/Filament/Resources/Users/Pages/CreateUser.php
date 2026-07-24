<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers(
            $data,
            ['avatar'],
            self::imageBaseName($data['name'] ?? null, 'Avatar'),
        );
    }
}
