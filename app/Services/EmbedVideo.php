<?php

namespace App\Services;

use Illuminate\Support\HtmlString;

/**
 * Stateless helpers for parsing and rendering YouTube, TikTok and Instagram
 * video embeds from a public URL — no external API calls or tokens required.
 */
class EmbedVideo
{
    public const PROVIDER_YOUTUBE = 'youtube';

    public const PROVIDER_TIKTOK = 'tiktok';

    public const PROVIDER_INSTAGRAM = 'instagram';

    /**
     * Supported providers keyed by value, with a human-readable label.
     *
     * @return array<string, string>
     */
    public static function providers(): array
    {
        return [
            self::PROVIDER_YOUTUBE => 'YouTube',
            self::PROVIDER_TIKTOK => 'TikTok',
            self::PROVIDER_INSTAGRAM => 'Instagram',
        ];
    }

    /**
     * Human-readable label for a provider value.
     */
    public static function label(?string $provider): string
    {
        return self::providers()[$provider] ?? 'Video';
    }

    /**
     * Detect the provider for a given URL, or null if unsupported.
     */
    public static function detectProvider(string $url): ?string
    {
        $host = strtolower((string) parse_url(trim($url), PHP_URL_HOST));

        return match (true) {
            str_contains($host, 'youtube.com'), str_contains($host, 'youtu.be') => self::PROVIDER_YOUTUBE,
            str_contains($host, 'tiktok.com') => self::PROVIDER_TIKTOK,
            str_contains($host, 'instagram.com') => self::PROVIDER_INSTAGRAM,
            default => null,
        };
    }

    /**
     * Extract the provider-specific media id (or Instagram shortcode) from the URL.
     */
    public static function extractId(string $provider, string $url): ?string
    {
        $url = trim($url);

        return match ($provider) {
            self::PROVIDER_YOUTUBE => self::matchFirst('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $url),
            self::PROVIDER_TIKTOK => self::matchFirst('~tiktok\.com/(?:@[\w.-]+/video|v|embed/v2|embed)/(\d+)~', $url),
            self::PROVIDER_INSTAGRAM => self::matchFirst('~instagram\.com/(?:p|reel|reels|tv)/([A-Za-z0-9_-]+)~', $url),
            default => null,
        };
    }

    /**
     * Whether the given URL is a supported, parseable embed.
     */
    public static function isValid(string $url): bool
    {
        $provider = self::detectProvider($url);

        return $provider !== null && self::extractId($provider, $url) !== null;
    }

    /**
     * The iframe `src` for the embedded player, or null if not parseable.
     */
    public static function embedSrc(string $provider, string $url): ?string
    {
        $id = self::extractId($provider, $url);

        if ($id === null) {
            return null;
        }

        return match ($provider) {
            self::PROVIDER_YOUTUBE => "https://www.youtube.com/embed/{$id}",
            self::PROVIDER_TIKTOK => "https://www.tiktok.com/embed/v2/{$id}",
            self::PROVIDER_INSTAGRAM => self::instagramEmbedSrc($url),
            default => null,
        };
    }

    /**
     * A thumbnail/preview image URL. YouTube returns a real thumbnail; the
     * others return a branded badge as an inline SVG data URI (no network call).
     */
    public static function thumbnail(string $provider, string $url): ?string
    {
        if ($provider === self::PROVIDER_YOUTUBE) {
            $id = self::extractId($provider, $url);

            return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : self::badge($provider);
        }

        return self::badge($provider);
    }

    /**
     * Width-to-height ratio of the embedded player, used to size the responsive
     * iframe so the provider's content fills it without letterboxing — TikTok is
     * a 9:16 vertical video, Instagram a portrait card, YouTube 16:9.
     */
    public static function aspectRatio(?string $provider): float
    {
        return match ($provider) {
            self::PROVIDER_TIKTOK => 9 / 16,
            self::PROVIDER_INSTAGRAM => 4 / 5,
            default => 16 / 9,
        };
    }

    /**
     * A ready-to-use responsive iframe for rendering on the frontend.
     */
    public static function iframeHtml(string $provider, string $url, ?string $title = null): ?HtmlString
    {
        $src = self::embedSrc($provider, $url);

        if ($src === null) {
            return null;
        }

        $title = e($title ?? self::label($provider));
        $paddingTop = round(100 / self::aspectRatio($provider), 2).'%';

        return new HtmlString(
            '<div style="position:relative;width:100%;padding-top:'.$paddingTop.';overflow:hidden">'
            .'<iframe src="'.e($src).'" title="'.$title.'" loading="lazy" frameborder="0" '
            .'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" '
            .'allowfullscreen style="position:absolute;inset:0;width:100%;height:100%"></iframe>'
            .'</div>'
        );
    }

    /**
     * Instagram has no fixed embed id endpoint, so append `/embed` to the
     * canonical post/reel URL (stripping any query string).
     */
    private static function instagramEmbedSrc(string $url): string
    {
        $base = strtok(trim($url), '?');

        return rtrim($base, '/').'/embed';
    }

    /**
     * Inline SVG badge used as a thumbnail placeholder for providers without a
     * fetchable image (TikTok, Instagram).
     */
    private static function badge(string $provider): string
    {
        [$bg, $fg, $label] = match ($provider) {
            self::PROVIDER_TIKTOK => ['#010101', '#25f4ee', 'TikTok'],
            self::PROVIDER_INSTAGRAM => ['#c13584', '#ffffff', 'Instagram'],
            default => ['#fffbeb', '#d97706', 'Video'],
        };

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160">'
            .'<rect width="160" height="160" fill="'.$bg.'"/>'
            .'<circle cx="80" cy="64" r="30" fill="none" stroke="'.$fg.'" stroke-width="4"/>'
            .'<path d="M72 50 l24 14 -24 14 z" fill="'.$fg.'"/>'
            .'<text x="80" y="124" font-family="sans-serif" font-size="20" font-weight="700" '
            .'text-anchor="middle" fill="'.$fg.'">'.$label.'</text>'
            .'</svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }

    private static function matchFirst(string $pattern, string $subject): ?string
    {
        return preg_match($pattern, $subject, $matches) ? $matches[1] : null;
    }
}
