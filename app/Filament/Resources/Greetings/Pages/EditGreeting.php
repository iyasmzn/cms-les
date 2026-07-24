<?php

namespace App\Filament\Resources\Greetings\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Greetings\GreetingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGreeting extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = GreetingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return self::applyImagePickers($data, ['photo']);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
