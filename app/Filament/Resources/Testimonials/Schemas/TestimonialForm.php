<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
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

                    FileUpload::make('photo')
                        ->label('Foto')
                        ->image()
                        ->disk('public')
                        ->directory('testimonials')
                        ->visibility('public')
                        ->automaticallyCropImagesToAspectRatio('1:1')
                        ->automaticallyResizeImagesMode('cover')
                        ->automaticallyResizeImagesToWidth('300')
                        ->automaticallyResizeImagesToHeight('300')
                        ->hint('Opsional. Akan di-crop menjadi persegi 300×300px.')
                        ->columnSpanFull(),
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
