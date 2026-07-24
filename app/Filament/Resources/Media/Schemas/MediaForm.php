<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Services\EmbedVideo;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('embed_provider'),

            Section::make('File')
                ->visible(fn (Get $get): bool => blank($get('embed_provider')))
                ->schema([
                    FileUpload::make('path')
                        ->label('File')
                        ->disk('public')
                        ->directory('media')
                        ->visibility('public')
                        ->image()
                        ->acceptedFileTypes([
                            'image/jpeg', 'image/png', 'image/gif',
                            'image/webp', 'image/svg+xml',
                            'application/pdf',
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Embed Video')
                ->description(fn (Get $get): string => 'Sumber: '.EmbedVideo::label($get('embed_provider')))
                ->icon(Heroicon::OutlinedVideoCamera)
                ->visible(fn (Get $get): bool => filled($get('embed_provider')))
                ->schema([
                    TextInput::make('embed_url')
                        ->label('URL Video')
                        ->required()
                        ->url()
                        ->maxLength(1000)
                        ->helperText('Link video YouTube, TikTok, atau Instagram.')
                        ->live(onBlur: true)
                        ->rule(static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                            if (EmbedVideo::detectProvider((string) $value) === null) {
                                $fail('URL harus dari YouTube, TikTok, atau Instagram.');

                                return;
                            }

                            if (! EmbedVideo::isValid((string) $value)) {
                                $fail('Tidak dapat membaca ID video dari URL.');
                            }
                        })
                        ->columnSpanFull(),

                    Placeholder::make('embed_preview')
                        ->label('Pratinjau')
                        ->visible(function (Get $get): bool {
                            $url = (string) $get('embed_url');

                            return filled($url)
                                && EmbedVideo::detectProvider($url) !== null
                                && EmbedVideo::isValid($url);
                        })
                        ->content(function (Get $get): ?HtmlString {
                            $url = (string) $get('embed_url');
                            $provider = EmbedVideo::detectProvider($url);

                            if ($provider === null || ! EmbedVideo::isValid($url)) {
                                return null;
                            }

                            $thumbnail = EmbedVideo::thumbnail($provider, $url);

                            if (blank($thumbnail)) {
                                return null;
                            }

                            return new HtmlString(
                                '<img src="'.e($thumbnail).'" alt="Pratinjau video" '
                                .'style="max-height:180px;max-width:100%;border-radius:.75rem;border:1px solid rgba(0,0,0,.1);object-fit:cover;" />'
                            );
                        })
                        ->columnSpanFull(),

                    FileUpload::make('embed_thumbnail_path')
                        ->label('Thumbnail Manual')
                        ->image()
                        ->disk('public')
                        ->directory('media/embed-thumbnails')
                        ->visibility('public')
                        ->maxSize(5120)
                        ->helperText('Opsional. YouTube & TikTok otomatis mengambil thumbnail. Unggah gambar untuk Instagram, atau bila ingin mengganti thumbnail otomatis.')
                        ->columnSpanFull(),
                ]),

            Section::make('Galeri Publik')
                ->description('Tampilkan media ini di halaman Galeri website.')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Toggle::make('show_in_gallery')
                                ->label('Tampilkan di Galeri')
                                ->helperText('Aktifkan untuk menampilkan gambar atau video ini di halaman Galeri.')
                                ->inline(false),

                            TextInput::make('album')
                                ->label('Album')
                                ->maxLength(100)
                                ->placeholder('Contoh: Wisuda 2025, Fasilitas')
                                ->hint('Opsional. Digunakan untuk mengelompokkan media di galeri.'),
                        ]),
                ]),

            Section::make('Informasi SEO')
                ->description('Metadata yang membantu mesin pencari dan aksesibilitas.')
                ->icon(Heroicon::OutlinedMagnifyingGlass)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama File')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('foto-wisuda-2025')
                        ->hint('Nama deskriptif tanpa ekstensi. Digunakan sebagai title SEO.'),

                    TextInput::make('alt')
                        ->label('Alt Text')
                        ->maxLength(500)
                        ->placeholder('Foto wisuda angkatan 2025 di aula pesantren')
                        ->hint('Deskripsi singkat gambar. Wajib untuk aksesibilitas dan SEO.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->maxLength(1000)
                        ->placeholder('Keterangan lengkap tentang gambar atau file ini...')
                        ->hint('Opsional. Konteks tambahan untuk kebutuhan SEO atau catatan internal.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
