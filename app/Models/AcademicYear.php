<?php

namespace App\Models;

use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    /** @use HasFactory<AcademicYearFactory> */
    use HasFactory;

    protected $fillable = [
        'year_start',
        'year_end',
        'is_active',
    ];

    protected $casts = [
        'year_start' => 'integer',
        'year_end' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $year): void {
            if ($year->is_active) {
                static::query()
                    ->whereKeyNot($year->getKey())
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    /** @return HasMany<RegistrationWave, $this> */
    public function waves(): HasMany
    {
        return $this->hasMany(RegistrationWave::class);
    }

    /** @return HasMany<SpmbRegistration, $this> */
    public function registrations(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class);
    }

    /**
     * Human readable label, e.g. "2026/2027".
     *
     * @return Attribute<string, never>
     */
    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn (): string => "{$this->year_start}/{$this->year_end}",
        );
    }

    /**
     * The single active academic year, if any.
     */
    public static function active(): ?self
    {
        return static::query()->where('is_active', true)->first();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
