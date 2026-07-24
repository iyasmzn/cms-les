<?php

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'blocks',
        'icon', 'icon_image', 'category', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    // ── Computed Attributes ──────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : "https://picsum.photos/seed/program-{$this->id}/800/500";
    }

    public function getMetaDescriptionAttribute(): string
    {
        $source = $this->excerpt ?: strip_tags((string) $this->content);

        return Str::limit($source, 155);
    }
}
