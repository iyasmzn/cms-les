<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pertanyaan dari Publik')
                    ->schema([
                        Select::make('post_id')
                            ->label('Terkait Artikel')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('— Pertanyaan umum —')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Penanya')
                                    ->required()
                                    ->maxLength(150),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(200),
                            ]),

                        Toggle::make('is_anonymous')
                            ->label('Ditampilkan sebagai Anonim')
                            ->helperText('Jika aktif, nama penanya disembunyikan (tampil "Anonim") di halaman publik.'),

                        Textarea::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Jawaban Admin')
                    ->schema([
                        Textarea::make('answer')
                            ->label('Jawaban')
                            ->rows(5)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_answered')
                                    ->label('Sudah Dijawab')
                                    ->default(false)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, bool $state): void {
                                        if ($state) {
                                            $set('answered_at', now()->format('Y-m-d H:i:s'));
                                        } else {
                                            $set('answered_at', null);
                                        }
                                    }),

                                DateTimePicker::make('answered_at')
                                    ->label('Tanggal Dijawab')
                                    ->native(false)
                                    ->seconds(false),
                            ]),

                        Toggle::make('is_published')
                            ->label('Tampilkan ke Publik')
                            ->default(true)
                            ->helperText('Pertanyaan dan jawaban ini akan ditampilkan di halaman Tanya Jawab.'),
                    ]),
            ]);
    }
}
