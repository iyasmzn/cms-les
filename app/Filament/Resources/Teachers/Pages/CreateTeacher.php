<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = TeacherResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers($data, ['photo']);
    }
}
