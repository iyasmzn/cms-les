<?php

namespace App\Models;

use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    protected $fillable = [
        'image', 'title', 'subtitle',
        'button_label', 'button_url',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * URL gambar — storage jika ada, fallback ke placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        return 'https://picsum.photos/seed/hero-'.$this->id.'/1600/900';
    }

    /**
     * @return Builder<static>
     */
    public static function active(): Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
