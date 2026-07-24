<?php

namespace App\Filament\Resources\Stories\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Schemas\ContentBlocks;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class StoryForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Cerita')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Cerita')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                'slug',
                                Str::slug($state ?? ''),
                            ))
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),

                        TextInput::make('excerpt')
                            ->label('Ringkasan')
                            ->maxLength(300)
                            ->hint('Digunakan untuk preview di halaman daftar cerita.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Isi Cerita')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('stories/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Penulis / Santri')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('author_name')
                                    ->label('Nama Santri')
                                    ->required()
                                    ->maxLength(150),

                                TextInput::make('author_class')
                                    ->label('Kelas / Tingkat')
                                    ->maxLength(100)
                                    ->hint('Contoh: Kelas 3, Kelas Ulya'),

                                TextInput::make('author_year')
                                    ->label('Angkatan / Tahun')
                                    ->maxLength(10)
                                    ->hint('Contoh: 2024'),
                            ]),

                        self::imagePicker(
                            key: 'author_photo',
                            label: 'Foto Santri',
                            hint: 'Foto persegi (1:1) disarankan. Akan di-resize ke 400×400.',
                            accepted: ['image/jpeg', 'image/png', 'image/webp'],
                            width: 400,
                            height: 400,
                            directory: 'stories/photos',
                            aspectRatio: '1:1',
                        )->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gambar & Publikasi')
                    ->schema([
                        self::imagePicker(
                            key: 'image',
                            label: 'Gambar Utama Cerita',
                            hint: 'Rasio 16:9 disarankan. Akan di-resize ke 1200×675.',
                            accepted: ['image/jpeg', 'image/png', 'image/webp'],
                            width: 1200,
                            height: 675,
                            directory: 'stories/images',
                            aspectRatio: '16:9',
                        )->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Publikasikan')
                                    ->default(false)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, bool $state): void {
                                        if ($state) {
                                            $set('published_at', now()->format('Y-m-d H:i:s'));
                                        }
                                    }),

                                DateTimePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->native(false)
                                    ->seconds(false),
                            ]),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Konten Tambahan')
                    ->description('Tambahkan blok gambar opsional (cover, carousel, galeri) yang ditampilkan di bawah isi cerita.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        ContentBlocks::make('stories/blocks'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
