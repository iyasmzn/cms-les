<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found_page_uses_the_custom_error_view(): void
    {
        $this->get('/this-route-does-not-exist')
            ->assertNotFound()
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_error_title_can_be_overridden_from_settings(): void
    {
        Setting::set('error_404_title', 'Ups, Tersesat');

        $this->get('/still-not-a-real-route')
            ->assertNotFound()
            ->assertSee('Ups, Tersesat');
    }

    public function test_home_button_can_be_hidden_from_settings(): void
    {
        Setting::set('error_show_home_button', false);

        $this->get('/nope-not-here')
            ->assertNotFound()
            ->assertDontSee('Kembali ke Beranda');
    }
}
