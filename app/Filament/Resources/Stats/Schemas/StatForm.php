<?php

namespace App\Filament\Resources\Stats\Schemas;

use App\Filament\Support\IconUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Isi Kartu Statistik')
                ->description('Data yang ditampilkan pada kartu statistik di halaman utama.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('icon')
                            ->label('Ikon (Emoji)')
                            ->required()
                            ->maxLength(20)
                            ->hint('Salin emoji dari EmojiPedia atau tekan Win + . di Windows.')
                            ->placeholder('🏆'),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->hint('Angka kecil tampil lebih dulu.'),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('value')
                            ->label('Angka / Nilai')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('200+'),

                        TextInput::make('label')
                            ->label('Label Utama')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Prestasi'),
                    ]),

                    TextInput::make('sub')
                        ->label('Keterangan Tambahan')
                        ->maxLength(150)
                        ->placeholder('Tingkat nasional')
                        ->columnSpanFull(),

                    IconUpload::make()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
