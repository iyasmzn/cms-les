<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\SyncsPhotoToMediaLibrary;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    use SyncsPhotoToMediaLibrary;

    protected static string $resource = TestimonialResource::class;

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
