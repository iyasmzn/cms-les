<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_default_section_headings_when_unset(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Program Unggulan')
            ->assertSee('Keunggulan Kami');
    }

    public function test_it_shows_custom_section_headings_when_set(): void
    {
        Setting::setMany([
            'section_programs_eyebrow' => 'Program Kami',
            'section_programs_title' => 'Program Andalan Pesantren',
            'section_programs_subtitle' => 'Deskripsi program yang sudah diubah.',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Program Andalan Pesantren')
            ->assertSee('Program Kami')
            ->assertSee('Deskripsi program yang sudah diubah.')
            ->assertDontSee('Keunggulan Kami');
    }

    public function test_blank_title_falls_back_to_default(): void
    {
        Setting::set('section_programs_title', '');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Program Unggulan');
    }

    public function test_home_meta_title_overrides_default(): void
    {
        Setting::set('home_meta_title', 'Pesantren Modern Terbaik di Kota');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Pesantren Modern Terbaik di Kota</title>', false);
    }

    public function test_meta_description_falls_back_to_site_description(): void
    {
        Setting::set('site_description', 'Deskripsi umum sekolah dari pengaturan umum.');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description" content="Deskripsi umum sekolah dari pengaturan umum.">', false);
    }

    public function test_home_meta_description_overrides_site_description(): void
    {
        Setting::setMany([
            'site_description' => 'Deskripsi umum sekolah.',
            'home_meta_description' => 'Deskripsi meta khusus beranda.',
        ]);

        // The meta tag uses the dedicated override, not the global site description.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description" content="Deskripsi meta khusus beranda.">', false);
    }
}
