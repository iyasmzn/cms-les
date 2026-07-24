<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Schemas\ContentBlocks;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
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

class PostForm
{
    use InteractsWithImagePicker;

    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $canPublish = (bool) $user?->can('Publish:Post');

        return $schema
            ->components([
                Section::make('Konten')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
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
                            ->hint('Maksimal 300 karakter. Digunakan untuk SEO dan preview.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Konten Artikel')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Media & Kategori')
                    ->schema([
                        self::imagePicker(
                            key: 'image',
                            label: 'Gambar Utama',
                            hint: 'Rasio 16:9 disarankan. Akan di-resize ke 1200×675.',
                            accepted: ['image/jpeg', 'image/png', 'image/webp'],
                            width: 1200,
                            height: 675,
                            directory: 'posts/images',
                            aspectRatio: '16:9',
                        )->columnSpanFull(),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(fn () => Category::optionsForType(Category::TYPE_POST))
                            ->required()
                            ->default('Berita')
                            ->native(false),

                        TextInput::make('read_time')
                            ->label('Estimasi Baca (menit)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->default(3),
                    ])
                    ->columns(2),

                Section::make('Konten Tambahan')
                    ->description('Tambahkan blok gambar opsional yang ditampilkan di bawah konten utama.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        ContentBlocks::make('posts/blocks'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Penulis & Publikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('author')
                                    ->label('Nama Penulis')
                                    ->required()
                                    ->default($canPublish ? 'Admin' : ($user?->name ?? 'Admin'))
                                    ->readOnly(! $canPublish),

                                TextInput::make('author_initials')
                                    ->label('Inisial Penulis')
                                    ->required()
                                    ->default($canPublish ? 'AD' : self::initialsFor($user?->name))
                                    ->maxLength(3)
                                    ->hint('Contoh: AF, SR, BS')
                                    ->readOnly(! $canPublish),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Publikasikan')
                                    ->default(false)
                                    ->live()
                                    ->visible($canPublish)
                                    ->dehydrated($canPublish)
                                    ->afterStateUpdated(function (Set $set, bool $state): void {
                                        if ($state) {
                                            $set('published_at', now()->format('Y-m-d H:i:s'));
                                        }
                                    }),

                                DateTimePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->native(false)
                                    ->seconds(false)
                                    ->visible($canPublish)
                                    ->dehydrated($canPublish),
                            ])
                            ->visible($canPublish),

                        Toggle::make('allow_comments')
                            ->label('Izinkan Komentar')
                            ->default(true)
                            ->helperText('Jika dinonaktifkan, pengunjung tidak dapat mengirim komentar pada artikel ini.'),
                    ])
                    ->description($canPublish ? null : 'Tulisan Anda akan berstatus draft dan menunggu publikasi oleh admin.'),
            ]);
    }

    /** Build up-to-3-char uppercase initials from a name for the author badge. */
    protected static function initialsFor(?string $name): string
    {
        $words = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $initials = collect($words)
            ->take(3)
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'AD';
    }
}
