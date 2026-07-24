<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = PostResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::applyImagePickers($data, ['image']);

        $data['blocks'] = self::applyBlockImagePickers(
            $data['blocks'] ?? [],
            self::imageBaseName($data['title'] ?? null, 'Blog'),
        );

        $data['user_id'] = auth()->id();

        // Authors without publish permission can only create drafts.
        if (! auth()->user()?->can('Publish:Post')) {
            $data['is_published'] = false;
            $data['published_at'] = null;
        }

        return $data;
    }
}
