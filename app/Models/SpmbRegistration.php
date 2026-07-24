<?php

namespace App\Models;

use Database\Factories\SpmbRegistrationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SpmbRegistration extends Model
{
    /** @use HasFactory<SpmbRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'academic_year_id',
        'registration_wave_id',
        'admission_path_id',
        'full_name',
        'nik',
        'email',
        'phone',
        'birth_date',
        'birth_place',
        'previous_school',
        'previous_school_city',
        'address',
        'parent_name',
        'parent_phone',
        'notes',
        'data',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Registration columns a dynamic form field may write to directly, matched
     * by the field's key. Any other field key is stored in the `data` JSON
     * bucket instead.
     *
     * @return array<int, string>
     */
    public static function dynamicColumnKeys(): array
    {
        return [
            'full_name', 'nik', 'email', 'phone', 'birth_date', 'birth_place',
            'previous_school', 'previous_school_city', 'address', 'parent_name',
            'parent_phone', 'notes',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (self $registration): void {
            if (filled($registration->registration_number)) {
                return;
            }

            $short = Str::upper($registration->institution?->short_name ?: 'REG');
            $year = $registration->academicYear?->year_start ?: now()->year;

            $registration->registration_number = sprintf('%s-%s-%04d', $short, $year, $registration->id);
            $registration->saveQuietly();
        });
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return BelongsTo<AcademicYear, $this> */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /** @return BelongsTo<RegistrationWave, $this> */
    public function registrationWave(): BelongsTo
    {
        return $this->belongsTo(RegistrationWave::class);
    }

    /** @return BelongsTo<AdmissionPath, $this> */
    public function admissionPath(): BelongsTo
    {
        return $this->belongsTo(AdmissionPath::class);
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];
    }

    /**
     * Whether the public registration form should accept new submissions:
     * a wave of the active academic year must currently be open, and the
     * admin must not have force-closed the form. Pass an institution to check
     * a single jenjang (SD/SMP/SMA).
     */
    public static function isOpen(?Institution $institution = null): bool
    {
        if (! (bool) Setting::get('spmb_form_enabled', true)) {
            return false;
        }

        return RegistrationWave::currentOpen($institution) !== null;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }
}
