<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Models\Category;
use App\Models\Teacher;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Pribadi')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Drs. Ahmad Fauzi, M.Pd.'),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(30)
                            ->placeholder('197003012005011001')
                            ->hint('Nomor Induk Pegawai (kosongkan jika tidak ada).'),
                    ]),

                    FileUpload::make('photo')
                        ->label('Foto')
                        ->image()
                        ->disk('public')
                        ->directory('teachers')
                        ->visibility('public')
                        ->automaticallyCropImagesToAspectRatio('3:4')
                        ->automaticallyResizeImagesMode('cover')
                        ->automaticallyResizeImagesToWidth('300')
                        ->automaticallyResizeImagesToHeight('400')
                        ->hint('Rasio 3:4 (potret). Akan di-resize ke 300×400px.')
                        ->columnSpanFull(),
                ]),

            Section::make('Jabatan & Bidang Studi')
                ->schema([
                    Select::make('institution_id')
                        ->label('Instansi / Unit')
                        ->relationship('institution', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->placeholder('Tidak terikat unit tertentu')
                        ->hint('Opsional — kosongkan jika guru tidak terikat unit.')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Select::make('position')
                            ->label('Jabatan')
                            ->options(function (?Teacher $record): array {
                                $options = Category::optionsForType(Category::TYPE_TEACHER);

                                // Keep the record's current jabatan selectable even if it isn't
                                // (or is no longer) an active category — e.g. set via import or
                                // legacy data — so editing never silently drops it.
                                if ($record && filled($record->position) && ! isset($options[$record->position])) {
                                    $options[$record->position] = $record->position;
                                }

                                return $options;
                            })
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->hint('Kelola pilihan di menu Kategori (tipe Guru / Jabatan).'),

                        TextInput::make('subject')
                            ->label('Mata Pelajaran')
                            ->maxLength(100)
                            ->placeholder('Matematika Wajib')
                            ->hint('Kosongkan jika tidak mengajar.'),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('education')
                            ->label('Pendidikan Terakhir')
                            ->maxLength(100)
                            ->placeholder('S2 Pendidikan Matematika'),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->hint('Angka kecil tampil lebih dulu.'),
                    ]),

                    Toggle::make('is_active')
                        ->label('Aktif Mengajar')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger')
                        ->columnSpanFull(),
                ]),

            Section::make('Kontak')
                ->description('Informasi kontak yang ditampilkan di halaman profil guru.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('(022) 1234-5678'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(150)
                            ->placeholder('nama@sman1.sch.id'),
                    ]),

                    TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('6281234567890')
                        ->hint('Nomor internasional tanpa + (contoh: 6281234567890).')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
