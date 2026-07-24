<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ThemeSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Tema & Tampilan';

    protected static ?string $title = 'Pengaturan Tema & Tampilan';

    protected static ?int $navigationSort = 6;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $savedColor = Setting::get('theme_primary_color', '#d97706');
        $savedFont = Setting::get('theme_font', 'instrument-sans');

        $this->form->fill([
            'theme_preset' => $this->matchPreset($savedColor),
            'theme_primary_color' => $savedColor,
            'theme_font' => $savedFont,
            'theme_font_custom_url' => Setting::get('theme_font_custom_url', ''),
            'theme_font_custom_family' => Setting::get('theme_font_custom_family', ''),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Warna Utama Website')
                ->description('Warna ini diterapkan ke seluruh elemen utama website: tombol, label, highlight, dan aksen navigasi.')
                ->icon(Heroicon::OutlinedSwatch)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('theme_preset')
                            ->label('Preset Warna')
                            ->options(self::presets())
                            ->searchable(false)
                            ->placeholder('— Pilih preset —')
                            ->live()
                            ->afterStateUpdated(
                                fn (Set $set, ?string $state) => $state
                                    ? $set('theme_primary_color', $state)
                                    : null
                            )
                            ->hint('Pilih preset atau ubah warna secara kustom di sebelah kanan.'),

                        ColorPicker::make('theme_primary_color')
                            ->label('Warna Kustom (HEX)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (Set $set, ?string $state) => $set(
                                    'theme_preset',
                                    $this->matchPreset($state ?? '#d97706')
                                )
                            )
                            ->hint('Klik kotak warna untuk membuka color picker.'),
                    ]),
                ]),

            Section::make('Tipografi')
                ->description('Pilih font yang digunakan di seluruh tampilan publik website.')
                ->icon(Heroicon::OutlinedDocumentText)
                ->schema([
                    Select::make('theme_font')
                        ->label('Font Website')
                        ->options(self::fonts())
                        ->allowHtml()
                        ->searchable()
                        ->live()
                        ->default('instrument-sans')
                        ->hint('Setiap opsi ditampilkan dengan font aslinya. Perubahan langsung terlihat setelah disimpan.'),

                    TextInput::make('theme_font_custom_url')
                        ->label('URL / Embed Google Fonts')
                        ->placeholder('<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700"> — atau tempel URL-nya saja')
                        ->visible(fn (Get $get): bool => $get('theme_font') === 'custom')
                        ->live(debounce: 600)
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            if ($family = google_font_family($state)) {
                                $set('theme_font_custom_family', $family);
                            }
                        })
                        ->helperText('Buka fonts.google.com → pilih font → "Get font" → "Get embed code" → salin baris <link> atau @import, lalu tempel di sini. Pratinjau muncul otomatis.'),

                    TextInput::make('theme_font_custom_family')
                        ->label('Nama Font (font-family)')
                        ->placeholder('mis. Roboto Slab')
                        ->visible(fn (Get $get): bool => $get('theme_font') === 'custom')
                        ->live(debounce: 600)
                        ->helperText('Terisi otomatis dari URL di atas — samakan persis dengan nama font di Google Fonts.'),

                    Placeholder::make('font_preview')
                        ->label('Pratinjau')
                        ->content(function (Get $get): HtmlString {
                            if (($get('theme_font') ?? 'instrument-sans') === 'custom') {
                                return self::customFontPreview($get('theme_font_custom_family'), $get('theme_font_custom_url'));
                            }

                            return self::fontPreview($get('theme_font') ?? 'instrument-sans');
                        }),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('theme_primary_color', $data['theme_primary_color'] ?? '#d97706');
        Setting::set('theme_font', $data['theme_font'] ?? 'instrument-sans');
        Setting::set('theme_font_custom_url', google_font_url($data['theme_font_custom_url'] ?? '') ?? '');
        Setting::set('theme_font_custom_family', clean_font_family_name($data['theme_font_custom_family'] ?? ''));

        Notification::make()
            ->success()
            ->title('Tema berhasil disimpan')
            ->body('Perubahan warna dan font akan langsung terlihat di website.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Tema')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action('save'),

            Action::make('reset')
                ->label('Reset ke Default')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowPath)
                ->requiresConfirmation()
                ->modalHeading('Reset Tema?')
                ->modalDescription('Warna akan dikembalikan ke Amber dan font ke Instrument Sans (default).')
                ->action(function (): void {
                    Setting::set('theme_primary_color', '#d97706');
                    Setting::set('theme_font', 'instrument-sans');
                    Setting::set('theme_font_custom_url', '');
                    Setting::set('theme_font_custom_family', '');

                    $this->form->fill([
                        'theme_preset' => '#d97706',
                        'theme_primary_color' => '#d97706',
                        'theme_font' => 'instrument-sans',
                        'theme_font_custom_url' => '',
                        'theme_font_custom_family' => '',
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Tema direset ke default (Amber + Instrument Sans)')
                        ->send();
                }),
        ];
    }

    /**
     * Returns preset color options.
     *
     * @return array<string, string>
     */
    public static function presets(): array
    {
        return [
            '#d97706' => '🟠 Amber (Default)',
            '#f59e0b' => '🟡 Kuning',
            '#2563eb' => '🔵 Biru',
            '#4f46e5' => '🟣 Indigo',
            '#9333ea' => '🟣 Ungu',
            '#e11d48' => '🌸 Rose',
            '#dc2626' => '🔴 Merah',
            '#16a34a' => '🟢 Hijau',
            '#0d9488' => '🩵 Teal',
            '#0891b2' => '🔵 Cyan',
            '#ea580c' => '🟠 Oranye',
        ];
    }

    /**
     * Single source of truth for the available fonts. Pulled from config/fonts.php
     * and shared with the public layouts.
     *
     * @return array<string, array{label: string, family: string, google: string, group: string, bundled?: bool}>
     */
    public static function fontFamilies(): array
    {
        return config('fonts');
    }

    /**
     * Returns available font options grouped by category, with each label
     * rendered in its own font. Requires `allowHtml()` + `searchable()`.
     *
     * @return array<string, array<string, string>>
     */
    public static function fonts(): array
    {
        $options = [];

        foreach (self::fontFamilies() as $key => $font) {
            $options[$font['group']][$key] = '<span style="font-family: '.e($font['family']).'; font-size: 0.95rem;">'.e($font['label']).'</span>';
        }

        $options['Kustom']['custom'] = '<span style="font-size: 0.95rem;">✎ Font Kustom — tempel dari Google Fonts</span>';

        return $options;
    }

    /**
     * Builds the live preview markup for the given predefined font key.
     */
    public static function fontPreview(string $key): HtmlString
    {
        $family = self::fontFamilies()[$key]['family'] ?? self::fontFamilies()['instrument-sans']['family'];

        return self::buildPreview($family);
    }

    /**
     * Builds the live preview markup for a custom, admin-provided font. The
     * webfont stylesheet is embedded directly in the preview so it loads
     * server-side as soon as the URL is pasted — no extra client script needed.
     */
    public static function customFontPreview(?string $name, ?string $url): HtmlString
    {
        $name = clean_font_family_name($name);
        $family = $name !== ''
            ? '"'.$name.'", ui-sans-serif, system-ui, sans-serif'
            : 'ui-sans-serif, system-ui, sans-serif';

        $href = google_font_url($url);
        $stylesheet = $href ? '<link rel="stylesheet" href="'.e($href).'">' : '';

        return self::buildPreview($family, $stylesheet);
    }

    /**
     * Renders the shared preview card for a resolved CSS font-family stack,
     * optionally preceded by a stylesheet that loads the webfont.
     */
    protected static function buildPreview(string $family, string $stylesheet = ''): HtmlString
    {
        $family = e($family);

        return new HtmlString(<<<HTML
            {$stylesheet}
            <div style="font-family: {$family}; border: 1px solid rgba(0,0,0,.1); border-radius: .75rem; padding: 1rem 1.25rem; background: #fff;">
                <div style="font-size: 1.5rem; font-weight: 700; line-height: 1.3; color: #1d1d1f;">Ar-Rahman Qur'an Academy</div>
                <div style="font-size: 1rem; font-weight: 500; margin-top: .25rem; color: #1d1d1f;">Menyemai generasi Qur'ani yang berakhlak.</div>
                <p style="font-size: .875rem; margin-top: .5rem; color: #6e6e73;">The quick brown fox jumps over the lazy dog — 0123456789</p>
            </div>
        HTML);
    }

    /**
     * Combined Google Fonts stylesheet URL that loads every selectable font so
     * the dropdown options and the live preview render in their real typeface.
     */
    public function googleFontsHref(): string
    {
        $families = collect(self::fontFamilies())
            ->pluck('google')
            ->filter()
            ->map(fn (string $google): string => 'family='.$google)
            ->implode('&');

        return 'https://fonts.googleapis.com/css2?'.$families.'&display=swap';
    }

    /**
     * Returns the preset key if the given color matches one, otherwise null.
     */
    private function matchPreset(string $color): ?string
    {
        $normalized = strtolower(trim($color));

        return array_key_exists($normalized, self::presets()) ? $normalized : null;
    }
}
