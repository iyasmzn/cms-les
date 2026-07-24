<?php

use App\Models\AcademicYear;
use App\Models\RegistrationWave;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

if (! function_exists('icon_url')) {
    /**
     * Resolve a stored icon-image value to a usable URL. Accepts absolute URLs
     * and root-relative paths as-is; otherwise treats it as a path on the public
     * disk. Returns null when empty so callers can fall back to an emoji icon.
     */
    function icon_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : asset('storage/'.$path);
    }
}

if (! function_exists('contact_items')) {
    /**
     * Build the list of public contact channels from the site settings. This is
     * the single source of truth for contact data shown on the landing page,
     * the contact page, and the footer. Only channels with a filled value are
     * returned. Each item mirrors the legacy ContactItem shape so the existing
     * Blade views can consume it unchanged.
     *
     * @return Collection<int, object{icon: string, icon_image: null, label: string, value: string, link: ?string}>
     */
    function contact_items(): Collection
    {
        $address = setting('contact_address');
        $phone = setting('contact_phone');
        $email = setting('contact_email');
        $whatsapp = setting('social_whatsapp');
        $telegram = setting('social_telegram');
        $hours = setting('contact_hours');

        $phoneDigits = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;
        $waDigits = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : null;

        $channels = [
            [
                'icon' => '📍',
                'label' => 'Alamat',
                'value' => $address,
                'link' => $address
                    ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($address)
                    : null,
            ],
            [
                'icon' => '📞',
                'label' => 'Telepon',
                'value' => $phone,
                'link' => $phoneDigits ? 'tel:'.$phoneDigits : null,
            ],
            [
                'icon' => '✉️',
                'label' => 'Email',
                'value' => $email,
                'link' => $email ? 'mailto:'.$email : null,
            ],
            [
                'icon' => '💬',
                'label' => 'WhatsApp',
                'value' => $whatsapp,
                'link' => $waDigits ? 'https://wa.me/'.$waDigits : null,
            ],
            [
                'icon' => '✈️',
                'label' => 'Telegram',
                'value' => $telegram,
                'link' => $telegram,
            ],
            [
                'icon' => '🕐',
                'label' => 'Jam Operasional',
                'value' => $hours,
                'link' => null,
            ],
        ];

        return collect($channels)
            ->filter(fn (array $channel): bool => filled($channel['value']))
            ->map(fn (array $channel): object => (object) [
                'icon' => $channel['icon'],
                'icon_image' => null,
                'label' => $channel['label'],
                'value' => $channel['value'],
                'link' => $channel['link'],
            ])
            ->values();
    }
}

if (! function_exists('setting')) {
    /**
     * Get a setting value from the database, with an optional default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_bool')) {
    /**
     * Get a setting value interpreted as a boolean. Handles the "1"/"0" strings
     * that Toggle fields persist as well as real booleans and the default.
     */
    function setting_bool(string $key, bool $default = false): bool
    {
        $value = Setting::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('feature_enabled')) {
    /**
     * Whether a public-facing feature is enabled, honouring dependencies.
     *
     * Dependencies:
     *  - "pertanyaan" requires "login_register" (submitting a question needs an
     *    account), so it is locked off whenever login/register is disabled.
     *  - "toko_checkout" requires both the shop and login/register: the shop may
     *    stay enabled for browsing while checkout is unavailable without login.
     *
     * All features default to enabled so existing installs keep working.
     */
    function feature_enabled(string $feature): bool
    {
        $loginRegister = setting_bool('feature_login_register', true);

        return match ($feature) {
            'login_register' => $loginRegister,
            'donasi' => setting_bool('feature_donasi', true),
            'toko' => setting_bool('feature_toko', true),
            'toko_checkout' => setting_bool('feature_toko', true) && $loginRegister,
            'pertanyaan' => $loginRegister && setting_bool('feature_pertanyaan', true),
            default => setting_bool('feature_'.$feature, true),
        };
    }
}

if (! function_exists('google_font_url')) {
    /**
     * Extract a clean Google/Bunny Fonts stylesheet URL from a pasted <link>
     * embed, @import rule, or bare URL. Only whitelisted font hosts are
     * returned, so arbitrary stylesheet URLs cannot be injected. Returns null
     * when nothing valid is found.
     */
    function google_font_url(?string $input): ?string
    {
        if (blank($input)) {
            return null;
        }

        if (preg_match('#https?://fonts\.(?:googleapis\.com|bunny\.net)/[^\s"\'<>()]+#i', $input, $matches)) {
            return html_entity_decode($matches[0]);
        }

        return null;
    }
}

if (! function_exists('google_font_family')) {
    /**
     * Extract the first font-family name from a Google Fonts URL/embed,
     * e.g. "family=Roboto+Slab:wght@400;700" becomes "Roboto Slab".
     */
    function google_font_family(?string $input): ?string
    {
        if (blank($input)) {
            return null;
        }

        if (preg_match('/family=([^:&"\'<>\s]+)/i', $input, $matches)) {
            return clean_font_family_name(str_replace('+', ' ', urldecode($matches[1])));
        }

        return null;
    }
}

if (! function_exists('clean_font_family_name')) {
    /**
     * Sanitise a font-family name so it is safe to interpolate into a CSS
     * declaration. Keeps only letters, numbers, spaces, and hyphens.
     */
    function clean_font_family_name(?string $name): string
    {
        return trim(preg_replace('/[^A-Za-z0-9 \-]/', '', (string) $name));
    }
}

if (! function_exists('resolved_font')) {
    /**
     * Resolve the currently selected website font into a CSS family stack and
     * the stylesheet URL to load (null when bundled locally or unset). Handles
     * both the predefined config/fonts.php entries and the custom Google Font.
     *
     * @return array{family: string, href: ?string}
     */
    function resolved_font(): array
    {
        $fonts = config('fonts');
        $selected = setting('theme_font', 'instrument-sans');

        if ($selected === 'custom') {
            $name = clean_font_family_name(setting('theme_font_custom_family', ''));

            return [
                'family' => $name !== ''
                    ? '"'.$name.'", ui-sans-serif, system-ui, sans-serif'
                    : 'ui-sans-serif, system-ui, sans-serif',
                'href' => google_font_url(setting('theme_font_custom_url', '')),
            ];
        }

        $font = $fonts[$selected] ?? $fonts['instrument-sans'];

        return [
            'family' => $font['family'],
            'href' => (empty($font['bundled']) && ! empty($font['google']))
                ? 'https://fonts.googleapis.com/css2?family='.$font['google'].'&display=swap'
                : null,
        ];
    }
}

if (! function_exists('spmb_year_label')) {
    /**
     * The active academic year label (e.g. "2026/2027"), falling back to the
     * legacy setting or the current calendar year pair.
     */
    function spmb_year_label(): string
    {
        return AcademicYear::active()?->label
            ?? setting('spmb_year', date('Y').'/'.(date('Y') + 1));
    }
}

if (! function_exists('spmb_current_wave')) {
    /**
     * The registration wave that is currently open for the active academic year.
     */
    function spmb_current_wave(): ?RegistrationWave
    {
        return RegistrationWave::currentOpen();
    }
}

if (! function_exists('spmb_in_admission_period')) {
    /**
     * Whether today falls within an active wave of the active academic year.
     */
    function spmb_in_admission_period(): bool
    {
        return spmb_current_wave() !== null;
    }
}
