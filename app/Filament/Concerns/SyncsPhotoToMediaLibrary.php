<?php

namespace App\Filament\Concerns;

use App\Services\MediaLibraryService;

/**
 * Registers a resource's uploaded image in the media library so photos added
 * through other features (teachers, testimonials, …) also appear in the
 * Media Library. The file is recorded but left unpublished
 * (`show_in_gallery` stays false) — an admin still chooses what shows publicly.
 *
 * Call from the page's `mutateFormDataBeforeCreate()` / `mutateFormDataBeforeSave()`.
 */
trait SyncsPhotoToMediaLibrary
{
    /**
     * Register the uploaded image at `$field` in the media library, naming it
     * after the record's `$nameField` value. Existing records keep their name.
     * Returns `$data` unchanged so it can be returned directly from the mutate
     * hooks.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function syncPhotoToMediaLibrary(array $data, string $field = 'photo', string $nameField = 'name'): array
    {
        $path = $data[$field] ?? null;
        $path = is_array($path) ? collect($path)->first() : $path;

        if (filled($path)) {
            $name = filled($data[$nameField] ?? null) ? $data[$nameField] : null;

            app(MediaLibraryService::class)->store($path, $name, $name, createOnly: true);
        }

        return $data;
    }
}
