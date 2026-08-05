{{--
    Infolist entry wrapper around <x-image-lightbox>. Use it from any infolist:

        ViewEntry::make('proof_path')
            ->view('filament.infolists.image-preview')
            ->viewData(fn (Model $record): array => [
                'url' => $record->proofUrl(),
                'caption' => 'Click to zoom',
            ]),

    Accepted keys: url, caption, alt, maxWidth, maxHeight.
--}}
@php
    /**
     * Filament extracts every public method of the entry into the view scope as
     * a closure (`url()`, `alt()`, …), which collides with these names. Anything
     * that is not a string therefore came from Filament, not from `viewData()`.
     */
    $asText = fn (mixed $value, ?string $default = null): ?string => is_string($value) ? $value : $default;

    $url = $asText($url ?? null);
    $caption = $asText($caption ?? null, 'Click to zoom');
    $alt = $asText($alt ?? null);
    $maxWidth = $asText($maxWidth ?? null, '28rem');
    $maxHeight = $asText($maxHeight ?? null, '20rem');
@endphp

<x-image-lightbox
    :url="$url"
    :caption="$caption"
    :alt="$alt"
    :max-width="$maxWidth"
    :max-height="$maxHeight"
/>
