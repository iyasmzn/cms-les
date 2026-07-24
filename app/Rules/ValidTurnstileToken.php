<?php

namespace App\Rules;

use App\Support\Turnstile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstileToken implements ValidationRule
{
    public function __construct(private readonly ?string $ip = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Turnstile::verify(is_string($value) ? $value : null, $this->ip)) {
            $fail('Verifikasi captcha gagal. Silakan coba lagi.');
        }
    }
}
