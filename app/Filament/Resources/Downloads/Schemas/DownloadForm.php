<?php

namespace App\Filament\Resources\Downloads\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DownloadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Unduhan')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('description')
                            ->label('Deskripsi')
                            ->maxLength(500)
                            ->hint('Opsional. Maksimal 500 karakter.')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Kategori')
                                    ->options(fn () => Category::optionsForType(Category::TYPE_DOWNLOAD))
                                    ->native(false)
                                    ->placeholder('Pilih kategori'),

                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                    ])
                    ->columns(2),

                Section::make('File')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('File Unduhan')
                            ->required()
                            ->disk('public')
                            ->directory('downloads')
                            ->visibility('public')
                            ->storeFileNamesIn('original_filename')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/zip',
                                'application/x-rar-compressed',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->maxSize(20480)
                            ->hint('Maks. 20 MB. Format: PDF, Word, Excel, ZIP, RAR, JPG, PNG.')
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if ($state instanceof TemporaryUploadedFile) {
                                    $set('file_type', $state->getMimeType());
                                    $set('file_size', $state->getSize());
                                }
                            })
                            ->live()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('file_type')
                                    ->label('Tipe File')
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Terisi otomatis'),

                                TextInput::make('file_size')
                                    ->label('Ukuran File (bytes)')
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Terisi otomatis'),
                            ]),
                    ]),

                Section::make('Visibilitas')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Tampilkan ke Publik')
                            ->default(true),
                    ])
                    ->compact(),
            ]);
    }
}
