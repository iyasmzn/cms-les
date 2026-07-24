<?php

namespace App\Filament\Pages;

use App\Filament\Support\IconUpload;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class QuickLinksSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Tautan Cepat';

    protected static ?string $title = 'Pengaturan Tautan Cepat';

    protected static ?int $navigationSort = 5;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $saved = json_decode(Setting::get('quick_links', ''), true);

        $this->form->fill([
            'items' => is_array($saved) ? $saved : $this->defaultItems(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Tautan Cepat')
                ->description('Ikon emoji, label, dan URL untuk tiap tombol di baris tautan cepat halaman depan. Seret untuk mengubah urutan.')
                ->icon(Heroicon::OutlinedBolt)
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('icon')
                                    ->label('Ikon')
                                    ->maxLength(10)
                                    ->placeholder('📋')
                                    ->hint('Emoji')
                                    ->columnSpan(2),

                                TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(40)
                                    ->placeholder('SPMB')
                                    ->columnSpan(4),

                                TextInput::make('url')
                                    ->label('URL / Tautan')
                                    ->required()
                                    ->maxLength(300)
                                    ->placeholder('#spmb atau /unduhan')
                                    ->columnSpan(4),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->onColor('success')
                                    ->inline(false)
                                    ->columnSpan(2),

                                IconUpload::make()
                                    ->columnSpanFull(),
                            ]),
                        ])
                        ->addActionLabel('+ Tambah Tautan')
                        ->reorderable()
                        ->reorderableWithDragAndDrop()
                        ->maxItems(8)
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): string => trim(($state['icon'] ?? '').' '.($state['label'] ?? 'Item baru')))
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('quick_links', json_encode(array_values($data['items'] ?? [])));

        Notification::make()
            ->success()
            ->title('Tautan cepat berhasil disimpan')
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
                ->label('Reset ke Default')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowPath)
                ->requiresConfirmation()
                ->modalHeading('Reset Tautan Cepat?')
                ->modalDescription('Ini akan mengembalikan tautan cepat ke pengaturan awal bawaan.')
                ->action(function (): void {
                    Setting::set('quick_links', json_encode($this->defaultItems()));

                    $this->form->fill(['items' => $this->defaultItems()]);

                    Notification::make()
                        ->success()
                        ->title('Tautan cepat direset ke default')
                        ->send();
                }),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function defaultItems(): array
    {
        return [
            ['icon' => '📋', 'label' => 'SPMB',      'url' => '#spmb',       'is_active' => true],
            ['icon' => '📥', 'label' => 'Unduhan',    'url' => '/unduhan',    'is_active' => true],
            ['icon' => '📅', 'label' => 'Jadwal',     'url' => '#jadwal',     'is_active' => true],
            ['icon' => '🏆', 'label' => 'Prestasi',   'url' => '#prestasi',   'is_active' => true],
            ['icon' => '👥', 'label' => 'Alumni',     'url' => '#alumni',     'is_active' => true],
            ['icon' => '📞', 'label' => 'Kontak',     'url' => '#kontak',     'is_active' => true],
        ];
    }
}
