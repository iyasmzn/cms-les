<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactItem extends Model
{
    protected $fillable = ['icon', 'icon_image', 'label', 'value', 'link', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function active(): Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
