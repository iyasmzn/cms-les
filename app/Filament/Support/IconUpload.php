<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/**
 * Reusable upload field for content icons. Sits alongside an emoji text `icon`
 * field: when an image is uploaded it takes precedence over the emoji on the
 * public site. Accepts raster/vector images plus `.ico` and `.gif`, and keeps
 * files untouched (no resize/crop) so favicons and animated GIFs stay intact.
 */
class IconUpload
{
    /**
     * Accepted icon mime types, including `.ico` (both common variants) and `.gif`.
     *
     * @var list<string>
     */
    public const ACCEPTED = [
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/x-icon',
        'image/vnd.microsoft.icon',
    ];

    public static function make(string $name = 'icon_image', string $directory = 'icons'): FileUpload
    {
        return FileUpload::make($name)
            ->label('Unggah Ikon')
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(self::ACCEPTED)
            ->maxSize(1024)
            ->hint('PNG, JPG, GIF, WEBP, SVG, atau ICO. Jika diisi, gambar ini menggantikan emoji.');
    }
}
