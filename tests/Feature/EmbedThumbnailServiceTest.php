<?php

namespace Tests\Feature;

use App\Services\EmbedThumbnailService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmbedThumbnailServiceTest extends TestCase
{
    public function test_fetches_and_stores_tiktok_thumbnail(): void
    {
        Storage::fake('public');
        Http::fake([
            'www.tiktok.com/oembed*' => Http::response(['thumbnail_url' => 'https://cdn.tiktokcdn.test/thumb.jpg'], 200),
            'cdn.tiktokcdn.test/*' => Http::response('FAKE_IMAGE_BYTES', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $path = app(EmbedThumbnailService::class)
            ->fetchAndStore('tiktok', 'https://www.tiktok.com/@scout/video/7012345678901234567');

        $this->assertNotNull($path);
        $this->assertStringStartsWith('media/embed-thumbnails/tiktok-', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_returns_null_for_providers_without_public_thumbnail(): void
    {
        Http::fake();

        $service = app(EmbedThumbnailService::class);

        $this->assertNull($service->fetchAndStore('youtube', 'https://youtu.be/dQw4w9WgXcQ'));
        $this->assertNull($service->fetchAndStore('instagram', 'https://www.instagram.com/reel/CabcdefghIj/'));
        $this->assertNull($service->fetchAndStore(null, 'https://example.com'));

        // No network request is attempted for unsupported providers.
        Http::assertNothingSent();
    }

    public function test_returns_null_when_oembed_request_fails(): void
    {
        Storage::fake('public');
        Http::fake(['www.tiktok.com/oembed*' => Http::response('', 500)]);

        $path = app(EmbedThumbnailService::class)
            ->fetchAndStore('tiktok', 'https://www.tiktok.com/@scout/video/7012345678901234567');

        $this->assertNull($path);
    }

    public function test_returns_null_when_oembed_has_no_thumbnail(): void
    {
        Storage::fake('public');
        Http::fake(['www.tiktok.com/oembed*' => Http::response(['title' => 'A video'], 200)]);

        $path = app(EmbedThumbnailService::class)
            ->fetchAndStore('tiktok', 'https://www.tiktok.com/@scout/video/7012345678901234567');

        $this->assertNull($path);
    }
}
