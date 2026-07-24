<?php

namespace App\Http\Concerns;

use App\Rules\Honeypot;
use App\Rules\ValidTurnstileToken;
use App\Support\Turnstile;
use Illuminate\Http\Request;

trait ProtectsAgainstSpam
{
    /**
     * Validation rules guarding a public form against bots: a honeypot field
     * plus an optional Cloudflare Turnstile check when configured in the panel.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function spamProtectionRules(Request $request): array
    {
        return [
            'website' => [new Honeypot],
            Turnstile::RESPONSE_FIELD => Turnstile::enabled()
                ? ['required', new ValidTurnstileToken($request->ip())]
                : ['nullable'],
        ];
    }
}
