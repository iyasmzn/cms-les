<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Services\MediaLibraryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): MediaLibraryService
    {
        return app(MediaLibraryService::class);
    }

    public function test_unique_name_returns_base_when_free(): void
    {
        $this->assertSame('Logo Sekolah', $this->service()->uniqueName('Logo Sekolah'));
    }

    public function test_unique_name_appends_incrementing_suffix_when_taken(): void
    {
        Media::factory()->create(['name' => 'Logo Sekolah']);
        Media::factory()->create(['name' => 'Logo Sekolah-1']);

        $this->assertSame('Logo Sekolah-2', $this->service()->uniqueName('Logo Sekolah'));
    }

    public function test_store_creates_media_with_provided_name_and_alt(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->create('logo.png', 10, 'image/png')->store('settings', 'public');

        $media = $this->service()->store($path, 'Logo Utama', 'Logo sekolah resmi');

        $this->assertNotNull($media);
        $this->assertDatabaseHas(Media::class, [
            'path' => $path,
            'name' => 'Logo Utama',
            'alt' => 'Logo sekolah resmi',
        ]);
    }

    public function test_store_dedupes_name_against_existing_media(): void
    {
        Storage::fake('public');
        Media::factory()->create(['name' => 'Logo Utama']);
        $path = UploadedFile::fake()->create('logo.png', 10, 'image/png')->store('settings', 'public');

        $media = $this->service()->store($path, 'Logo Utama', null);

        $this->assertSame('Logo Utama-1', $media->name);
    }

    public function test_store_updates_existing_record_without_creating_duplicate(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->create('logo.png', 10, 'image/png')->store('settings', 'public');

        $first = $this->service()->store($path, 'Awal', 'Alt awal');
        $again = $this->service()->store($path, 'Diperbarui', 'Alt baru');

        $this->assertSame($first->id, $again->id);
        $this->assertSame(1, Media::where('path', $path)->count());
        $this->assertDatabaseHas(Media::class, [
            'id' => $first->id,
            'name' => 'Diperbarui',
            'alt' => 'Alt baru',
        ]);
    }

    public function test_store_keeps_existing_meta_when_new_values_are_blank(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->create('logo.png', 10, 'image/png')->store('settings', 'public');

        $first = $this->service()->store($path, 'Tetap', 'Alt tetap');
        $this->service()->store($path, null, null);

        $this->assertDatabaseHas(Media::class, [
            'id' => $first->id,
            'name' => 'Tetap',
            'alt' => 'Alt tetap',
        ]);
    }
}
