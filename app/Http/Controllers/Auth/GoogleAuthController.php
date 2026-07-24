<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $this->applySettings();

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $this->applySettings();

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login dengan Google gagal. Silakan coba lagi.']);
        }

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
            ]
        );

        // Email dari Google sudah terverifikasi. Diset eksplisit karena
        // email_verified_at bukan atribut yang mass-assignable pada User.
        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('home'))
            ->with('success', 'Selamat datang, '.$user->name.'!');
    }

    private function applySettings(): void
    {
        abort_unless((bool) Setting::get('oauth_google_enabled', false), 404);

        $clientId = Setting::get('oauth_google_client_id') ?: config('services.google.client_id');
        $clientSecret = Setting::get('oauth_google_client_secret') ?: config('services.google.client_secret');

        config([
            'services.google.client_id' => $clientId,
            'services.google.client_secret' => $clientSecret,
            // Redirect URI selalu diturunkan dari domain aktif agar tidak perlu
            // diatur lewat env (GOOGLE_REDIRECT_URI) saat pindah domain/production.
            'services.google.redirect' => route('auth.google.callback'),
        ]);
    }
}
