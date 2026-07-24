<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Services\EmbedThumbnailService;
use App\Services\EmbedVideo;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (): void {
                    if (filled($this->record->path)) {
                        Storage::disk($this->record->disk)->delete($this->record->path);
                    }
                    if (filled($this->record->embed_thumbnail_path)) {
                        Storage::disk($this->record->disk)->delete($this->record->embed_thumbnail_path);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Embed items: keep the provider in sync with the (possibly changed) URL.
        if (filled($data['embed_url'] ?? null)) {
            $data['embed_url'] = trim($data['embed_url']);
            $data['embed_provider'] = EmbedVideo::detectProvider($data['embed_url']);

            // Auto-fetch the provider thumbnail (TikTok) when none is set. A manual
            // upload takes precedence; clear it to re-fetch from the provider.
            if (blank($data['embed_thumbnail_path'] ?? null)) {
                $data['embed_thumbnail_path'] = app(EmbedThumbnailService::class)
                    ->fetchAndStore($data['embed_provider'], $data['embed_url']);
            }

            return $data;
        }

        // If a new file was uploaded, update size and mime_type
        if (isset($data['path']) && $data['path'] !== $this->record->path) {
            $disk = Storage::disk($data['disk'] ?? 'public');

            if ($disk->exists($data['path'])) {
                $data['size'] = $disk->size($data['path']);
                $data['mime_type'] = $disk->mimeType($data['path']);
                // Delete old file
                $disk->delete($this->record->path);
            }
        }

        return $data;
    }
}
