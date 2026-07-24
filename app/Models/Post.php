<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'content', 'image', 'blocks',
        'category', 'author', 'author_initials',
        'read_time', 'is_published', 'published_at', 'allow_comments', 'allow_questions',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_questions' => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Relations ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    // ── Computed Attributes ──────────────────────────────────

    /**
     * Thumbnail URL — falls back to picsum placeholder during development.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : "https://picsum.photos/seed/post-{$this->id}/800/500";
    }

    /** Canonical URL for SEO. */
    public function getCanonicalUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    /** 155-char meta description from excerpt or stripped content. */
    public function getMetaDescriptionAttribute(): string
    {
        $source = $this->excerpt ?: strip_tags($this->content);

        return Str::limit($source, 155);
    }

    /** Indonesian-formatted date, e.g. "24 Mei 2026". */
    public function getFormattedDateAttribute(): string
    {
        if (! $this->published_at) {
            return '-';
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
            5 => 'Mei',     6 => 'Juni',      7 => 'Juli',     8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $this->published_at->day.' '.$months[$this->published_at->month].' '.$this->published_at->year;
    }
}
