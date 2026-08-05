<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageLightboxComponentTest extends TestCase
{
    public function test_it_renders_a_thumbnail_card_and_a_lightbox(): void
    {
        $this->blade('<x-image-lightbox url="/storage/proofs/receipt.jpg" caption="Bukti transfer" />')
            ->assertSee('/storage/proofs/receipt.jpg', false)
            ->assertSee('Bukti transfer')
            ->assertSee('x-teleport="body"', false)
            ->assertSee('Scroll or pinch to zoom', false);
    }

    public function test_it_renders_a_placeholder_without_a_url(): void
    {
        $this->blade('<x-image-lightbox :url="null" />')
            ->assertDontSee('x-teleport', false)
            ->assertSee('—');
    }

    public function test_the_card_chrome_and_caption_can_be_dropped(): void
    {
        $this->blade('<x-image-lightbox url="/storage/a.jpg" caption="Hidden caption" alt="Struk" :card="false" />')
            ->assertSee('/storage/a.jpg', false)
            ->assertDontSee('Hidden caption')
            ->assertDontSee('border: 1px solid', false);
    }

    public function test_alt_text_falls_back_to_the_caption(): void
    {
        $this->blade('<x-image-lightbox url="/storage/a.jpg" caption="Kwitansi" />')
            ->assertSee('alt="Kwitansi"', false);

        $this->blade('<x-image-lightbox url="/storage/a.jpg" caption="Kwitansi" alt="Foto struk" />')
            ->assertSee('alt="Foto struk"', false);
    }

    public function test_the_size_caps_are_configurable(): void
    {
        $this->blade('<x-image-lightbox url="/storage/a.jpg" max-width="40rem" max-height="12rem" />')
            ->assertSee('max-width: 40rem', false)
            ->assertSee('max-height:12rem', false);
    }
}
