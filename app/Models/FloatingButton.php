<?php

namespace App\Models;

use Database\Factories\FloatingButtonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloatingButton extends Model
{
    /** @use HasFactory<FloatingButtonFactory> */
    use HasFactory;

    protected $fillable = [
        'label', 'url', 'icon', 'icon_image', 'color',
        'open_in_new_tab', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function active(): Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
