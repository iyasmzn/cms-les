<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fetches and locally stores preview thumbnails for video embeds whose provider
 * exposes one without authentication. Currently only TikTok, via its public
 * oEmbed endpoint — YouTube already has direct thumbnails, and Instagram's
 * oEmbed requires a Facebook app token, so both are skipped here.
 *
 * The image is downloaded and stored on the public disk because TikTok's CDN
 * URLs are signed and expire; persisting the bytes keeps the thumbnail stable.
 */
class EmbedThumbnailService
{
    private const TIKTOK_OEMBED = 'https://www.tiktok.com/oembed';

    /**
     * Resolve the provider's thumbnail, download it, and store it on the public
     * disk, returning the stored path. Returns null when the provider exposes no
     * fetchable thumbnail or on any failure — callers fall back to the provider
     * badge. Never throws.
     */
    public function fetchAndStore(?string $provider, ?string $embedUrl): ?string
    {
        if ($provider !== EmbedVideo::PROVIDER_TIKTOK || blank($embedUrl)) {
            return null;
        }

        try {
            $thumbnailUrl = $this->tiktokThumbnailUrl($embedUrl);

            if (blank($thumbnailUrl)) {
                return null;
            }

            $response = Http::timeout(8)->get($thumbnailUrl);

            if (! $response->ok() || blank($response->body())) {
                return null;
            }

            $path = 'media/embed-thumbnails/tiktok-'.Str::random(24).'.'.$this->extensionFor($response->header('Content-Type'));

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * The thumbnail URL from TikTok's public oEmbed response, or null.
     */
    private function tiktokThumbnailUrl(string $embedUrl): ?string
    {
        $response = Http::timeout(5)->get(self::TIKTOK_OEMBED, ['url' => $embedUrl]);

        if (! $response->ok()) {
            return null;
        }

        $thumbnail = $response->json('thumbnail_url');

        return is_string($thumbnail) && filled($thumbnail) ? $thumbnail : null;
    }

    /**
     * Map an image content-type to a file extension, defaulting to jpg.
     */
    private function extensionFor(?string $contentType): string
    {
        return match (true) {
            str_contains((string) $contentType, 'webp') => 'webp',
            str_contains((string) $contentType, 'png') => 'png',
            default => 'jpg',
        };
    }
}
