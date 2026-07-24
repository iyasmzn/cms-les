<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Filament\Schemas\ContentBlocks;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kegiatan')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Kegiatan')
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
                            ->hint('Maksimal 300 karakter.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Deskripsi Lengkap')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('events/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gambar & Kategori')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar Kegiatan')
                            ->image()
                            ->disk('public')
                            ->directory('events/images')
                            ->visibility('public')
                            ->automaticallyCropImagesToAspectRatio('16:9')
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyResizeImagesToWidth('1200')
                            ->automaticallyResizeImagesToHeight('675')
                            ->columnSpanFull(),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(fn () => Category::optionsForType(Category::TYPE_EVENT))
                            ->native(false),

                        TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),

                        TextInput::make('youtube_url')
                            ->label('Link YouTube')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->maxLength(500)
                            ->hint('Opsional – video akan ditampilkan di halaman kegiatan.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Konten Tambahan')
                    ->description('Tambahkan blok gambar opsional yang ditampilkan di bawah konten utama.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        ContentBlocks::make('events/blocks'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Waktu & Publikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Mulai')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false),

                                DateTimePicker::make('ends_at')
                                    ->label('Selesai')
                                    ->native(false)
                                    ->seconds(false)
                                    ->after('starts_at'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Publikasikan')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
