<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id', 'user_id', 'guest_name', 'body',
        'admin_reply', 'replied_at', 'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'replied_at' => 'datetime',
    ];

    // ── Relations ───────────────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    // ── Computed Attributes ──────────────────────────────────

    /** Whether this comment was left by a guest (no linked account). */
    public function getIsGuestAttribute(): bool
    {
        return $this->user_id === null;
    }

    /** Display name — the account name for members, or the typed guest name. */
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Tamu';
    }

    /** Avatar URL — the member avatar, or a generated initial avatar for guests. */
    public function getAuthorAvatarUrlAttribute(): string
    {
        return $this->user?->avatar_url
            ?? 'https://ui-avatars.com/api/?name='.urlencode($this->author_name).'&background=08484A&color=fff&size=200&bold=true';
    }
}
