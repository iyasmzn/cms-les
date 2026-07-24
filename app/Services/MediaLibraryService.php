<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaLibraryService
{
    /**
     * Ensure the given file path exists as a Media record.
     * Safe to call multiple times — uses firstOrCreate to avoid duplicates.
     */
    public function sync(string $path, string $disk = 'public'): void
    {
        if (blank($path)) {
            return;
        }

        try {
            $storage = Storage::disk($disk);

            if (! $storage->exists($path)) {
                return;
            }

            Media::firstOrCreate(
                ['path' => $path, 'disk' => $disk],
                [
                    'name' => $this->uniqueName(pathinfo(basename($path), PATHINFO_FILENAME)),
                    'mime_type' => $storage->mimeType($path),
                    'size' => $storage->size($path),
                    'uploaded_by' => Auth::id(),
                ]
            );
        } catch (\Throwable) {
            // Never block a save because of media sync failure.
        }
    }

    /**
     * Create (or update) a Media record for an uploaded file with an explicit
     * name and alt text. When the file already has a Media record, only the
     * provided metadata is refreshed; otherwise a record is created with a
     * unique name. Pass $createOnly to leave an existing record's metadata
     * untouched (used when the name is auto-derived, not user-entered).
     * Returns the resulting Media, or null on failure.
     */
    public function store(string $path, ?string $name = null, ?string $alt = null, string $disk = 'public', bool $createOnly = false): ?Media
    {
        if (blank($path)) {
            return null;
        }

        try {
            $storage = Storage::disk($disk);

            if (! $storage->exists($path)) {
                return null;
            }

            $existing = Media::query()->where('path', $path)->where('disk', $disk)->first();

            if ($existing) {
                if ($createOnly) {
                    return $existing;
                }

                $existing->fill(array_filter([
                    'name' => filled($name) ? $name : null,
                    'alt' => filled($alt) ? $alt : null,
                ], fn ($value): bool => $value !== null));
                $existing->save();

                return $existing;
            }

            $base = filled($name) ? $name : pathinfo(basename($path), PATHINFO_FILENAME);

            return Media::create([
                'path' => $path,
                'disk' => $disk,
                'name' => $this->uniqueName($base),
                'alt' => filled($alt) ? $alt : null,
                'mime_type' => $storage->mimeType($path),
                'size' => $storage->size($path),
                'uploaded_by' => Auth::id(),
            ]);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Return a Media name that is unique within the library, appending an
     * incrementing suffix ("-1", "-2", …) when the base name is already taken.
     */
    public function uniqueName(string $base): string
    {
        $base = trim($base) !== '' ? trim($base) : 'media';

        $name = $base;
        $suffix = 1;

        while (Media::query()->where('name', $name)->exists()) {
            $name = $base.'-'.$suffix;
            $suffix++;
        }

        return $name;
    }
}
