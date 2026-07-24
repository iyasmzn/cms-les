<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function article(): Post
    {
        return Post::factory()->create(['allow_comments' => true]);
    }

    public function test_legit_submission_passes_when_captcha_disabled(): void
    {
        $response = $this->post(route('comments.store', $this->article()), [
            'name' => 'Budi',
            'body' => 'Kapan pendaftaran dibuka?',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments', ['guest_name' => 'Budi']);
    }

    public function test_filled_honeypot_is_rejected(): void
    {
        $response = $this->post(route('comments.store', $this->article()), [
            'name' => 'SpamBot',
            'body' => 'Buy cheap stuff http://spam.example',
            'website' => 'http://spam.example',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertDatabaseMissing('comments', ['guest_name' => 'SpamBot']);
    }

    public function test_turnstile_requires_a_token_when_enabled(): void
    {
        $this->enableTurnstile();

        $response = $this->post(route('comments.store', $this->article()), [
            'name' => 'Budi',
            'body' => 'Halo',
        ]);

        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_turnstile_blocks_submission_with_invalid_token(): void
    {
        $this->enableTurnstile();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $response = $this->post(route('comments.store', $this->article()), [
            'name' => 'Budi',
            'body' => 'Halo',
            'cf-turnstile-response' => 'invalid-token',
        ]);

        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_turnstile_allows_submission_with_valid_token(): void
    {
        $this->enableTurnstile();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $response = $this->post(route('comments.store', $this->article()), [
            'name' => 'Budi',
            'body' => 'Halo',
            'cf-turnstile-response' => 'valid-token',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments', ['guest_name' => 'Budi']);
    }

    private function enableTurnstile(): void
    {
        Setting::setMany([
            'turnstile_enabled' => true,
            'turnstile_site_key' => '1x00000000000000000000AA',
            'turnstile_secret_key' => '1x0000000000000000000000000000000AA',
        ]);
    }
}
