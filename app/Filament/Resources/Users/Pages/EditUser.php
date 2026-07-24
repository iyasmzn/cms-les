<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return self::applyImagePickers(
            $data,
            ['avatar'],
            self::imageBaseName($data['name'] ?? null, 'Avatar'),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
