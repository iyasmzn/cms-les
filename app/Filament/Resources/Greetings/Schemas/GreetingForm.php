<?php

namespace App\Filament\Resources\Greetings\Schemas;

use App\Models\StaticPage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GreetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Tokoh')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Drs. Ahmad Fauzi, M.Pd.'),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(30)
                            ->placeholder('197601012005011001'),
                    ]),

                    TextInput::make('position')
                        ->label('Jabatan')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Kepala Sekolah, Kepala Yayasan, Ketua Komite, ...')
                        ->columnSpanFull(),

                    FileUpload::make('photo')
                        ->label('Foto')
                        ->image()
                        ->disk('public')
                        ->directory('greetings')
                        ->visibility('public')
                        ->automaticallyResizeImagesToWidth('600')
                        ->automaticallyResizeImagesToHeight('700')
                        ->hint('Foto formal, rasio portrait 3:4 disarankan.')
                        ->columnSpanFull(),
                ]),

            Section::make('Sambutan')
                ->description('Kutipan singkat yang muncul di slider homepage, bukan konten lengkap sambutan.')
                ->schema([
                    Textarea::make('excerpt')
                        ->label('Kutipan / Sambutan Singkat')
                        ->required()
                        ->rows(4)
                        ->maxLength(500)
                        ->hint('Maks 500 karakter. Tampil di homepage sebagai preview sambutan.')
                        ->placeholder('Kami berkomitmen memberikan pendidikan terbaik untuk mencetak generasi yang beriman, berilmu, dan berdaya saing...')
                        ->columnSpanFull(),

                    Select::make('page_slug')
                        ->label('Halaman Sambutan Lengkap')
                        ->options(fn () => StaticPage::active()->ordered()->pluck('title', 'slug'))
                        ->native(false)
                        ->searchable()
                        ->hint('Tombol "Baca Selengkapnya" di homepage akan mengarah ke halaman ini.')
                        ->placeholder('Pilih halaman statis...')
                        ->columnSpanFull(),
                ]),

            Section::make('Pengaturan Tampil')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->hint('Angka kecil tampil lebih dulu.'),

                        Toggle::make('is_published')
                            ->label('Tampilkan di Website')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                ]),
        ]);
    }
}
