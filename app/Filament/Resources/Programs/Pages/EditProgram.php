<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = ProgramResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Program'),
        );

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
