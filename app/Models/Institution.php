<?php

namespace App\Models;

use Database\Factories\InstitutionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    /** @use HasFactory<InstitutionFactory> */
    use HasFactory;

    public const FORM_MODE_INTERNAL = 'internal';

    public const FORM_MODE_EXTERNAL_LINK = 'external_link';

    public const FORM_MODE_EMBED = 'embed';

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'icon',
        'icon_image',
        'color',
        'description',
        'detail_url',
        'address',
        'sort_order',
        'is_active',
        'form_mode',
        'external_url',
        'embed_url',
        'procedures',
        'fees',
        'form_title',
        'form_description',
        'closed_message',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'procedures' => 'array',
        'fees' => 'array',
    ];

    /** @return HasMany<Teacher, $this> */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
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

    /** @return BelongsToMany<AdmissionPath, $this> */
    public function admissionPaths(): BelongsToMany
    {
        return $this->belongsToMany(AdmissionPath::class);
    }

    /** @return HasMany<PpdbField, $this> */
    public function ppdbFields(): HasMany
    {
        return $this->hasMany(PpdbField::class);
    }

    /** @return array<string, string> */
    public static function formModeOptions(): array
    {
        return [
            self::FORM_MODE_INTERNAL => 'Formulir internal (data tersimpan di sistem)',
            self::FORM_MODE_EXTERNAL_LINK => 'Tautan eksternal (buka situs lain)',
            self::FORM_MODE_EMBED => 'Sematkan formulir situs lain (embed)',
        ];
    }

    /**
     * Whether registrations for this jenjang are collected and stored in this
     * system via the built-in form.
     */
    public function usesInternalForm(): bool
    {
        return $this->form_mode === self::FORM_MODE_INTERNAL;
    }

    /**
     * Whether registrations are handled by an external site linked via a button.
     */
    public function usesExternalLink(): bool
    {
        return $this->form_mode === self::FORM_MODE_EXTERNAL_LINK;
    }

    /**
     * Whether registrations are handled by an embedded (iframe) external form.
     */
    public function usesEmbed(): bool
    {
        return $this->form_mode === self::FORM_MODE_EMBED;
    }

    /**
     * Whether this jenjang is currently accepting registrations, taking the
     * global on/off switch and the form mode (open wave / external / embed)
     * into account. Drives the open/closed status shown on the public site.
     */
    public function registrationOpen(): bool
    {
        if (! (bool) Setting::get('spmb_form_enabled', true)) {
            return false;
        }

        return match ($this->form_mode) {
            self::FORM_MODE_EXTERNAL_LINK => filled($this->external_url),
            self::FORM_MODE_EMBED => filled($this->embed_url),
            default => RegistrationWave::currentOpen($this) !== null,
        };
    }

    /**
     * Registration procedures for this jenjang, falling back to the global
     * Setting when this jenjang has none of its own.
     *
     * @return array<int, array<string, mixed>>
     */
    public function resolvedProcedures(): array
    {
        return $this->procedures ?: (json_decode((string) Setting::get('spmb_procedures', ''), true) ?: []);
    }

    /**
     * Registration fees for this jenjang, falling back to the global Setting.
     *
     * @return array<int, array<string, mixed>>
     */
    public function resolvedFees(): array
    {
        return $this->fees ?: (json_decode((string) Setting::get('spmb_fees', ''), true) ?: []);
    }

    public function resolvedFormTitle(): string
    {
        return $this->form_title ?: (string) Setting::get('spmb_form_title', 'Formulir Pendaftaran SPMB');
    }

    public function resolvedFormDescription(): string
    {
        return $this->form_description ?: (string) Setting::get('spmb_form_description', 'Isi formulir di bawah ini dengan data yang benar dan lengkap.');
    }

    public function resolvedClosedMessage(): string
    {
        return $this->closed_message ?: (string) Setting::get('spmb_closed_message', 'Pendaftaran SPMB saat ini sedang ditutup.');
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
