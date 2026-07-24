<?php

namespace App\Filament\Resources\Greetings\Pages;

use App\Filament\Concerns\SyncsPhotoToMediaLibrary;
use App\Filament\Resources\Greetings\GreetingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGreeting extends EditRecord
{
    use SyncsPhotoToMediaLibrary;

    protected static string $resource = GreetingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->syncPhotoToMediaLibrary($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
