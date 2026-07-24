<?php

namespace Database\Factories;

use App\Models\Media;
use App\Services\EmbedVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state (an uploaded image file).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'alt' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'album' => null,
            'path' => 'media/'.fake()->uuid().'.jpg',
            'embed_provider' => null,
            'embed_url' => null,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(1_000, 500_000),
            'uploaded_by' => null,
        ];
    }

    /**
     * Flagged to appear in the public gallery.
     */
    public function inGallery(): static
    {
        return $this->state(fn (): array => [
            'show_in_gallery' => true,
        ]);
    }

    /**
     * Grouped under a named gallery album.
     */
    public function inAlbum(string $album): static
    {
        return $this->state(fn (): array => [
            'album' => $album,
        ]);
    }

    /**
     * An external video embed (YouTube/TikTok/Instagram) with no physical file.
     */
    public function embed(string $provider = EmbedVideo::PROVIDER_YOUTUBE): static
    {
        $urls = [
            EmbedVideo::PROVIDER_YOUTUBE => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            EmbedVideo::PROVIDER_TIKTOK => 'https://www.tiktok.com/@scout/video/7012345678901234567',
            EmbedVideo::PROVIDER_INSTAGRAM => 'https://www.instagram.com/reel/CabcdefghIj/',
        ];

        return $this->state(fn (): array => [
            'path' => null,
            'mime_type' => null,
            'size' => 0,
            'embed_provider' => $provider,
            'embed_url' => $urls[$provider],
        ]);
    }

    /**
     * Attach a manually uploaded preview image to an embed.
     */
    public function withEmbedThumbnail(string $path = 'media/embed-thumbnails/thumb.jpg'): static
    {
        return $this->state(fn (): array => [
            'embed_thumbnail_path' => $path,
        ]);
    }
}
