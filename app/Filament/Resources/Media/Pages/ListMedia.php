<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use App\Services\EmbedThumbnailService;
use App\Services\EmbedVideo;
use App\Services\MediaLibraryService;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    /** Card preview size: small | medium | large | list. Persisted per session. */
    #[Session]
    public string $cardSize = 'medium';

    /**
     * Dropdown item that switches the card preview size. The active size is
     * highlighted and disabled to make the current choice obvious.
     */
    private function cardSizeAction(string $value, string $label, Heroicon $icon): Action
    {
        return Action::make("card_size_{$value}")
            ->label($label)
            ->icon($icon)
            ->color(fn (): string => $this->cardSize === $value ? 'primary' : 'gray')
            ->action(function () use ($value): void {
                $this->cardSize = $value;

                // The table is cached at boot (before this action ran), so rebuild
                // it to apply the new card size without needing a page refresh.
                $this->table = $this->table($this->makeTable());
            });
    }

    public function getSubheading(): ?string
    {
        return 'Kelola foto, dokumen, dan video kegiatan. Aktifkan "Tampil di Galeri" agar media muncul di halaman Galeri publik.';
    }

    /**
     * Card-size selector, rendered in the table header so it sits with the
     * table's own controls instead of among the page actions.
     */
    public function cardSizeActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            $this->cardSizeAction('small', 'Kecil', Heroicon::OutlinedSquares2x2),
            $this->cardSizeAction('medium', 'Sedang', Heroicon::OutlinedViewColumns),
            $this->cardSizeAction('large', 'Besar', Heroicon::OutlinedSquare2Stack),
            $this->cardSizeAction('list', 'Daftar', Heroicon::OutlinedListBullet),
        ])
            ->label('Ukuran Kartu')
            ->icon(Heroicon::OutlinedViewfinderCircle)
            ->button()
            ->color('gray');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload File')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('primary')
                ->schema([
                    FileUpload::make('path')
                        ->label('File')
                        ->required()
                        ->disk('public')
                        ->directory('media')
                        ->visibility('public')
                        ->image()
                        ->acceptedFileTypes([
                            'image/jpeg', 'image/png', 'image/gif',
                            'image/webp', 'image/svg+xml',
                            'application/pdf',
                        ])
                        ->maxSize(10240)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set): void {
                            $file = collect(is_array($state) ? $state : [$state])->first();

                            if (! is_object($file) || ! method_exists($file, 'getClientOriginalName')) {
                                return;
                            }

                            $base = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                ->replace(['-', '_'], ' ')
                                ->squish()
                                ->title()
                                ->toString();

                            $set('name', app(MediaLibraryService::class)->uniqueName($base));
                            $set('alt', $base);
                        })
                        ->columnSpanFull(),

                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(200)
                        ->helperText('Terisi otomatis dari nama berkas; bisa diubah.'),

                    TextInput::make('alt')
                        ->label('Alt Text')
                        ->maxLength(500)
                        ->helperText('Deskripsi singkat gambar untuk SEO & aksesibilitas.'),

                    TextInput::make('album')
                        ->label('Album')
                        ->maxLength(100)
                        ->placeholder('Contoh: Wisuda 2025, Fasilitas')
                        ->helperText('Opsional. Untuk mengelompokkan media di galeri.')
                        ->datalist(fn (): array => Media::query()
                            ->whereNotNull('album')
                            ->distinct()
                            ->orderBy('album')
                            ->pluck('album')
                            ->all()),

                    Toggle::make('show_in_gallery')
                        ->label('Tampil di Galeri')
                        ->helperText('Aktif: media langsung tampil di halaman Galeri.')
                        ->default(true)
                        ->inline(false),
                ])
                ->modalHeading('Upload ke Media Library')
                ->modalDescription('Pilih file, lalu sesuaikan nama & alt bila perlu sebelum disimpan.')
                ->modalWidth('xl')
                ->action(function (array $data): void {
                    $disk = Storage::disk('public');
                    $path = is_array($data['path'] ?? null) ? collect($data['path'])->first() : ($data['path'] ?? null);

                    if (blank($path) || ! $disk->exists($path)) {
                        Notification::make()
                            ->danger()
                            ->title('File tidak ditemukan')
                            ->send();

                        return;
                    }

                    Media::create([
                        'name' => $data['name'],
                        'alt' => $data['alt'] ?? null,
                        'album' => $data['album'] ?? null,
                        'path' => $path,
                        'disk' => 'public',
                        'mime_type' => $disk->mimeType($path),
                        'size' => $disk->size($path),
                        'show_in_gallery' => $data['show_in_gallery'] ?? true,
                        'uploaded_by' => Auth::id(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('File berhasil diupload')
                        ->send();
                }),

            Action::make('add_embed')
                ->label('Tambah Embed Video')
                ->icon(Heroicon::OutlinedVideoCamera)
                ->color('gray')
                ->schema([
                    TextInput::make('name')
                        ->label('Judul')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Video Wisuda Angkatan 2025')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('alt', $state ?? '')),

                    TextInput::make('alt')
                        ->label('Alt Text')
                        ->maxLength(500)
                        ->helperText('Terisi otomatis dari judul; bisa diubah.'),

                    TextInput::make('embed_url')
                        ->label('URL Video')
                        ->required()
                        ->url()
                        ->maxLength(1000)
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->helperText('Tempel link video dari YouTube, TikTok, atau Instagram.')
                        ->live(onBlur: true)
                        ->rule(static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                            $provider = EmbedVideo::detectProvider((string) $value);

                            if ($provider === null) {
                                $fail('URL harus dari YouTube, TikTok, atau Instagram.');

                                return;
                            }

                            if (! EmbedVideo::isValid((string) $value)) {
                                $fail('Tidak dapat membaca ID video dari URL. Pastikan ini link video (bukan link profil/halaman).');
                            }
                        }),

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
                        }),

                    FileUpload::make('embed_thumbnail_path')
                        ->label('Thumbnail Manual')
                        ->image()
                        ->disk('public')
                        ->directory('media/embed-thumbnails')
                        ->visibility('public')
                        ->maxSize(5120)
                        ->helperText('Opsional. YouTube & TikTok otomatis mengambil thumbnail. Unggah gambar untuk Instagram, atau bila ingin mengganti thumbnail otomatis.'),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(2)
                        ->maxLength(1000)
                        ->placeholder('Keterangan singkat tentang video ini...'),

                    Toggle::make('show_in_gallery')
                        ->label('Tampil di Galeri')
                        ->helperText('Aktif: video langsung tampil di halaman Galeri.')
                        ->default(true)
                        ->inline(false),
                ])
                ->modalHeading('Tambah Embed Video')
                ->modalDescription('Tanpa upload file — cukup tempel link videonya.')
                ->modalWidth('xl')
                ->action(function (array $data): void {
                    $url = trim($data['embed_url']);
                    $provider = EmbedVideo::detectProvider($url);

                    // Auto-fetch the provider thumbnail (TikTok) unless one was uploaded.
                    $thumbnailPath = $data['embed_thumbnail_path'] ?? null;

                    if (blank($thumbnailPath)) {
                        $thumbnailPath = app(EmbedThumbnailService::class)->fetchAndStore($provider, $url);
                    }

                    Media::create([
                        'name' => $data['name'],
                        'alt' => $data['alt'] ?? null,
                        'description' => $data['description'] ?? null,
                        'embed_provider' => $provider,
                        'embed_url' => $url,
                        'embed_thumbnail_path' => $thumbnailPath,
                        'disk' => 'public',
                        'show_in_gallery' => $data['show_in_gallery'] ?? true,
                        'uploaded_by' => Auth::id(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Embed video ditambahkan')
                        ->send();
                }),

            Action::make('embed_help')
                ->label('Cara Embed Video')
                ->tooltip('Cara Embed Video')
                ->icon(Heroicon::OutlinedQuestionMarkCircle)
                ->color('gray')
                ->iconButton()
                ->modalHeading('Cara Embed Video YouTube, TikTok & Instagram')
                ->modalIcon(Heroicon::OutlinedVideoCamera)
                ->modalContent(fn (): View => view('filament.media.embed-help'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalWidth('2xl'),
        ];
    }
}
