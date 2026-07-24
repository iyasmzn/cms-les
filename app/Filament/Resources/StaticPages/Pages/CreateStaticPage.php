<?php

namespace App\Filament\Resources\StaticPages\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\StaticPages\StaticPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaticPage extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = StaticPageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Halaman'),
        );

        return $data;
    }
}
