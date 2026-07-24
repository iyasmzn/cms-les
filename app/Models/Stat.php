<?php

namespace App\Models;

use Database\Factories\StatFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    /** @use HasFactory<StatFactory> */
    use HasFactory;

    protected $fillable = ['icon', 'icon_image', 'label', 'value', 'sub', 'sort_order'];

    /**
     * @return Builder<static>
     */
    public static function ordered(): Builder
    {
        return static::orderBy('sort_order')->orderBy('id');
    }
}
