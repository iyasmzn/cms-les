<?php

namespace App\Filament\Resources\FloatingButtons\Schemas;

use App\Filament\Support\IconUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FloatingButtonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tombol')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Hubungi Kami'),

                                TextInput::make('icon')
                                    ->label('Ikon (emoji atau teks)')
                                    ->maxLength(100)
                                    ->default('💬')
                                    ->placeholder('💬')
                                    ->hint('Emoji atau karakter singkat yang tampil di tombol.'),
                            ]),

                        IconUpload::make()
                            ->columnSpanFull(),

                        TextInput::make('url')
                            ->label('URL / Link')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://wa.me/628123456789')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                ColorPicker::make('color')
                                    ->label('Warna Tombol')
                                    ->default('#08484A'),

                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->hint('Urutan tampil (angka kecil = tampil lebih dulu).'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('open_in_new_tab')
                                    ->label('Buka di Tab Baru')
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
