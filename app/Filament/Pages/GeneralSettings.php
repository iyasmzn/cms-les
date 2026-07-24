<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\MediaLibraryService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GeneralSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Umum';

    protected static ?string $title = 'Pengaturan Umum';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Identitas Sekolah
            'site_name' => Setting::get('site_name', config('app.name')),
            'site_tagline' => Setting::get('site_tagline', 'Unggul, Berkarakter, Berprestasi'),
            'site_description' => Setting::get('site_description'),

            // Media
            'site_logo' => Setting::get('site_logo'),
            'site_favicon' => Setting::get('site_favicon'),

            // Kontak
            'contact_address' => Setting::get('contact_address'),
            'contact_phone' => Setting::get('contact_phone'),
            'contact_email' => Setting::get('contact_email'),
            'contact_hours' => Setting::get('contact_hours', 'Senin–Jumat, 07.00–15.30 WIB'),
            'contact_map_url' => Setting::get('contact_map_url'),

            // Sosial Media
            'social_facebook' => Setting::get('social_facebook'),
            'social_instagram' => Setting::get('social_instagram'),
            'social_youtube' => Setting::get('social_youtube'),
            'social_tiktok' => Setting::get('social_tiktok'),
            'social_telegram' => Setting::get('social_telegram'),
            'social_whatsapp' => Setting::get('social_whatsapp'),

            // Toko Buku
            'shop_whatsapp' => Setting::get('shop_whatsapp'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Identitas Sekolah')
                ->description('Nama dan informasi dasar yang tampil di seluruh halaman website.')
                ->icon(Heroicon::OutlinedAcademicCap)
                ->collapsible()
                ->persistCollapsed()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('site_name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('SMA Negeri 1 Bandung'),

                        TextInput::make('site_tagline')
                            ->label('Tagline')
                            ->maxLength(100)
                            ->placeholder('Unggul, Berkarakter, Berprestasi'),
                    ]),

                    Textarea::make('site_description')
                        ->label('Deskripsi Singkat')
                        ->rows(3)
                        ->maxLength(300)
                        ->hint('Digunakan untuk meta description SEO. Maks 300 karakter.')
                        ->columnSpanFull(),
                ]),

            Section::make('Logo & Favicon')
                ->description('Gambar yang mewakili identitas visual sekolah di browser dan halaman web.')
                ->icon(Heroicon::OutlinedPhoto)
                ->collapsible()
                ->persistCollapsed()
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('site_logo')
                            ->label('Logo Sekolah')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->automaticallyResizeImagesToWidth('400')
                            ->automaticallyResizeImagesToHeight('400')
                            ->hint('Format PNG/SVG transparan disarankan. Maks 400×400px.'),

                        FileUpload::make('site_favicon')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->automaticallyResizeImagesToWidth('64')
                            ->automaticallyResizeImagesToHeight('64')
                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml'])
                            ->hint('Format ICO atau PNG 64×64px.'),
                    ]),
                ]),

            Section::make('Informasi Kontak')
                ->description('Informasi kontak yang tampil di footer dan halaman kontak.')
                ->icon(Heroicon::OutlinedMapPin)
                ->collapsible()
                ->persistCollapsed()
                ->schema([
                    Textarea::make('contact_address')
                        ->label('Alamat Lengkap')
                        ->rows(2)
                        ->placeholder('Jl. Pendidikan No. 1, Kota Bandung 40111')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('(022) 1234-5678'),

                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->placeholder('info@sman1.sch.id'),
                    ]),

                    TextInput::make('contact_hours')
                        ->label('Jam Operasional')
                        ->placeholder('Senin–Jumat, 07.00–15.30 WIB')
                        ->columnSpanFull(),

                    TextInput::make('contact_map_url')
                        ->label('URL Embed Google Maps')
                        ->url()
                        ->placeholder('https://www.google.com/maps/embed?pb=...')
                        ->hint('Buka Google Maps → Bagikan → Sematkan peta → Salin URL dari atribut src.')
                        ->helperText('Hanya URL dari google.com/maps/embed yang diterima.')
                        ->columnSpanFull(),
                ]),

            Section::make('Media Sosial')
                ->description('Tautan media sosial yang tampil di footer.')
                ->icon(Heroicon::OutlinedShare)
                ->collapsible()
                ->persistCollapsed()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('social_facebook')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/namahalaman')
                            ->prefixIcon(Heroicon::OutlinedGlobeAlt),

                        TextInput::make('social_instagram')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/namaakun')
                            ->prefixIcon(Heroicon::OutlinedGlobeAlt),

                        TextInput::make('social_youtube')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/@channel')
                            ->prefixIcon(Heroicon::OutlinedGlobeAlt),

                        TextInput::make('social_tiktok')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://tiktok.com/@namaakun')
                            ->prefixIcon(Heroicon::OutlinedGlobeAlt),

                        TextInput::make('social_telegram')
                            ->label('Telegram')
                            ->url()
                            ->placeholder('https://t.me/namaakun')
                            ->hint('Juga tampil sebagai kartu kontak di halaman.')
                            ->prefixIcon(Heroicon::OutlinedPaperAirplane),

                        TextInput::make('social_whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->placeholder('6281234567890')
                            ->hint('Nomor internasional tanpa + (contoh: 6281234567890)')
                            ->prefixIcon(Heroicon::OutlinedPhone),
                    ]),
                ]),

            Section::make('Toko Buku')
                ->description('Konfigurasi nomor WhatsApp yang menerima pesanan buku dari halaman checkout.')
                ->icon(Heroicon::OutlinedBookOpen)
                ->afterHeader([
                    Text::make('Fitur nonaktif')
                        ->badge()
                        ->color('gray')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->visible(fn (): bool => ! feature_enabled('toko')),
                ])
                ->collapsible()
                ->collapsed(fn (): bool => ! feature_enabled('toko'))
                ->persistCollapsed()
                ->schema([
                    TextInput::make('shop_whatsapp')
                        ->label('Nomor WhatsApp Toko')
                        ->tel()
                        ->placeholder('6281234567890')
                        ->hint('Nomor internasional tanpa + (contoh: 6281234567890). Pesanan checkout akan dikirim ke nomor ini.')
                        ->helperText('Jika kosong, checkout akan mencoba menggunakan nomor WhatsApp sosial media.')
                        ->prefixIcon(Heroicon::OutlinedPhone),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setMany($data);

        // Sync uploaded logo/favicon to the Media Library
        $media = app(MediaLibraryService::class);
        foreach (['site_logo', 'site_favicon'] as $key) {
            if (! blank($data[$key] ?? null)) {
                $media->sync($data[$key]);
            }
        }

        Notification::make()
            ->success()
            ->title('Pengaturan berhasil disimpan')
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
