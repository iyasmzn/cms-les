<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'class_year',
        'graduation_year',
        'message',
        'photo',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /**
     * URL foto alumni — storage jika ada, fallback ke generated avatar.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/'.$this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=3b82f6&color=fff&size=300&bold=true';
    }

    /**
     * @return Builder<static>
     */
    public static function published(): Builder
    {
        return static::where('is_published', true)->orderBy('sort_order')->orderBy('name');
    }
}
