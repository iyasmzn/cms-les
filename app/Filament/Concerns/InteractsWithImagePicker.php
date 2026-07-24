<?php

namespace App\Filament\Concerns;

use App\Models\Media;
use App\Services\MediaLibraryService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Reusable image field for settings pages and resources: choose an existing
 * item from the media library (with thumbnail options & preview) or upload a
 * new one. On upload, the name & alt fields auto-fill from the original file
 * name and stay editable; the name is made unique within the library.
 *
 * Backing form keys per field `$key`:
 *  - `$key`            — the stored file path
 *  - `{$key}_source`   — 'upload' | 'library'
 *  - `{$key}_library`  — selected Media id (library mode)
 *  - `{$key}_name`     — Media name (upload mode, only when `$withMeta`)
 *  - `{$key}_alt`      — Media alt text (upload mode, only when `$withMeta`)
 *
 * Methods are static so they can be used from both Livewire pages and static
 * resource form/schema classes.
 */
trait InteractsWithImagePicker
{
    /**
     * @param  list<string>  $accepted
     */
    protected static function imagePicker(
        string $key,
        string $label,
        string $hint,
        array $accepted,
        int $width,
        ?int $height = null,
        string $directory = 'settings',
        ?string $aspectRatio = null,
        bool $withMeta = true,
    ): Fieldset {
        $isUpload = fn (Get $get): bool => ($get("{$key}_source") ?? 'upload') === 'upload';
        $isLibrary = fn (Get $get): bool => $get("{$key}_source") === 'library';

        $upload = FileUpload::make($key)
            ->label('Unggah Gambar')
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            // GIF selalu diterima; skrip FilePond global (AdminPanelProvider) melewati
            // transform untuk image/gif sehingga animasinya tetap utuh (tidak dikompres),
            // sedangkan format lain tetap di-resize/kompres di browser.
            ->acceptedFileTypes(array_values(array_unique([...$accepted, 'image/gif'])))
            ->automaticallyResizeImagesToWidth((string) $width)
            ->when($height !== null, fn (FileUpload $component): FileUpload => $component
                ->automaticallyResizeImagesToHeight((string) $height))
            ->when($aspectRatio !== null, fn (FileUpload $component): FileUpload => $component
                ->automaticallyCropImagesToAspectRatio($aspectRatio)
                ->automaticallyResizeImagesMode('cover'))
            ->when($withMeta, fn (FileUpload $component): FileUpload => $component
                ->live()
                ->afterStateUpdated(function ($state, Set $set) use ($key): void {
                    $file = collect(is_array($state) ? $state : [$state])->first();

                    if (! is_object($file) || ! method_exists($file, 'getClientOriginalName')) {
                        return;
                    }

                    $base = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        ->replace(['-', '_'], ' ')
                        ->squish()
                        ->title()
                        ->toString();

                    $set("{$key}_name", app(MediaLibraryService::class)->uniqueName($base));
                    $set("{$key}_alt", $base);
                }))
            ->visible($isUpload)
            ->hint($hint)
            ->columnSpanFull();

        $schema = [
            ToggleButtons::make("{$key}_source")
                ->label('Sumber Gambar')
                ->options([
                    'upload' => 'Upload Baru',
                    'library' => 'Pilih dari Media',
                ])
                ->icons([
                    'upload' => Heroicon::OutlinedArrowUpTray,
                    'library' => Heroicon::OutlinedPhoto,
                ])
                ->default('upload')
                ->inline()
                ->live()
                ->columnSpanFull(),

            Select::make("{$key}_library")
                ->label('Pilih dari Media')
                ->allowHtml()
                ->options(fn (): array => static::mediaLibraryOptions())
                ->searchable()
                ->preload()
                ->native(false)
                ->live()
                ->placeholder('Cari nama media…')
                ->visible($isLibrary)
                ->required($isLibrary)
                ->columnSpanFull(),

            Placeholder::make("{$key}_preview")
                ->label('Pratinjau')
                ->visible(fn (Get $get): bool => $isLibrary($get) && filled($get("{$key}_library")))
                ->content(function (Get $get) use ($key): ?HtmlString {
                    $media = Media::find($get("{$key}_library"));

                    if (! $media) {
                        return null;
                    }

                    return new HtmlString(
                        '<img src="'.e($media->thumbnail_url).'" alt="'.e($media->alt ?? $media->name).'" '
                        .'style="max-height:160px;max-width:100%;border-radius:0.75rem;border:1px solid rgba(0,0,0,.1);object-fit:contain;" />'
                    );
                })
                ->columnSpanFull(),

            $upload,
        ];

        if ($withMeta) {
            $schema[] = TextInput::make("{$key}_name")
                ->label('Nama Media')
                ->maxLength(200)
                ->helperText('Terisi otomatis dari nama berkas; ditambah angka bila sudah dipakai.')
                ->visible($isUpload);

            $schema[] = TextInput::make("{$key}_alt")
                ->label('Alt Text')
                ->maxLength(500)
                ->helperText('Deskripsi singkat gambar untuk SEO & aksesibilitas.')
                ->visible($isUpload);
        }

        return Fieldset::make($label)->schema($schema);
    }

