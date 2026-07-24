<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id', 'user_id', 'name', 'email', 'is_anonymous', 'question',
        'answer', 'is_answered', 'is_published', 'answered_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_answered' => 'boolean',
        'is_published' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeAnswered(Builder $query): Builder
    {
        return $query->where('is_answered', true);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->whereNull('post_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Computed Attributes ──────────────────────────────────

    /** Public display name — "Anonim" when the asker opted to hide it. */
    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Anonim' : $this->name;
    }
}
