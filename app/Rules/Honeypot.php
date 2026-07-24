<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Passes when the honeypot field is empty. A filled value means a bot
 * completed a field that is hidden from real users.
 */
class Honeypot implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (filled($value)) {
            $fail('Terjadi kesalahan validasi. Silakan muat ulang halaman dan coba lagi.');
        }
    }
}
