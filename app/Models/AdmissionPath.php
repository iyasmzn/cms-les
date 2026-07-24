<?php

namespace App\Models;

use Database\Factories\AdmissionPathFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionPath extends Model
{
    /** @use HasFactory<AdmissionPathFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'icon_image',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** @return BelongsToMany<Institution, $this> */
    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    /** @return HasMany<SpmbRegistration, $this> */
    public function registrations(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class);
    }

    /**
     * Paths available to a given jenjang: those attached to it plus shared
     * paths (no jenjang attached at all). Passing null returns only shared paths.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForInstitution(Builder $query, ?Institution $institution): Builder
    {
        return $query->where(function (Builder $query) use ($institution): void {
            $query->whereDoesntHave('institutions');

            if ($institution !== null) {
                $query->orWhereHas('institutions', function (Builder $subQuery) use ($institution): void {
                    $subQuery->whereKey($institution->id);
                });
            }
        });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
