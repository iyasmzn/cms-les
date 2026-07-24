<?php

namespace App\Filament\Resources\Slides\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SlideForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gambar')
                ->description('Gambar latar belakang slide. Rasio 16:9, minimal 1280×720px disarankan.')
                ->schema([
                    self::imagePicker(
                        key: 'image',
                        label: 'Gambar Slide',
                        hint: 'Akan di-resize ke 1600×900px (16:9). Biarkan kosong untuk menggunakan placeholder.',
                        accepted: ['image/jpeg', 'image/png', 'image/webp'],
                        width: 1600,
                        height: 900,
                        directory: 'slides',
                        aspectRatio: '16:9',
                    ),
                ]),

            Section::make('Konten')
                ->schema([
                    TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Unggul dalam Akademik')
                        ->columnSpanFull(),

                    TextInput::make('subtitle')
                        ->label('Subjudul / Deskripsi')
                        ->maxLength(500)
                        ->placeholder('Raih prestasi terbaik bersama guru-guru berpengalaman.')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('button_label')
                            ->label('Teks Tombol CTA')
                            ->maxLength(100)
                            ->placeholder('Daftar Sekarang')
                            ->hint('Kosongkan jika tidak perlu tombol.'),

                        TextInput::make('button_url')
                            ->label('URL Tombol CTA')
                            ->maxLength(255)
                            ->placeholder('https://... atau #spmb'),
                    ]),
                ]),

            Section::make('Pengaturan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->hint('Angka kecil tampil lebih dulu.'),

                        Toggle::make('is_active')
                            ->label('Aktif / Tampilkan')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                ]),
        ]);
    }
}
