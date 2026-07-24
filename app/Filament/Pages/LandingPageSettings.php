<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
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

class LandingPageSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Halaman Depan';

    protected static ?string $title = 'Pengaturan Halaman Depan';

    protected static ?int $navigationSort = 3;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @return array<int, array{key: string, label: string, visible: bool}> */
    private static function defaultSections(): array
    {
        return [
            ['key' => 'section_hero',        'label' => '🖼️  Hero Image Slider',        'visible' => true],
            ['key' => 'section_quick_links', 'label' => '🔗  Tautan Cepat',              'visible' => true],
            ['key' => 'section_spmb',        'label' => '📋  Kartu SPMB',               'visible' => true],
            ['key' => 'section_stats',       'label' => '📊  Statistik Sekolah',         'visible' => true],
            ['key' => 'section_principal',   'label' => '👨‍💼  Sambutan Para Tokoh',     'visible' => true],
            ['key' => 'section_spmb_steps',  'label' => '📝  Tahapan SPMB',             'visible' => true],
            ['key' => 'section_programs',    'label' => '🎓  Program Unggulan',          'visible' => true],
            ['key' => 'section_events',      'label' => '📅  Agenda Kegiatan',           'visible' => true],
            ['key' => 'section_products',    'label' => '🛍️  Produk Toko',              'visible' => true],
            ['key' => 'section_gallery',     'label' => '🖼️  Galeri Foto',              'visible' => true],
            ['key' => 'section_blog',        'label' => '📰  Blog & Berita',             'visible' => true],
            ['key' => 'section_testimonials', 'label' => '💬  Kesan & Pesan Alumni',      'visible' => true],
            ['key' => 'section_donasi',      'label' => '💝  Donasi',                   'visible' => true],
            ['key' => 'section_contact',     'label' => '📞  Kontak Kami',               'visible' => true],
        ];
    }

    /**
     * Sections whose header text (eyebrow, title, subtitle) can be edited here.
     * Keys map to the `section_{key}_*` setting keys read by the Blade partials.
     * `highlight` marks a section whose title has an accented second line.
     *
     * @return array<string, array{label: string, icon: Heroicon, eyebrow: string, title: string, subtitle: string, highlight?: string}>
     */
    private static function contentSections(): array
    {
        return [
            'programs' => [
                'label' => 'Program Unggulan',
                'icon' => Heroicon::OutlinedAcademicCap,
                'eyebrow' => 'Keunggulan Kami',
                'title' => 'Program Unggulan',
                'subtitle' => 'Berbagai program yang dirancang untuk membentuk santri berprestasi dan berakhlak mulia.',
            ],
            'events' => [
                'label' => 'Agenda Kegiatan',
                'icon' => Heroicon::OutlinedCalendarDays,
                'eyebrow' => 'Agenda Pesantren',
                'title' => 'Kegiatan Akan Datang',
                'subtitle' => 'Pengajian, seminar, dan berbagai kegiatan menarik yang segera diselenggarakan.',
            ],
            'products' => [
                'label' => 'Produk Toko',
                'icon' => Heroicon::OutlinedShoppingBag,
                'eyebrow' => 'Toko',
                'title' => 'Produk Pilihan',
                'subtitle' => 'Beragam produk berkualitas dengan harga terbaik, siap dipesan.',
            ],
            'gallery' => [
                'label' => 'Galeri Foto',
                'icon' => Heroicon::OutlinedPhoto,
                'eyebrow' => 'Foto & Video',
                'title' => 'Galeri Sekolah',
                'subtitle' => 'Momen-momen berharga dari kehidupan sekolah kami.',
            ],
            'blog' => [
                'label' => 'Blog & Berita',
                'icon' => Heroicon::OutlinedNewspaper,
                'eyebrow' => 'Berita & Artikel',
                'title' => 'Artikel',
                'subtitle' => 'Blog inspiratif dari berbagai sumber.',
            ],
            'testimonials' => [
                'label' => 'Kesan & Pesan Alumni',
                'icon' => Heroicon::OutlinedChatBubbleBottomCenterText,
                'eyebrow' => 'Kesan & Pesan',
                'title' => 'Apa Kata Alumni',
                'subtitle' => 'Cerita dan harapan dari para alumni yang telah menempuh pendidikan bersama kami.',
            ],
            'principal' => [
                'label' => 'Sambutan Para Tokoh',
                'icon' => Heroicon::OutlinedChatBubbleLeftRight,
                'eyebrow' => 'Sambutan',
                'title' => 'Sambutan Para Tokoh',
                'subtitle' => '',
            ],
            'contact' => [
                'label' => 'Kontak Kami',
                'icon' => Heroicon::OutlinedPhone,
                'eyebrow' => 'Hubungi Kami',
                'title' => 'Kami Siap Membantu Anda',
                'subtitle' => 'Punya pertanyaan seputar SPMB, akademik, atau kegiatan sekolah? Jangan ragu untuk menghubungi kami.',
            ],
            'donasi' => [
                'label' => 'Donasi',
                'icon' => Heroicon::OutlinedHeart,
                'eyebrow' => 'Program Donasi',
                'title' => 'Bersama Wujudkan',
                'subtitle' => 'Setiap kontribusi Anda sangat berarti bagi perkembangan pendidikan santri. Donasi Anda akan digunakan untuk pengadaan sarana belajar, beasiswa, dan program-program pesantren.',
                'highlight' => 'Pendidikan Berkualitas',
            ],
        ];
    }

    public function mount(): void
    {
        $defaults = self::defaultSections();
        $labelMap = collect($defaults)->keyBy('key');

        $saved = Setting::get('section_order');
        $savedSections = $saved ? (json_decode($saved, true) ?: []) : [];

        // Preserve the saved order and visibility for known sections, always
        // refreshing the label from the canonical list.
        $sections = [];
        $seen = [];
        foreach ($savedSections as $section) {
            $key = $section['key'] ?? null;
            if (! $key || ! $labelMap->has($key)) {
                continue;
            }

            $sections[] = [
                'key' => $key,
                'label' => $labelMap->get($key)['label'],
                'visible' => (bool) ($section['visible'] ?? true),
            ];
            $seen[$key] = true;
        }

        // Append any sections added after the saved order was stored so they
        // remain toggleable (e.g. the gallery section).
        foreach ($defaults as $section) {
            if (! isset($seen[$section['key']])) {
                $sections[] = $section;
            }
        }

        $fill = [
            'sections' => $sections,
            'home_meta_title' => Setting::get('home_meta_title', ''),
            'home_meta_description' => Setting::get('home_meta_description', ''),
        ];

        // Pre-fill each editable section's content with its current value,
        // falling back to the canonical default text so admins see what is live.
        foreach (self::contentSections() as $key => $content) {
            $fill["section_{$key}_eyebrow"] = Setting::get("section_{$key}_eyebrow", $content['eyebrow']);
            $fill["section_{$key}_title"] = Setting::get("section_{$key}_title", $content['title']);
            $fill["section_{$key}_subtitle"] = Setting::get("section_{$key}_subtitle", $content['subtitle']);

            if (isset($content['highlight'])) {
                $fill["section_{$key}_title_highlight"] = Setting::get("section_{$key}_title_highlight", $content['highlight']);
            }
        }

        $this->form->fill($fill);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $components = [
            Section::make('Urutan & Visibilitas Seksi')
                ->description('Drag dan drop untuk mengatur urutan tampilan. Aktifkan atau nonaktifkan setiap seksi.')
                ->icon(Heroicon::OutlinedQueueList)
                ->schema([
                    Repeater::make('sections')
                        ->label('')
                        ->addable(false)
                        ->deletable(false)
                        ->reorderableWithDragAndDrop(true)
                        ->schema([
                            TextInput::make('key')
                                ->hiddenLabel()
                                ->disabled()
                                ->dehydrated(true)
                                ->extraInputAttributes(['class' => 'hidden'])
                                ->columnSpan(0),

                            TextInput::make('label')
                                ->label('Seksi')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(3),

                            Toggle::make('visible')
                                ->label('Tampilkan')
                                ->onColor('success')
                                ->columnSpan(1),
                        ])
                        ->columns(4),
                ]),

            Section::make('SEO Halaman Depan')
                ->description('Judul dan deskripsi meta khusus halaman depan untuk mesin pencari (Google) dan berbagi ke media sosial.')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->collapsible()
                ->schema([
                    TextInput::make('home_meta_title')
                        ->label('Meta Title')
                        ->maxLength(70)
                        ->placeholder(setting('site_name', config('app.name')).' — '.setting('site_tagline', 'Unggul, Berkarakter, Berprestasi'))
                        ->helperText('Judul di tab browser & hasil pencarian. Ideal 50–60 karakter. Kosongkan untuk memakai Nama Sekolah + Tagline.')
                        ->columnSpanFull(),

                    Textarea::make('home_meta_description')
                        ->label('Meta Description')
                        ->rows(3)
                        ->maxLength(160)
                        ->helperText('Ringkasan yang tampil di hasil pencarian Google. Ideal 150–160 karakter. Kosongkan untuk memakai Deskripsi Singkat dari Pengaturan Umum.')
                        ->columnSpanFull(),
                ]),
        ];

        foreach (self::contentSections() as $key => $content) {
            $components[] = Section::make('Konten: '.$content['label'])
                ->description('Ubah teks judul dan deskripsi seksi ini. Kosongkan sebuah kolom untuk memakai teks bawaan.')
                ->icon($content['icon'])
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema($this->contentFields($key, $content)),
                ]);
        }

        return $schema->components($components);
    }

    /**
     * @param  array{label: string, icon: Heroicon, eyebrow: string, title: string, subtitle: string, highlight?: string}  $content
     * @return array<int, TextInput|Textarea>
     */
    private function contentFields(string $key, array $content): array
    {
        $fields = [
            TextInput::make("section_{$key}_eyebrow")
                ->label('Label Kecil')
                ->maxLength(60)
                ->placeholder($content['eyebrow'])
                ->helperText('Teks kecil di atas judul. Kosongkan untuk menyembunyikan.'),

            TextInput::make("section_{$key}_title")
                ->label('Judul')
                ->maxLength(120)
                ->placeholder($content['title']),
        ];

        if (isset($content['highlight'])) {
            $fields[] = TextInput::make("section_{$key}_title_highlight")
                ->label('Judul — Bagian Disorot')
                ->maxLength(120)
                ->placeholder($content['highlight'])
                ->helperText('Ditampilkan di baris kedua judul dengan warna aksen.')
                ->columnSpanFull();
        }

        $fields[] = Textarea::make("section_{$key}_subtitle")
            ->label('Deskripsi')
            ->rows(2)
            ->maxLength(300)
            ->placeholder($content['subtitle'])
            ->helperText('Kosongkan untuk menyembunyikan deskripsi.')
            ->columnSpanFull();

        return $fields;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // array_values preserves drag-and-drop order from Repeater
        $payload = collect($data)->except('sections')->all();
        $payload['section_order'] = json_encode(array_values($data['sections'] ?? []));

        Setting::setMany($payload);

        Notification::make()
            ->success()
            ->title('Pengaturan halaman depan disimpan')
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
