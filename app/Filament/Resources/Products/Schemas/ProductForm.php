<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Produk')
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

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU / Kode Produk')
                                    ->maxLength(60),

                                TextInput::make('brand')
                                    ->label('Merek / Brand')
                                    ->maxLength(150),
                            ]),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(fn () => Category::optionsForType(Category::TYPE_PRODUCT))
                            ->searchable()
                            ->native(false),

                        Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gambar Produk')
                    ->schema([
                        self::imagePicker(
                            key: 'cover_image',
                            label: 'Gambar Utama',
                            hint: 'Rasio 1:1 disarankan. Akan di-resize ke 800×800.',
                            accepted: ['image/jpeg', 'image/png', 'image/webp'],
                            width: 800,
                            height: 800,
                            directory: 'products/covers',
                            aspectRatio: '1:1',
                        )->columnSpanFull(),

                        FileUpload::make('gallery')
                            ->label('Galeri Gambar')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->automaticallyResizeImagesToWidth('800')
                            ->hint('Bisa lebih dari satu gambar. Diurutkan dengan seret & lepas.')
                            ->columnSpanFull(),

                        self::mediaLibrarySelect('gallery_library', 'Tambah dari Media')
                            ->helperText('Gambar terpilih akan ditambahkan ke galeri saat disimpan.'),
                    ]),

                Section::make('Harga & Stok')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->prefix('Rp'),

                                TextInput::make('stock')
                                    ->label('Stok')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('weight_gram')
                                    ->label('Berat (gram)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('gram'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_available')
                                    ->label('Tersedia untuk Dibeli')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
