<?php

namespace App\Filament\Resources\Institutions\Schemas;

use App\Filament\Support\IconUpload;
use App\Models\Institution;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Jenjang / Unit Pendidikan')
                ->description('Setiap jenjang menjalankan PPDB-nya sendiri: gelombang, jadwal, jalur, dan pendaftar.')
                ->icon('heroicon-o-building-library')
                ->schema([
                    Grid::make(12)->schema([
                        TextInput::make('icon')
                            ->label('Ikon')
                            ->maxLength(10)
                            ->placeholder('🎓')
                            ->hint('Emoji')
                            ->columnSpan(2),

                        TextInput::make('name')
                            ->label('Nama Jenjang')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(7),

                        TextInput::make('short_name')
                            ->label('Singkatan')
                            ->maxLength(20)
                            ->placeholder('SMA')
                            ->columnSpan(3),
                    ]),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true)
                        ->helperText('Dipakai di URL halaman PPDB (/ppdb/slug). Hindari mengubah bila sudah dipublikasikan.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(2)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    TextInput::make('detail_url')
                        ->label('URL / Path Halaman Detail')
                        ->maxLength(500)
                        ->placeholder('https://sekolah.sch.id/profil/sma  atau  /profil/sma')
                        ->helperText('Opsional. Tautan halaman detail/profil jenjang agar calon pendaftar bisa melihat informasi lengkap sebelum mendaftar. Isi URL lengkap (https://...) atau path internal diawali garis miring (/...).')
                        ->columnSpanFull(),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(2)
                        ->maxLength(255)
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
                            ->default('primary')
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
                            ->helperText('Nonaktifkan untuk menyembunyikan jenjang dari halaman PPDB publik.'),
                    ]),
                ]),

            Section::make('Mode Formulir Pendaftaran')
                ->description('Cara calon peserta jenjang ini mendaftar. Untuk mode eksternal/embed, data pendaftaran TIDAK disimpan di sistem ini.')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->schema([
                    Select::make('form_mode')
                        ->label('Mode Formulir')
                        ->options(Institution::formModeOptions())
                        ->default(Institution::FORM_MODE_INTERNAL)
                        ->required()
                        ->native(false)
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('external_url')
                        ->label('URL Pendaftaran Eksternal')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://ppdb.sekolahlain.sch.id')
                        ->helperText('Tombol "Daftar" akan mengarah ke tautan ini (dibuka di tab baru).')
                        ->required(fn (Get $get): bool => $get('form_mode') === Institution::FORM_MODE_EXTERNAL_LINK)
                        ->visible(fn (Get $get): bool => $get('form_mode') === Institution::FORM_MODE_EXTERNAL_LINK)
                        ->columnSpanFull(),

                    TextInput::make('embed_url')
                        ->label('URL Formulir untuk Disematkan')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://docs.google.com/forms/d/e/.../viewform?embedded=true')
                        ->helperText('Formulir ditampilkan dalam bingkai (iframe). Google Form: Kirim → Sematkan < > → salin nilai src.')
                        ->required(fn (Get $get): bool => $get('form_mode') === Institution::FORM_MODE_EMBED)
                        ->visible(fn (Get $get): bool => $get('form_mode') === Institution::FORM_MODE_EMBED)
                        ->columnSpanFull(),
                ]),

            Section::make('Konten Halaman PPDB')
                ->description('Khusus jenjang ini. Kosongkan sebuah bagian untuk memakai pengaturan global (Pengaturan PPDB).')
                ->icon('heroicon-o-document-text')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextInput::make('form_title')
                        ->label('Judul Formulir')
                        ->maxLength(120)
                        ->placeholder('Formulir Pendaftaran SPMB')
                        ->columnSpanFull(),

                    Textarea::make('form_description')
                        ->label('Deskripsi / Petunjuk Formulir')
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('closed_message')
                        ->label('Pesan saat Pendaftaran Ditutup')
                        ->rows(2)
                        ->columnSpanFull(),

                    Repeater::make('procedures')
                        ->label('Prosedur Pendaftaran')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('icon')
                                    ->label('Ikon')
                                    ->maxLength(10)
                                    ->placeholder('📝')
                                    ->columnSpan(2),

                                TextInput::make('title')
                                    ->label('Judul Langkah')
                                    ->required()
                                    ->maxLength(60)
                                    ->columnSpan(10),

                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->columnSpanFull(),
                            ]),
                        ])
                        ->addActionLabel('+ Tambah Langkah')
                        ->reorderable()
                        ->reorderableWithDragAndDrop()
                        ->maxItems(10)
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): string => trim(($state['icon'] ?? '').' '.($state['title'] ?? 'Langkah')))
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull(),

                    Repeater::make('fees')
                        ->label('Biaya Pendaftaran')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('category')
                                    ->label('Kategori')
                                    ->required()
                                    ->maxLength(60)
                                    ->columnSpan(4),

                                TextInput::make('amount')
                                    ->label('Jumlah')
                                    ->required()
                                    ->maxLength(30)
                                    ->columnSpan(3),

                                TextInput::make('note')
                                    ->label('Keterangan')
                                    ->maxLength(100)
                                    ->columnSpan(5),
                            ]),
                        ])
                        ->addActionLabel('+ Tambah Biaya')
                        ->reorderable()
                        ->reorderableWithDragAndDrop()
                        ->maxItems(15)
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): string => trim(($state['category'] ?? 'Item').' — '.($state['amount'] ?? '')))
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
