<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = ProductResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = self::applyImagePickers($data, ['cover_image']);

        $data = self::applyGalleryLibrary(
            $data,
            baseName: self::imageBaseName($data['title'] ?? null, 'Produk'),
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
