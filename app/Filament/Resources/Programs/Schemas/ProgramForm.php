<?php

namespace App\Filament\Resources\Programs\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Schemas\ContentBlocks;
use App\Filament\Support\IconUpload;
use App\Models\Category;
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

class ProgramForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Program')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Program')
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
                            ->hint('Digunakan untuk tampilan card di halaman depan.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Konten Detail')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('programs/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Media & Kategori')
                    ->schema([
                        self::imagePicker(
                            key: 'image',
                            label: 'Gambar Program',
                            hint: 'Rasio 16:9. Akan di-resize ke 1200×675.',
                            accepted: ['image/jpeg', 'image/png', 'image/webp'],
                            width: 1200,
                            height: 675,
                            directory: 'programs/images',
                            aspectRatio: '16:9',
                        )->columnSpanFull(),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(fn () => Category::optionsForType(Category::TYPE_PROGRAM))
                            ->native(false),

                        TextInput::make('icon')
                            ->label('Emoji / Ikon')
                            ->hint('Gunakan emoji, contoh: 📖, 🕌, 🎓')
                            ->maxLength(10),

                        IconUpload::make()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Konten Tambahan')
                    ->description('Tambahkan blok gambar opsional yang ditampilkan di bawah konten utama.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        ContentBlocks::make('programs/blocks'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Pengaturan Tampil')
                    ->schema([
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
