<?php

namespace App\Models;

use Database\Factories\DownloadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    /** @use HasFactory<DownloadFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'download_count',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    // ── Computed Attributes ──────────────────────────────────

    /**
     * Human-readable file size (e.g. "1.2 MB").
     */
    public function getFileSizeLabelAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes < 1024) {
            return "{$bytes} B";
        }

        if ($bytes < 1_048_576) {
            return round($bytes / 1024, 1).' KB';
        }

        if ($bytes < 1_073_741_824) {
            return round($bytes / 1_048_576, 1).' MB';
        }

        return round($bytes / 1_073_741_824, 2).' GB';
    }

    /**
     * Icon name based on file type.
     */
    public function getFileIconAttribute(): string
    {
        return match (true) {
            str_contains($this->file_type, 'pdf') => 'pdf',
            str_contains($this->file_type, 'word') || str_contains($this->original_filename, '.doc') => 'doc',
            str_contains($this->file_type, 'excel') || str_contains($this->original_filename, '.xls') => 'xls',
            str_contains($this->file_type, 'zip') || str_contains($this->file_type, 'rar') => 'zip',
            str_contains($this->file_type, 'image') => 'img',
            default => 'file',
        };
    }

    /**
     * Public URL for downloading the file.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('downloads.download', $this->id);
    }
}
