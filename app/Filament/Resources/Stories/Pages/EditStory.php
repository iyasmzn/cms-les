<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Stories\StoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStory extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = StoryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = self::applyImagePickers($data, ['image', 'author_photo']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Cerita Santri'),
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
