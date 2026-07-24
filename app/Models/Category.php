<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** Tipe kategori — satu per fitur. */
    public const TYPE_POST = 'post';

    public const TYPE_PRODUCT = 'product';

    public const TYPE_EVENT = 'event';

    public const TYPE_PROGRAM = 'program';

    public const TYPE_DOWNLOAD = 'download';

    public const TYPE_TEACHER = 'teacher';

    /** Label tampilan per tipe (untuk UI admin). */
    public const TYPE_LABELS = [
        self::TYPE_POST => 'Blog / Berita',
        self::TYPE_PRODUCT => 'Produk Toko',
        self::TYPE_EVENT => 'Kegiatan / Event',
        self::TYPE_PROGRAM => 'Program',
        self::TYPE_DOWNLOAD => 'Unduhan',
        self::TYPE_TEACHER => 'Guru / Jabatan',
    ];

    protected $fillable = ['type', 'name', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────

    /** @param  Builder<Category>  $query */
    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /** @param  Builder<Category>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @param  Builder<Category>  $query */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Ambil daftar opsi kategori untuk Select Filament.
     * Mengembalikan ['Nama' => 'Nama'] untuk tipe yang diberikan.
     *
     * @return array<string, string>
     */
    public static function optionsForType(string $type): array
    {
        return self::forType($type)
            ->active()
            ->ordered()
            ->pluck('name', 'name')
            ->all();
    }

    /** Semua tipe yang tersedia sebagai array label. */
    public static function types(): array
    {
        return self::TYPE_LABELS;
    }
}
