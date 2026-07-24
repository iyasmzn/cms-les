<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Stories\StoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStory extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = StoryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::applyImagePickers($data, ['image', 'author_photo']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Cerita Santri'),
        );

        return $data;
    }
}
