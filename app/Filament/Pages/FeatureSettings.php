<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FeatureSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.feature-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Fitur Website';

    protected static ?string $title = 'Fitur Website';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function getNavigationBadge(): ?string
    {
        $disabled = collect(['login_register', 'donasi', 'toko', 'pertanyaan'])
            ->reject(fn (string $feature): bool => feature_enabled($feature))
            ->count();

        return $disabled > 0 ? (string) $disabled.' nonaktif' : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'gray';
    }

    public function mount(): void
    {
        $loginEnabled = setting_bool('feature_login_register', true);

        $this->form->fill([
            'feature_login_register' => $loginEnabled,
            'feature_donasi' => setting_bool('feature_donasi', true),
            'feature_toko' => setting_bool('feature_toko', true),
            // Locked to nonaktif whenever login/register is disabled.
            'feature_pertanyaan' => $loginEnabled && setting_bool('feature_pertanyaan', true),

            // Komentar
            'comment_user_auto_publish' => setting_bool('comment_user_auto_publish', true),
            'comment_guest_auto_publish' => setting_bool('comment_guest_auto_publish', true),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Autentikasi Pengguna')
                ->description('Fitur ini menjadi induk bagi fitur lain yang membutuhkan akun.')
                ->icon(Heroicon::OutlinedKey)
                ->schema([
                    Toggle::make('feature_login_register')
                        ->label('Login & Registrasi Pengguna')
                        ->default(true)
                        ->live()
                        ->afterStateUpdated(function (Set $set, bool $state): void {
                            // Mematikan login otomatis mengunci & mematikan pertanyaan.
                            if (! $state) {
                                $set('feature_pertanyaan', false);
                            }
                        })
                        ->helperText('Nonaktifkan agar pengunjung tidak bisa masuk atau mendaftar akun. Fitur pertanyaan ikut nonaktif, dan checkout toko tidak tersedia.'),
                ]),

            Section::make('Fitur Publik')
                ->description('Aktifkan atau nonaktifkan fitur yang tampil di website.')
                ->icon(Heroicon::OutlinedSquares2x2)
                ->schema([
                    Toggle::make('feature_donasi')
                        ->label('Donasi')
                        ->default(true)
                        ->helperText('Menampilkan halaman donasi dan formulir donasi.'),

                    Toggle::make('feature_toko')
                        ->label('Toko / Produk')
                        ->default(true)
                        ->helperText('Menampilkan toko dan keranjang. Jika Login & Registrasi nonaktif, produk tetap tampil namun proses checkout tidak tersedia.'),

                    Toggle::make('feature_pertanyaan')
                        ->label('Tanya Jawab (Pertanyaan)')
                        ->default(true)
                        ->disabled(fn (Get $get): bool => ! $get('feature_login_register'))
                        ->helperText(fn (Get $get): string => $get('feature_login_register')
                            ? 'Menampilkan halaman tanya jawab. Pengunjung wajib login untuk mengirim pertanyaan.'
                            : 'Terkunci: aktifkan Login & Registrasi terlebih dahulu karena pengirim pertanyaan wajib memiliki akun.'),
                ]),

            Section::make('Komentar Artikel')
                ->description('Atur apakah komentar baru langsung tampil atau menunggu persetujuan admin.')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema([
                    Toggle::make('comment_user_auto_publish')
                        ->label('Komentar pengguna terdaftar langsung tampil')
                        ->default(true)
                        ->helperText('Nonaktifkan agar komentar dari pengguna yang login harus disetujui admin dulu.'),

                    Toggle::make('comment_guest_auto_publish')
                        ->label('Komentar tamu (tanpa login) langsung tampil')
                        ->default(true)
                        ->helperText('Nonaktifkan agar komentar dari tamu masuk sebagai menunggu persetujuan admin.'),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Paksa pertanyaan nonaktif jika login/register dimatikan (dependensi).
        if (empty($data['feature_login_register'])) {
            $data['feature_pertanyaan'] = false;
        }

        Setting::setMany($data);

        Notification::make()
            ->success()
            ->title('Pengaturan fitur berhasil disimpan')
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
