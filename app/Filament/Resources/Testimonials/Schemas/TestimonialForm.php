<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(150)
                        ->placeholder('Budi Santoso')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('class_year')
                            ->label('Angkatan (Tahun Masuk)')
                            ->maxLength(10)
                            ->placeholder('2020')
                            ->hint('Tahun pertama masuk sekolah.'),

                        TextInput::make('graduation_year')
                            ->label('Tahun Lulus')
                            ->maxLength(10)
                            ->placeholder('2023'),
                    ]),

                    self::imagePicker(
                        key: 'photo',
                        label: 'Foto',
                        hint: 'Opsional. Akan di-crop menjadi persegi 300×300px.',
                        accepted: ['image/jpeg', 'image/png', 'image/webp'],
                        width: 300,
                        height: 300,
                        directory: 'testimonials',
                        aspectRatio: '1:1',
                    )->columnSpanFull(),
                ]),

            Section::make('Kesan & Pesan')
                ->schema([
                    Textarea::make('message')
                        ->label('Isi Kesan & Pesan')
                        ->required()
                        ->rows(5)
                        ->maxLength(1000)
                        ->placeholder('Tuliskan kesan dan pesan selama bersekolah di sini...')
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
