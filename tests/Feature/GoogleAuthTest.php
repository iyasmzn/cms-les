<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function enableGoogleLogin(): void
    {
        Setting::setMany([
            'oauth_google_enabled' => '1',
            'oauth_google_client_id' => 'test-client-id.apps.googleusercontent.com',
            'oauth_google_client_secret' => 'test-secret',
        ]);
    }

    public function test_google_routes_return_404_when_login_is_disabled(): void
    {
        Setting::set('oauth_google_enabled', '0');

        $this->get(route('auth.google'))->assertNotFound();
        $this->get(route('auth.google.callback'))->assertNotFound();
    }

    public function test_redirect_uri_is_derived_from_current_domain_not_config(): void
    {
        $this->enableGoogleLogin();

        // Simulasikan nilai lawas dari env/config yang HARUS diabaikan.
        config(['services.google.redirect' => 'http://stale-env-domain.test/auth/google/callback']);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        $this->assertStringContainsString('accounts.google.com', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        // Redirect URI mengikuti domain aktif (named route), bukan nilai env/config.
        $this->assertSame(route('auth.google.callback'), $query['redirect_uri']);
        $this->assertStringNotContainsString('stale-env-domain.test', $location);
    }

    public function test_callback_creates_user_and_logs_in(): void
    {
        $this->enableGoogleLogin();

        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => 'google-12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/budi.jpg',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas(User::class, [
            'google_id' => 'google-12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/budi.jpg',
        ]);

        $this->assertNotNull(User::where('google_id', 'google-12345')->first()->email_verified_at);
    }

    public function test_callback_links_existing_google_account_without_duplicating(): void
    {
        $this->enableGoogleLogin();

        $existing = User::factory()->create(['google_id' => 'google-999', 'name' => 'Nama Lama']);

        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => 'google-999',
            'name' => 'Nama Baru',
            'email' => $existing->email,
            'avatar' => 'https://lh3.googleusercontent.com/a/baru.jpg',
        ]));

        $this->get(route('auth.google.callback'))->assertRedirect(route('home'));

        $this->assertSame(1, User::where('google_id', 'google-999')->count());
        $this->assertDatabaseHas(User::class, [
            'id' => $existing->id,
            'name' => 'Nama Baru',
        ]);
    }
}
