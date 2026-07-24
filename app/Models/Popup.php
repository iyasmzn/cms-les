<?php

namespace App\Models;

use Database\Factories\PopupFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    /** @use HasFactory<PopupFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'image', 'youtube_url', 'button_label', 'button_url',
        'open_in_new_tab', 'delay_seconds', 'show_every_days',
        'width', 'is_active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'delay_seconds' => 'integer',
            'show_every_days' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public static function activeNow(): Builder
    {
        $now = now();

        return static::where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
