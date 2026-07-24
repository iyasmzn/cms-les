<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FooterSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Footer';

    protected static ?string $title = 'Pengaturan Footer';

    protected static ?int $navigationSort = 4;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $links = json_decode(Setting::get('footer_service_links', ''), true);

        $this->form->fill([
            'footer_services_enabled' => setting_bool('footer_services_enabled', true),
            'footer_services_title' => Setting::get('footer_services_title', 'Layanan'),
            'items' => is_array($links) ? $links : $this->defaultServiceLinks(),

            'footer_copyright' => Setting::get('footer_copyright'),
            'footer_credit_enabled' => setting_bool('footer_credit_enabled', true),
            'footer_credit_text' => Setting::get('footer_credit_text', 'Dibuat dengan penuh semangat'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Kolom Layanan')
                ->description('Daftar tautan pada kolom "Layanan" di footer. Seret untuk mengubah urutan.')
                ->icon(Heroicon::OutlinedSquares2x2)
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('footer_services_enabled')
                            ->label('Tampilkan kolom Layanan')
                            ->default(true)
                            ->onColor('success'),

                        TextInput::make('footer_services_title')
                            ->label('Judul Kolom')
                            ->maxLength(40)
                            ->placeholder('Layanan'),
                    ]),

                    Repeater::make('items')
                        ->label('')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('label')
                                    ->label('Teks')
                                    ->required()
                                    ->maxLength(60)
                                    ->placeholder('Blog & Berita')
                                    ->columnSpan(4),

                                TextInput::make('url')
                                    ->label('URL / Tautan')
                                    ->required()
                                    ->maxLength(300)
                                    ->placeholder('/blog atau https://...')
                                    ->columnSpan(4),

                                Select::make('feature')
                                    ->label('Tampil jika fitur aktif')
                                    ->options([
                                        '' => 'Selalu tampil',
                                        'toko' => 'Toko aktif',
                                        'donasi' => 'Donasi aktif',
                                        'pertanyaan' => 'Pertanyaan aktif',
                                    ])
                                    ->default('')
                                    ->columnSpan(3),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->onColor('success')
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                        ])
                        ->addActionLabel('+ Tambah Tautan')
                        ->reorderable()
                        ->reorderableWithDragAndDrop()
                        ->maxItems(10)
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): string => $state['label'] ?? 'Tautan baru')
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull(),
                ]),

            Section::make('Baris Bawah (Copyright)')
                ->description('Teks hak cipta dan kredit yang tampil di bagian paling bawah footer.')
                ->icon(Heroicon::OutlinedInformationCircle)
                ->schema([
                    TextInput::make('footer_copyright')
                        ->label('Teks Hak Cipta')
                        ->maxLength(200)
                        ->placeholder('© {tahun} {nama_situs}. Semua hak dilindungi.')
                        ->helperText('Kosongkan untuk memakai default. Gunakan {tahun} dan {nama_situs} sebagai placeholder.')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Toggle::make('footer_credit_enabled')
                            ->label('Tampilkan teks kredit')
                            ->default(true)
                            ->onColor('success'),

                        TextInput::make('footer_credit_text')
                            ->label('Teks Kredit')
                            ->maxLength(60)
                            ->placeholder('Dibuat dengan penuh semangat'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setMany([
            'footer_services_enabled' => $data['footer_services_enabled'] ? '1' : '0',
            'footer_services_title' => $data['footer_services_title'] ?? 'Layanan',
            'footer_service_links' => json_encode(array_values($data['items'] ?? [])),
            'footer_copyright' => $data['footer_copyright'] ?? '',
            'footer_credit_enabled' => $data['footer_credit_enabled'] ? '1' : '0',
            'footer_credit_text' => $data['footer_credit_text'] ?? '',
        ]);

        Notification::make()
            ->success()
            ->title('Pengaturan footer berhasil disimpan')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action('save'),

            Action::make('reset')
                ->label('Reset Tautan Layanan')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowPath)
                ->requiresConfirmation()
                ->modalHeading('Reset Tautan Layanan?')
                ->modalDescription('Ini akan mengembalikan tautan kolom Layanan ke pengaturan awal bawaan.')
                ->action(function (): void {
                    Setting::set('footer_service_links', json_encode($this->defaultServiceLinks()));

                    $this->form->fill(['items' => $this->defaultServiceLinks()]);

                    Notification::make()
                        ->success()
                        ->title('Tautan Layanan direset ke default')
                        ->send();
                }),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function defaultServiceLinks(): array
    {
        return [
            ['label' => 'Toko',            'url' => '/produk',   'feature' => 'toko', 'is_active' => true],
            ['label' => 'Daftar Santri',   'url' => '/ppdb',     'feature' => '',     'is_active' => true],
            ['label' => 'Blog & Berita',   'url' => '/blog',     'feature' => '',     'is_active' => true],
            ['label' => 'Unduhan',         'url' => '/unduhan',  'feature' => '',       'is_active' => true],
            ['label' => 'Tenaga Pendidik', 'url' => '/guru',     'feature' => '',       'is_active' => true],
            ['label' => 'Donasi',          'url' => '/donasi',   'feature' => 'donasi', 'is_active' => true],
            ['label' => 'Keranjang',       'url' => '/keranjang', 'feature' => 'toko',  'is_active' => true],
        ];
    }
}
