<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ErrorPageSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.error-page-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Halaman Error';

    protected static ?string $title = 'Halaman Error';

    protected static ?int $navigationSort = 7;

    /**
     * The HTTP error codes that can be customised, with their default copy.
     *
     * @var array<string, array{label: string, title: string, message: string}>
     */
    protected const ERROR_CODES = [
        '404' => [
            'label' => '404 — Tidak Ditemukan',
            'title' => 'Halaman Tidak Ditemukan',
            'message' => 'Maaf, halaman yang Anda cari tidak dapat ditemukan atau mungkin telah dipindahkan.',
        ],
        '403' => [
            'label' => '403 — Akses Ditolak',
            'title' => 'Akses Ditolak',
            'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
        ],
        '419' => [
            'label' => '419 — Sesi Kedaluwarsa',
            'title' => 'Halaman Kedaluwarsa',
            'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.',
        ],
        '429' => [
            'label' => '429 — Terlalu Banyak Permintaan',
            'title' => 'Terlalu Banyak Permintaan',
            'message' => 'Anda mengirim terlalu banyak permintaan dalam waktu singkat. Silakan tunggu sebentar lalu coba lagi.',
        ],
        '500' => [
            'label' => '500 — Kesalahan Server',
            'title' => 'Terjadi Kesalahan',
            'message' => 'Maaf, terjadi kesalahan pada server kami. Tim kami telah diberitahu dan sedang menanganinya.',
        ],
        '503' => [
            'label' => '503 — Pemeliharaan',
            'title' => 'Sedang Dalam Pemeliharaan',
            'message' => 'Situs sedang dalam pemeliharaan untuk peningkatan layanan. Silakan kembali beberapa saat lagi.',
        ],
    ];

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $values = [
            'error_show_home_button' => (bool) Setting::get('error_show_home_button', true),
            'error_show_back_button' => (bool) Setting::get('error_show_back_button', true),
            'error_support_email' => Setting::get('error_support_email'),
        ];

        foreach (self::ERROR_CODES as $code => $defaults) {
            $values["error_{$code}_title"] = Setting::get("error_{$code}_title", $defaults['title']);
            $values["error_{$code}_message"] = Setting::get("error_{$code}_message", $defaults['message']);
        }

        $this->form->fill($values);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $sections = [
            Section::make('Tampilan Umum')
                ->description('Pengaturan yang berlaku untuk seluruh halaman error.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('error_show_home_button')
                            ->label('Tampilkan tombol "Kembali ke Beranda"')
                            ->default(true),

                        Toggle::make('error_show_back_button')
                            ->label('Tampilkan tombol "Halaman Sebelumnya"')
                            ->default(true),
                    ]),

                    TextInput::make('error_support_email')
                        ->label('Email Dukungan')
                        ->email()
                        ->placeholder('support@sekolah.sch.id')
                        ->helperText('Ditampilkan pada halaman error server (500). Jika kosong, memakai email kontak umum.')
                        ->columnSpanFull(),
                ]),
        ];

        foreach (self::ERROR_CODES as $code => $defaults) {
            $sections[] = Section::make($defaults['label'])
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextInput::make("error_{$code}_title")
                        ->label('Judul')
                        ->maxLength(100)
                        ->placeholder($defaults['title']),

                    Textarea::make("error_{$code}_message")
                        ->label('Pesan')
                        ->rows(2)
                        ->maxLength(300)
                        ->placeholder($defaults['message']),
                ]);
        }

        return $schema->components($sections);
    }

    public function save(): void
    {
        Setting::setMany($this->form->getState());

        Notification::make()
            ->success()
            ->title('Pengaturan halaman error berhasil disimpan')
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
