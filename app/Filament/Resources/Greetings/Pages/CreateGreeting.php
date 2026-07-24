<?php

namespace App\Filament\Resources\Greetings\Pages;

use App\Filament\Concerns\SyncsPhotoToMediaLibrary;
use App\Filament\Resources\Greetings\GreetingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGreeting extends CreateRecord
{
    use SyncsPhotoToMediaLibrary;

    protected static string $resource = GreetingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->syncPhotoToMediaLibrary($data);
    }
}
