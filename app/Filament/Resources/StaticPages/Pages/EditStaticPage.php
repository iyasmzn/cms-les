<?php

namespace App\Filament\Resources\StaticPages\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\StaticPages\StaticPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaticPage extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = StaticPageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Halaman'),
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
