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

class NavbarSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Menu Navigasi';

    protected static ?string $title = 'Pengaturan Menu Navigasi';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $saved = json_decode(Setting::get('nav_items', ''), true);

        $this->form->fill([
            'items' => is_array($saved) ? $saved : $this->defaultNavItems(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Menu')
                ->description('Seret untuk mengubah urutan. Setiap item dapat memiliki sub-menu (maks 1 level).')
                ->icon(Heroicon::OutlinedBars3)
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->schema([
                            Grid::make(4)->schema([
                                TextInput::make('label')
                                    ->label('Teks Menu')
                                    ->required()
                                    ->maxLength(60)
                                    ->placeholder('Beranda')
                                    ->columnSpan(2),

                                TextInput::make('url')
                                    ->label('URL / Tautan')
                                    ->required()
                                    ->maxLength(300)
                                    ->placeholder('/ atau #spmb atau /guru')
                                    ->columnSpan(1),

                                Select::make('target')
                                    ->label('Buka di')
                                    ->options(['_self' => 'Tab Sama', '_blank' => 'Tab Baru'])
                                    ->default('_self')
                                    ->columnSpan(1),
                            ]),

                            Toggle::make('is_active')
                                ->label('Tampilkan item ini')
                                ->default(true)
                                ->onColor('success')
                                ->inline(false),

                            Repeater::make('children')
                                ->label('Sub Menu')
                                ->schema([
                                    Grid::make(4)->schema([
                                        TextInput::make('label')
                                            ->label('Teks')
                                            ->required()
                                            ->maxLength(60)
                                            ->placeholder('Kurikulum')
                                            ->columnSpan(2),

                                        TextInput::make('url')
                                            ->label('URL')
                                            ->required()
                                            ->maxLength(300)
                                            ->placeholder('#kurikulum')
                                            ->columnSpan(1),

                                        Select::make('target')
                                            ->label('Buka di')
                                            ->options(['_self' => 'Tab Sama', '_blank' => 'Tab Baru'])
                                            ->default('_self')
                                            ->columnSpan(1),
                                    ]),

                                    Toggle::make('is_active')
                                        ->label('Tampilkan')
                                        ->default(true)
                                        ->onColor('success')
                                        ->inline(false),
                                ])
                                ->reorderable()
                                ->collapsible()
                                ->collapsed()
                                ->defaultItems(0)
                                ->itemLabel(fn (array $state): string => $state['label'] ?? 'Item baru')
                                ->addActionLabel('+ Tambah Sub Menu')
                                ->columnSpanFull(),
                        ])
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): string => $state['label'] ?? 'Item baru')
                        ->addActionLabel('+ Tambah Item Menu')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('nav_items', json_encode($data['items'] ?? []));

        Notification::make()
            ->success()
            ->title('Menu navigasi berhasil disimpan')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Menu')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action('save'),

            Action::make('reset')
                ->label('Reset ke Default')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowPath)
                ->requiresConfirmation()
                ->modalHeading('Reset Menu Navigasi?')
                ->modalDescription('Ini akan mengembalikan menu ke pengaturan awal bawaan. Perubahan yang ada akan hilang.')
                ->action(function () {
                    Setting::set('nav_items', json_encode($this->defaultNavItems()));

                    $this->form->fill(['items' => $this->defaultNavItems()]);

                    Notification::make()
                        ->success()
                        ->title('Menu navigasi direset ke default')
                        ->send();
                }),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function defaultNavItems(): array
    {
        return [
            ['label' => 'Beranda',  'url' => '/',          'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'Profil',   'url' => '#profil',    'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'SPMB',     'url' => '#spmb',      'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'Akademik', 'url' => '#akademik',  'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'Guru',     'url' => '/guru',      'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'Blog',     'url' => '/blog',      'target' => '_self', 'is_active' => true, 'children' => []],
            ['label' => 'Kontak',   'url' => '#kontak',    'target' => '_self', 'is_active' => true, 'children' => []],
        ];
    }
}
