<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Throwable;

class Turnstile
{
    /** Name of the token field Cloudflare injects into the form. */
    public const RESPONSE_FIELD = 'cf-turnstile-response';

    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Turnstile is active only when enabled in settings and both keys are present.
     */
    public static function enabled(): bool
    {
        return (bool) Setting::get('turnstile_enabled', false)
            && filled(self::siteKey())
            && filled(Setting::get('turnstile_secret_key'));
    }

    public static function siteKey(): ?string
    {
        return Setting::get('turnstile_site_key');
    }

    /**
     * Verify a client token against Cloudflare's siteverify endpoint.
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        $secret = Setting::get('turnstile_secret_key');

        if (blank($token) || blank($secret)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(self::VERIFY_URL, [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (Throwable) {
            return false;
        }

        return $response->successful() && $response->json('success') === true;
    }
}
