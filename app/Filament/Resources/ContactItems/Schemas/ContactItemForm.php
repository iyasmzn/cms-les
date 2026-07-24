<?php

namespace App\Filament\Resources\ContactItems\Schemas;

use App\Filament\Support\IconUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Kontak')
                ->schema([
                    Grid::make(4)->schema([
                        TextInput::make('icon')
                            ->label('Ikon (Emoji)')
                            ->required()
                            ->default('📍')
                            ->maxLength(20)
                            ->hint('Paste emoji, mis: 📍 📞 ✉️ 💬 🕐')
                            ->columnSpan(1),

                        TextInput::make('label')
                            ->label('Judul')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Alamat, Telepon, Email…')
                            ->columnSpan(3),
                    ]),

                    TextInput::make('value')
                        ->label('Nilai / Teks Tampil')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Jl. Pendidikan No. 1, Kota Bandung')
                        ->columnSpanFull(),

                    TextInput::make('link')
                        ->label('URL Tautan (opsional)')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://maps.google.com/... atau tel:+62... atau mailto:...')
                        ->hint('Kosongkan jika tidak perlu tautan klik.')
                        ->columnSpanFull(),

                    IconUpload::make()
                        ->columnSpanFull(),
                ]),

            Section::make('Pengaturan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Tampilkan')
                            ->default(true)
                            ->onColor('success'),
                    ]),
                ]),
        ]);
    }
}
