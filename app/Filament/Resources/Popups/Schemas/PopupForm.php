<?php

namespace App\Filament\Resources\Popups\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PopupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Konten')
                ->schema([
                    TextInput::make('youtube_url')
                        ->label('URL YouTube')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->hint('Jika diisi, video YouTube ditampilkan di atas popup (menggantikan gambar).')
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label('Gambar')
                        ->image()
                        ->disk('public')
                        ->directory('popups')
                        ->visibility('public')
                        ->hint('Diabaikan jika URL YouTube diisi.')
                        ->columnSpanFull(),

                    TextInput::make('title')
                        ->label('Judul')
                        ->maxLength(255)
                        ->placeholder('Pengumuman Penting')
                        ->columnSpanFull(),

                    Textarea::make('content')
                        ->label('Deskripsi')
                        ->rows(4)
                        ->maxLength(2000)
                        ->placeholder('Isi pesan atau keterangan yang ingin ditampilkan...')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('button_label')
                            ->label('Teks Tombol')
                            ->maxLength(100)
                            ->placeholder('Selengkapnya')
                            ->hint('Kosongkan jika tidak perlu tombol.'),

                        TextInput::make('button_url')
                            ->label('URL Tombol')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://...'),
                    ]),

                    Toggle::make('open_in_new_tab')
                        ->label('Buka Tombol di Tab Baru')
                        ->default(false),
                ]),

            Section::make('Pengaturan Tampil')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('width')
                            ->label('Lebar Popup')
                            ->options([
                                'sm' => 'Kecil (480px)',
                                'md' => 'Sedang (640px)',
                                'lg' => 'Besar (800px)',
                            ])
                            ->default('md')
                            ->required(),

                        TextInput::make('delay_seconds')
                            ->label('Tunda Tampil (detik)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(60)
                            ->hint('0 = langsung muncul saat halaman dibuka.'),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('show_every_days')
                            ->label('Tampilkan Setiap N Hari')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->hint('0 = sekali per sesi browser. 1 = setiap hari. 7 = tiap minggu.'),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->hint('Angka kecil tampil lebih dulu.'),
                    ]),

                    Grid::make(2)->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Tampil Mulai')
                            ->native(false)
                            ->hint('Kosongkan = selalu tampil.'),

                        DateTimePicker::make('ends_at')
                            ->label('Tampil Sampai')
                            ->native(false)
                            ->hint('Kosongkan = tidak ada batas akhir.'),
                    ]),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ]),
        ]);
    }
}
