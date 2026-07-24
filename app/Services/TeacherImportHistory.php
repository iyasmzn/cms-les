<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Persists teacher import runs as JSON files on the local filesystem disk —
 * deliberately NOT in the database. Entries are therefore volatile and may be
 * lost when storage is cleared, the app is redeployed, or files are pruned.
 */
class TeacherImportHistory
{
    private const DIRECTORY = 'teacher-import-history';

    /**
     * Newest entries kept on disk; older runs are pruned automatically.
     */
    private const MAX_ENTRIES = 50;

    private const DISK = 'local';

    /**
     * Persist a completed import run and return the stored entry.
     *
     * @return array<string, mixed>
     */
    public function record(TeacherImportResult $result, string $filename): array
    {
        $now = Carbon::now();
        $id = $now->format('Ymd-His').'-'.Str::lower(Str::random(6));

        $entry = [
            'id' => $id,
            'imported_at' => $now->toIso8601String(),
            'filename' => $filename,
            'created' => $result->created,
            'updated' => $result->updated,
            'failed' => $result->failed(),
            'total' => $result->total(),
            'imported' => $result->imported,
            'failures' => $result->failures,
        ];

        Storage::disk(self::DISK)->put(
            self::DIRECTORY.'/'.$id.'.json',
            (string) json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );

        $this->prune();

        return $entry;
    }

    /**
     * All stored entries, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $disk = Storage::disk(self::DISK);

        $entries = [];

        foreach ($disk->files(self::DIRECTORY) as $file) {
            if (! str_ends_with($file, '.json')) {
                continue;
            }

            $decoded = json_decode((string) $disk->get($file), true);

            if (is_array($decoded)) {
                $entries[] = $decoded;
            }
        }

        usort($entries, fn (array $a, array $b): int => ($b['imported_at'] ?? '') <=> ($a['imported_at'] ?? ''));

        return $entries;
    }

    public function clear(): void
    {
        Storage::disk(self::DISK)->deleteDirectory(self::DIRECTORY);
    }

    private function prune(): void
    {
        $disk = Storage::disk(self::DISK);

        collect($disk->files(self::DIRECTORY))
            ->filter(fn (string $file): bool => str_ends_with($file, '.json'))
            ->sortDesc()
            ->slice(self::MAX_ENTRIES)
            ->each(fn (string $file) => $disk->delete($file));
    }
}
