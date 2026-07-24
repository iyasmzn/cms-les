<?php

namespace App\Models;

use Database\Factories\PpdbFieldFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbField extends Model
{
    /** @use HasFactory<PpdbFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'key',
        'label',
        'type',
        'options',
        'placeholder',
        'help_text',
        'is_required',
        'width',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** @return array<string, string> */
    public static function typeOptions(): array
    {
        return [
            'text' => 'Teks singkat',
            'textarea' => 'Teks panjang',
            'number' => 'Angka',
            'email' => 'Email',
            'tel' => 'Nomor telepon',
            'date' => 'Tanggal',
            'select' => 'Dropdown pilihan',
            'radio' => 'Pilihan (radio)',
            'file' => 'Unggah berkas',
        ];
    }

    /**
     * Field types that carry a fixed set of options.
     */
    public function hasChoices(): bool
    {
        return in_array($this->type, ['select', 'radio'], true);
    }

    /**
     * Flatten the stored options into a list of string values. Options are
     * stored as an array of `['value' => '...']` rows from the admin repeater.
     *
     * @return array<int, string>
     */
    public function optionValues(): array
    {
        return collect($this->options ?? [])
            ->map(fn ($option): string => is_array($option) ? (string) ($option['value'] ?? '') : (string) $option)
            ->filter(fn (string $value): bool => $value !== '')
            ->values()
            ->all();
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
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
