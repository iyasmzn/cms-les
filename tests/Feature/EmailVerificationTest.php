<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_verification_email_and_redirects_to_notice(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_submit_question(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'question' => 'Pertanyaan dari user belum verifikasi.',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_verified_user_can_submit_question(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'question' => 'Pertanyaan dari user terverifikasi.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('questions', 1);
    }

    public function test_notice_redirects_already_verified_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertRedirect(route('home'));
    }

    public function test_signed_link_verifies_the_email(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('home'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_invalid_hash_does_not_verify_the_email(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email@example.com')],
        );

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_user_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
