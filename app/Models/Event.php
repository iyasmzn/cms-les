<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'blocks', 'youtube_url',
        'category', 'location', 'starts_at', 'ends_at',
        'is_published', 'sort_order',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('starts_at');
    }

    // ── Computed Attributes ──────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : "https://picsum.photos/seed/event-{$this->id}/800/500";
    }

    public function getFormattedDateAttribute(): string
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $d = $this->starts_at;

        return $d->day.' '.$months[$d->month].' '.$d->year;
    }

    public function getIsPastAttribute(): bool
    {
        return $this->starts_at->isPast();
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_url) {
            return null;
        }

        $url = $this->youtube_url;

        // youtu.be/ID
        if (preg_match('#youtu\.be/([a-zA-Z0-9_\-]{11})#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        // youtube.com/watch?v=ID or /embed/ID
        if (preg_match('#(?:v=|/embed/)([a-zA-Z0-9_\-]{11})#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return null;
    }
}
