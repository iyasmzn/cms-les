<?php

namespace App\Filament\Resources\Greetings\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Greetings\GreetingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGreeting extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = GreetingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers($data, ['photo']);
    }
}
