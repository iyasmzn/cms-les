<?php

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name', 'nip', 'position', 'subject',
        'education', 'phone', 'email', 'whatsapp',
        'photo', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Instansi / unit tempat guru bertugas (opsional).
     *
     * @return BelongsTo<Institution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * URL foto guru — storage jika ada, fallback ke generated avatar.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/'.$this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=d97706&color=fff&size=300&bold=true';
    }

    /**
     * @return Builder<static>
     */
    public static function active(): Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
