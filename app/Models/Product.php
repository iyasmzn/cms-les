<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'sku', 'brand', 'category', 'description',
        'cover_image', 'gallery', 'price', 'stock', 'weight_gram',
        'is_available', 'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'gallery' => 'array',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    // ── Computed Attributes ──────────────────────────────────

    public function getCoverUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/'.$this->cover_image)
            : "https://picsum.photos/seed/product-{$this->id}/600/600";
    }

    /**
     * Public URLs for every gallery image, falling back to the cover.
     *
     * @return list<string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $urls = collect($this->gallery ?? [])
            ->filter()
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->values()
            ->all();

        return $urls !== [] ? $urls : [$this->cover_url];
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
