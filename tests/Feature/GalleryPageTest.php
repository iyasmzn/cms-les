<?php

namespace Tests\Feature;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_lists_only_flagged_media(): void
    {
        $shown = Media::factory()->inGallery()->create(['name' => 'Foto Wisuda']);
        $hidden = Media::factory()->create(['name' => 'Foto Internal']);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertSee('Foto Wisuda')
            ->assertDontSee('Foto Internal');
    }

    public function test_index_page_excludes_non_visual_media(): void
    {
        $pdf = Media::factory()->inGallery()->create([
            'name' => 'Brosur PDF',
            'mime_type' => 'application/pdf',
        ]);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertDontSee('Brosur PDF');
    }

    public function test_album_filter_limits_to_matching_media(): void
    {
        $wisuda = Media::factory()->inGallery()->inAlbum('Wisuda 2025')->create(['name' => 'Prosesi Wisuda']);
        $fasilitas = Media::factory()->inGallery()->inAlbum('Fasilitas')->create(['name' => 'Gedung Asrama']);

        $this->get(route('gallery.index', ['album' => 'Wisuda 2025']))
            ->assertOk()
            ->assertSee('Prosesi Wisuda')
            ->assertDontSee('Gedung Asrama');
    }

    public function test_foto_filter_excludes_video_embeds(): void
    {
        $photo = Media::factory()->inGallery()->create(['name' => 'Foto Upacara']);
        $video = Media::factory()->inGallery()->embed()->create(['name' => 'Video Profil']);

        $this->get(route('gallery.index', ['type' => 'foto']))
            ->assertOk()
            ->assertSee('Foto Upacara')
            ->assertDontSee('Video Profil');
    }

    public function test_video_filter_excludes_photos(): void
    {
        $photo = Media::factory()->inGallery()->create(['name' => 'Foto Upacara']);
        $video = Media::factory()->inGallery()->embed()->create(['name' => 'Video Profil']);

        $this->get(route('gallery.index', ['type' => 'video']))
            ->assertOk()
            ->assertSee('Video Profil')
            ->assertDontSee('Foto Upacara');
    }

    public function test_home_page_gallery_section_shows_flagged_media(): void
    {
        $shown = Media::factory()->inGallery()->create(['name' => 'Kegiatan Sekolah']);
        $hidden = Media::factory()->create(['name' => 'Arsip Privat']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Kegiatan Sekolah')
            ->assertDontSee('Arsip Privat');
    }
}
