<?php

namespace App\Models;

use Database\Factories\StoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Story extends Model
{
    /** @use HasFactory<StoryFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'author_name', 'author_class', 'author_year',
        'author_photo', 'excerpt', 'content', 'image', 'blocks',
        'is_published', 'published_at', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'blocks' => 'array',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->latest('published_at');
    }

    // ── Computed Attributes ──────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : "https://picsum.photos/seed/story-{$this->id}/800/500";
    }

    public function getAuthorPhotoUrlAttribute(): string
    {
        return $this->author_photo
            ? asset('storage/'.$this->author_photo)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->author_name).'&background=08484A&color=fff';
    }

    public function getFormattedDateAttribute(): string
    {
        if (! $this->published_at) {
            return '-';
        }
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $this->published_at->day.' '.$months[$this->published_at->month].' '.$this->published_at->year;
    }

    public function getMetaDescriptionAttribute(): string
    {
        $source = $this->excerpt ?: strip_tags((string) $this->content);

        return Str::limit($source, 155);
    }
}
