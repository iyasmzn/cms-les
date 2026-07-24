<?php

namespace App\Filament\Resources\Media\Tables;

use App\Models\Media;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;
use Livewire\Component;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        $size = $table->getLivewire()->cardSize ?? 'medium';

        [$grid, $imageHeight] = match ($size) {
            'small' => [['sm' => 3, 'md' => 4, 'xl' => 6, '2xl' => 8], 110],
            'large' => [['default' => 1, 'md' => 2, 'xl' => 3], 260],
            'list' => [['default' => 1], 0],
            default => [['sm' => 2, 'md' => 3, 'xl' => 4, '2xl' => 5], 160],
        };

        $record = $size === 'list'
            ? static::listLayout()
            : static::gridLayout($imageHeight);

        return $table
            ->columns([$record])
            ->contentGrid($grid)
            ->defaultSort('created_at', 'desc')
            ->paginated([12, 24, 48, 96])
            ->searchable()
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe File')
                    ->options([
                        'image' => '🖼️  Gambar',
                        'pdf' => '📄  PDF',
                        'embed' => '🎬  Video Embed',
                        'other' => '📎  Lainnya',
                    ])
                    ->query(function ($query, array $data): void {
                        if (blank($data['value'])) {
                            return;
                        }
                        match ($data['value']) {
                            'image' => $query->where('mime_type', 'like', 'image/%'),
                            'pdf' => $query->where('mime_type', 'application/pdf'),
                            'embed' => $query->whereNotNull('embed_provider'),
                            'other' => $query->whereNull('embed_provider')
                                ->where('mime_type', 'not like', 'image/%')
                                ->where('mime_type', '!=', 'application/pdf'),
                        };
                    }),

                SelectFilter::make('origin')
                    ->label('Folder Asal')
                    ->options(fn (): array => Media::originOptions())
                    ->query(function ($query, array $data): void {
                        if (blank($data['value'])) {
                            return;
                        }
                        $query->fromOrigin($data['value']);
                    }),

                SelectFilter::make('album')
                    ->label('Album')
                    ->options(fn (): array => Media::query()
                        ->whereNotNull('album')
                        ->distinct()
                        ->orderBy('album')
                        ->pluck('album', 'album')
                        ->all()),

                TernaryFilter::make('show_in_gallery')
                    ->label('Status Publikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Dipublikasikan')
                    ->falseLabel('Disembunyikan'),
            ])
            ->recordActions([
                Action::make('copy_url')
                    ->label('Salin URL')
                    ->icon(Heroicon::OutlinedClipboard)
                    ->color('gray')
                    ->action(function (Media $record, Component $livewire): void {
                        // Filament strips event handlers from action extraAttributes, so
                        // the copy runs through Livewire's JS bridge after the click.
                        $url = Js::from($record->url);
                        $livewire->js(<<<JS
                            (() => {
                                const v = {$url};
                                if (navigator.clipboard && window.isSecureContext) {
                                    navigator.clipboard.writeText(v);
                                } else {
                                    const t = document.createElement('textarea');
                                    t.value = v;
                                    t.style.position = 'fixed';
                                    t.style.left = '-9999px';
                                    document.body.appendChild(t);
                                    t.focus();
                                    t.select();
                                    try { document.execCommand('copy'); } catch (e) {}
                                    document.body.removeChild(t);
                                }
                            })()
                        JS);

                        Notification::make()
                            ->title('URL disalin ke clipboard')
                            ->body($record->url)
                            ->success()
                            ->send();
                    }),
            ])
            // Card-size selector + bulk actions share the toolbar (left of search
            // & filters). They must live in a single toolbarActions() call —
            // the deprecated bulkActions() would overwrite anything set before it.
            ->toolbarActions([
                $table->getLivewire()->cardSizeActionGroup(),

                BulkAction::make('publish_selected')
                    ->label('Tampilkan di Galeri')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->action(fn (Collection $records) => static::setGalleryVisibility($records, true))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('unpublish_selected')
                    ->label('Sembunyikan dari Galeri')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('gray')
                    ->action(fn (Collection $records) => static::setGalleryVisibility($records, false))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('delete_selected')
                    ->label('Hapus Terpilih')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            if (filled($record->path)) {
                                Storage::disk($record->disk)->delete($record->path);
                            }
                            if (filled($record->embed_thumbnail_path)) {
                                Storage::disk($record->disk)->delete($record->embed_thumbnail_path);
                            }
                            $record->delete();
                        }

                        Notification::make()
                            ->success()
                            ->title('File berhasil dihapus')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    /**
     * Toggle the gallery (published) visibility for the selected media.
     *
     * @param  Collection<int, Media>  $records
     */
    private static function setGalleryVisibility(Collection $records, bool $visible): void
    {
        Media::query()->whereKey($records->modelKeys())->update(['show_in_gallery' => $visible]);

        Notification::make()
            ->success()
            ->title($visible ? 'Media ditampilkan di galeri' : 'Media disembunyikan dari galeri')
            ->send();
    }

    /**
     * Vertical card layout (small / medium / large) — thumbnail on top, meta below.
     */
    private static function gridLayout(int $imageHeight): Stack
    {
        return Stack::make([
            ImageColumn::make('path')
                ->label('')
                ->disk('public')
                ->height($imageHeight)
                ->width('100%')
                ->extraAttributes(['style' => "width:100%;height:{$imageHeight}px;display:block;"])
                ->extraImgAttributes(['style' => 'width:100%;height:100%;object-fit:cover;display:block;'])
                ->defaultImageUrl(static::thumbnailFallback()),

            Stack::make(static::metaColumns())
                ->space(1)
                ->extraAttributes(['class' => 'px-3 py-2.5 min-w-0 overflow-hidden']),
        ])
            ->extraAttributes(['class' => 'overflow-hidden']);
    }

    /**
     * Horizontal row layout (list) — small thumbnail on the left, meta on the right.
     */
    private static function listLayout(): Split
    {
        return Split::make([
            ImageColumn::make('path')
                ->label('')
                ->disk('public')
                ->height(72)
                ->width(72)
                ->grow(false)
                ->extraImgAttributes(['style' => 'width:72px;height:72px;object-fit:cover;border-radius:.5rem;display:block;'])
                ->defaultImageUrl(static::thumbnailFallback()),

            Stack::make(static::metaColumns())
                ->space(1)
                ->extraAttributes(['class' => 'min-w-0 overflow-hidden']),
        ])->extraAttributes(['class' => 'items-center gap-3 px-3 py-2']);
    }

    /**
     * Meta lines shared by every layout: name, origin badge, date + size.
     *
     * @return array<int, TextColumn>
     */
    private static function metaColumns(): array
    {
        return [
            TextColumn::make('name')
                ->weight(FontWeight::SemiBold)
                ->size(TextSize::Small)
                ->limit(40)
                ->searchable()
                ->extraAttributes(['style' => 'overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;display:block;']),

            TextColumn::make('origin')
                ->state(fn (Media $record): string => $record->getOriginLabel())
                ->badge()
                ->color('gray')
                ->icon(Heroicon::OutlinedFolder)
                ->size(TextSize::ExtraSmall),

            TextColumn::make('album')
                ->badge()
                ->color('info')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->size(TextSize::ExtraSmall)
                ->placeholder(''),

            TextColumn::make('created_at')
                ->state(function (Media $record): string {
                    $date = $record->created_at?->translatedFormat('d M Y') ?? '';

                    return $record->is_embed
                        ? $date
                        : trim($date.' · '.$record->size_formatted, ' ·');
                })
                ->icon(Heroicon::OutlinedCalendar)
                ->color('gray')
                ->size(TextSize::ExtraSmall),

            TextColumn::make('show_in_gallery')
                ->state(fn (Media $record): string => $record->show_in_gallery ? 'Dipublikasikan' : 'Disembunyikan')
                ->badge()
                ->color(fn (Media $record): string => $record->show_in_gallery ? 'success' : 'gray')
                ->icon(fn (Media $record): Heroicon => $record->show_in_gallery ? Heroicon::OutlinedEye : Heroicon::OutlinedEyeSlash)
                ->size(TextSize::ExtraSmall),
        ];
    }

    /**
     * Fallback thumbnail for embeds (provider thumbnail) and non-image files
     * (a document glyph). Images fall through to the stored file.
     */
    private static function thumbnailFallback(): callable
    {
        return function (Media $record): string {
            if ($record->is_embed) {
                return $record->embed_thumbnail ?? '';
            }

            return $record->is_image
                ? ''
                : 'data:image/svg+xml,'.rawurlencode(
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="#d97706">'
                    .'<rect width="80" height="80" rx="8" fill="#fffbeb"/>'
                    .'<text x="40" y="50" font-size="32" text-anchor="middle">📄</text>'
                    .'</svg>'
                );
        };
    }
}
