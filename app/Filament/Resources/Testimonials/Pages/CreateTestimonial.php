<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\SyncsPhotoToMediaLibrary;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    use SyncsPhotoToMediaLibrary;

    protected static string $resource = TestimonialResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->syncPhotoToMediaLibrary($data);
    }
}
