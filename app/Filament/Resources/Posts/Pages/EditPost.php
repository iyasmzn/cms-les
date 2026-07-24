<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = PostResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = self::applyImagePickers($data, ['image']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Blog'),
        );

        // Authors without publish permission cannot change the publish state;
        // preserve whatever the record already has.
        if (! auth()->user()?->can('Publish:Post')) {
            unset($data['is_published'], $data['published_at']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
