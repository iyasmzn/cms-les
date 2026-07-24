<?php

namespace App\Filament\Resources\AdmissionPaths\Schemas;

use App\Filament\Support\IconUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AdmissionPathForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Jalur Pendaftaran')
                ->description('Data jalur pendaftaran yang tampil di formulir SPMB dan halaman publik.')
                ->icon('heroicon-o-rectangle-stack')
                ->schema([
                    Grid::make(12)->schema([
                        TextInput::make('icon')
                            ->label('Ikon')
                            ->maxLength(10)
                            ->placeholder('🏡')
                            ->hint('Emoji')
                            ->columnSpan(2),

                        TextInput::make('name')
                            ->label('Nama Jalur')
                            ->required()
                            ->maxLength(60)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(6),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(60)
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatis dari nama. Hindari mengubah bila sudah dipakai pendaftar.')
                            ->columnSpan(4),
                    ]),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(2)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    Select::make('institutions')
                        ->label('Khusus Jenjang')
                        ->relationship('institutions', 'name')
                        ->multiple()
                        ->native(false)
                        ->preload()
                        ->placeholder('Semua Jenjang (bersama)')
                        ->helperText('Kosongkan agar jalur ini tersedia untuk SEMUA jenjang. Pilih satu atau beberapa jenjang untuk membatasi hanya ke jenjang tersebut.')
                        ->columnSpanFull(),

                    IconUpload::make()
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        Select::make('color')
                            ->label('Warna Badge')
                            ->options([
                                'primary' => 'Primary',
                                'info' => 'Info (biru)',
                                'success' => 'Success (hijau)',
                                'warning' => 'Warning (kuning)',
                                'danger' => 'Danger (merah)',
                                'gray' => 'Gray',
                            ])
                            ->default('gray')
                            ->required()
                            ->native(false),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->helperText('Nonaktifkan untuk menyembunyikan dari formulir pendaftaran.'),
                    ]),
                ]),
        ]);
    }
}
