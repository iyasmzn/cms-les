<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class IntegrationSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Integrasi & OAuth';

    protected static ?string $title = 'Pengaturan Integrasi & OAuth';

    protected static ?int $navigationSort = 10;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Google OAuth
            'oauth_google_enabled' => (bool) Setting::get('oauth_google_enabled', false),
            'oauth_google_client_id' => Setting::get('oauth_google_client_id'),
            'oauth_google_client_secret' => Setting::get('oauth_google_client_secret'),

            // Cloudflare Turnstile
            'turnstile_enabled' => (bool) Setting::get('turnstile_enabled', false),
            'turnstile_site_key' => Setting::get('turnstile_site_key'),
            'turnstile_secret_key' => Setting::get('turnstile_secret_key'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            $this->providerSection(
                provider: 'google',
                label: 'Google',
                icon: Heroicon::OutlinedGlobeAlt,
                description: 'Login menggunakan akun Google. Dapatkan Client ID dan Secret dari Google Cloud Console → APIs & Services → Credentials.',
                helpUrl: 'https://console.cloud.google.com/apis/credentials',
                redirectUri: route('auth.google.callback'),
            ),

            // Tambahkan provider baru di sini dengan memanggil $this->providerSection(...)
            // Contoh: $this->providerSection('github', 'GitHub', ...),

            $this->turnstileSection(),

        ]);
    }

    private function turnstileSection(): Section
    {
        return Section::make('Cloudflare Turnstile (Captcha)')
            ->description('Lindungi form publik (Tanya Jawab, Donasi, PPDB) dari bot & spam. Gratis tanpa batas. Dapatkan Site Key dan Secret Key dari dashboard Cloudflare → Turnstile → Add Site.')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->schema([
                Toggle::make('turnstile_enabled')
                    ->label('Aktifkan Turnstile')
                    ->helperText('Captcha hanya tampil di form publik bila diaktifkan dan kedua key terisi.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->live()
                    ->columnSpanFull(),

                Grid::make(1)->schema([
                    TextInput::make('turnstile_site_key')
                        ->label('Site Key')
                        ->placeholder('0x4AAAA...')
                        ->visible(fn (Get $get): bool => (bool) $get('turnstile_enabled'))
                        ->required(fn (Get $get): bool => (bool) $get('turnstile_enabled'))
                        ->maxLength(255),

                    TextInput::make('turnstile_secret_key')
                        ->label('Secret Key')
                        ->password()
                        ->revealable()
                        ->placeholder('Isi untuk mengubah secret yang tersimpan')
                        ->visible(fn (Get $get): bool => (bool) $get('turnstile_enabled'))
                        ->maxLength(255)
                        ->hint('Disimpan aman di server, tidak pernah tampil ke publik. Kosongkan jika tidak ingin mengubah secret yang sudah tersimpan.'),
                ])->columnSpanFull(),
            ]);
    }

    private function providerSection(
        string $provider,
        string $label,
        Heroicon $icon,
        string $description,
        string $helpUrl,
        string $redirectUri,
    ): Section {
        return Section::make($label)
            ->description($description)
            ->icon($icon)
            ->schema([
                Toggle::make("oauth_{$provider}_enabled")
                    ->label("Aktifkan Login dengan {$label}")
                    ->onColor('success')
                    ->offColor('danger')
                    ->live()
                    ->columnSpanFull(),

                Grid::make(1)->schema([
                    TextInput::make("oauth_{$provider}_client_id")
                        ->label('Client ID')
                        ->placeholder('your-client-id.apps.googleusercontent.com')
                        ->visible(fn (Get $get): bool => (bool) $get("oauth_{$provider}_enabled"))
                        ->required(fn (Get $get): bool => (bool) $get("oauth_{$provider}_enabled"))
                        ->maxLength(500),

                    TextInput::make("oauth_{$provider}_client_secret")
                        ->label('Client Secret')
                        ->password()
                        ->revealable()
                        ->placeholder('Isi untuk mengubah secret yang tersimpan')
                        ->visible(fn (Get $get): bool => (bool) $get("oauth_{$provider}_enabled"))
                        ->maxLength(500)
                        ->hint('Kosongkan jika tidak ingin mengubah secret yang sudah tersimpan.'),
                ])->columnSpanFull(),

                $this->redirectUriHelper($redirectUri, $provider),
            ]);
    }

    private function redirectUriHelper(string $uri, string $provider): TextInput
    {
        return TextInput::make("_redirect_uri_{$provider}")
            ->label('Authorized Redirect URI')
            ->default($uri)
            ->disabled()
            ->dehydrated(false)
            ->hint('Salin URI ini ke kolom "Authorized redirect URIs" di konsol penyedia OAuth.')
            ->extraInputAttributes(['style' => 'font-family:monospace;font-size:0.8rem'])
            ->suffixAction(
                Action::make("copy_{$provider}")
                    ->icon(Heroicon::OutlinedClipboard)
                    ->tooltip('Salin URI')
                    ->extraAttributes([
                        'x-data' => '',
                        'x-on:click' => 'navigator.clipboard.writeText("'.$uri.'"); $tooltip("Tersalin!")',
                    ])
            );
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $toSave = [
            'oauth_google_enabled' => $data['oauth_google_enabled'] ?? false,
            'oauth_google_client_id' => $data['oauth_google_client_id'] ?? null,
        ];

        // Hanya simpan secret jika diisi (tidak kosong)
        foreach (['google'] as $provider) {
            $secretKey = "oauth_{$provider}_client_secret";
            if (! blank($data[$secretKey] ?? null)) {
                $toSave[$secretKey] = $data[$secretKey];
            }
        }

        // Cloudflare Turnstile
        $toSave['turnstile_enabled'] = $data['turnstile_enabled'] ?? false;
        $toSave['turnstile_site_key'] = $data['turnstile_site_key'] ?? null;

        if (! blank($data['turnstile_secret_key'] ?? null)) {
            $toSave['turnstile_secret_key'] = $data['turnstile_secret_key'];
        }

        Setting::setMany($toSave);

        Notification::make()
            ->success()
            ->title('Pengaturan integrasi berhasil disimpan')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action('save'),
        ];
    }
}