    /**
     * A multi-select that appends existing media-library images to a gallery
     * field on save. Pair with a regular multiple FileUpload for new uploads.
     */
    protected static function mediaLibrarySelect(string $key, string $label): Select
    {
        return Select::make($key)
            ->label($label)
            ->multiple()
            ->allowHtml()
            ->options(fn (): array => static::mediaLibraryOptions())
            ->searchable()
            ->preload()
            ->native(false)
            ->placeholder('Pilih gambar dari media…')
            ->helperText('Gambar terpilih akan ditambahkan ke galeri saat disimpan.')
            ->columnSpanFull();
    }

    /**
     * Default state for the picker's source toggles, to merge into form fill.
     *
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    protected static function imagePickerDefaults(array $keys): array
    {
        return collect($keys)
            ->mapWithKeys(fn (string $key): array => ["{$key}_source" => 'upload'])
            ->all();
    }

    /**
     * Resolve each top-level image field from its chosen source.
     *
     * @param  array<string, mixed>  $data
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    protected static function applyImagePickers(array $data, array $keys, ?string $baseName = null): array
    {
        foreach ($keys as $key) {
            $data = self::resolveImageField($data, $key, $baseName);
        }

        return $data;
    }

    /**
     * Resolve the picker keys inside a "blocks" repeater (cover image + nested
     * carousel/gallery image lists), stripping helper keys from the stored JSON.
     * Uploaded images (which have no name/alt field) are named after $baseName.
     *
     * @param  array<int, mixed>|null  $blocks
     * @return array<int, mixed>
     */
    protected static function applyBlockImagePickers(?array $blocks, ?string $baseName = null): array
    {
        $blocks ??= [];

        foreach ($blocks as &$block) {
            if (! is_array($block)) {
                continue;
            }

            if (array_key_exists('image', $block) || array_key_exists('image_source', $block)) {
                $block = self::resolveImageField($block, 'image', $baseName);
            }

            if (isset($block['images']) && is_array($block['images'])) {
                foreach ($block['images'] as &$image) {
                    if (is_array($image)) {
                        $image = self::resolveImageField($image, 'image', $baseName);
                    }
                }
                unset($image);
            }
        }
        unset($block);

        return $blocks;
    }

    /**
     * Base media name to use for uploads that have no name field: the content's
     * own title, falling back to the feature name when the title is empty.
     */
    protected static function imageBaseName(?string $title, string $feature): string
    {
        return filled($title) ? trim($title) : $feature;
    }

    /**
     * Merge media-library selections into a multiple-file gallery field. When
     * $baseName is given, every gallery path is also synced to the media library
     * — new uploads are named after $baseName (deduped); existing ones are left
     * untouched.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function applyGalleryLibrary(array $data, string $galleryKey = 'gallery', string $libraryKey = 'gallery_library', ?string $baseName = null): array
    {
        $existing = collect($data[$galleryKey] ?? [])->filter();

        $fromLibrary = Media::query()
            ->whereIn('id', $data[$libraryKey] ?? [])
            ->pluck('path')
            ->filter();

        $paths = $existing->merge($fromLibrary)->unique()->values();

        if ($baseName !== null) {
            $media = app(MediaLibraryService::class);

            foreach ($paths as $path) {
                $media->store($path, $baseName, $baseName, createOnly: true);
            }
        }

        $data[$galleryKey] = $paths->all();

        unset($data[$libraryKey]);

        return $data;
    }

    /**
     * Resolve a single image field (path) within an array of form data: pick the
     * library item's path, or persist an upload to the media library, then strip
     * the picker's helper keys.
     *
     * Full pickers carry their own `{$key}_name`/`{$key}_alt` fields. Lite
     * pickers (no meta fields) name new uploads after $baseName and never
     * rename an existing record.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected static function resolveImageField(array $item, string $key, ?string $baseName = null): array
    {
        $hasMetaFields = array_key_exists("{$key}_name", $item) || array_key_exists("{$key}_alt", $item);

        if (($item["{$key}_source"] ?? 'upload') === 'library') {
            $selected = Media::find($item["{$key}_library"] ?? null);
            $item[$key] = $selected?->path ?? ($item[$key] ?? null);
        } elseif (! blank($item[$key] ?? null)) {
            $name = $hasMetaFields ? ($item["{$key}_name"] ?? null) : $baseName;
            $alt = $hasMetaFields ? ($item["{$key}_alt"] ?? null) : $baseName;

            app(MediaLibraryService::class)->store(
                $item[$key],
                $name,
                $alt,
                createOnly: ! $hasMetaFields,
            );
        }

        unset(
            $item["{$key}_source"],
            $item["{$key}_library"],
            $item["{$key}_name"],
            $item["{$key}_alt"],
        );

        return $item;
    }

    /**
     * Image media options rendered with a thumbnail beside the name.
     *
     * @return array<int, string>
     */
    protected static function mediaLibraryOptions(): array
    {
        return Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Media $media): array => [
                $media->id => '<div style="display:flex;align-items:center;gap:.5rem;">'
                    .'<img src="'.e($media->thumbnail_url).'" alt="" style="width:2rem;height:2rem;border-radius:.375rem;object-fit:cover;flex-shrink:0;" />'
                    .'<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'.e($media->name).'</span>'
                    .'</div>',
            ])
            ->all();
    }
}
