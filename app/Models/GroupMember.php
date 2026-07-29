<?php

namespace App\Models;

use App\Notifications\CourseRegistrationStatusUpdated;
use Database\Factories\GroupMemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class GroupMember extends Model
{
    /** @use HasFactory<GroupMemberFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'registration_number',
        'full_name',
        'nik',
        'gender',
        'birth_date',
        'birth_place',
        'phone',
        'email',
        'address',
        'parent_name',
        'parent_phone',
        'notes',
        'data',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_at' => 'date',
        'data' => 'array',
    ];

    protected static function booted(): void
    {
        static::created(function (self $member): void {
            if (filled($member->registration_number)) {
                return;
            }

            $short = Str::upper($member->group?->institution?->short_name ?: 'LES');

            $member->registration_number = sprintf('%s-%04d', $short, $member->id);
            $member->saveQuietly();
        });

        static::updated(function (self $member): void {
            $member->notifyStatusChange();
        });
    }

    /**
     * Email the member when an admin moves their registration to a decided
     * state (active/inactive). Only runs when mail is enabled and an email is
     * on file, so the panel action never fails on unconfigured SMTP.
     */
    private function notifyStatusChange(): void
    {
        if (! $this->wasChanged('status') || ! in_array($this->status, ['active', 'inactive'], true)) {
            return;
        }

        if (blank($this->email) || ! (bool) Setting::get('mail_enabled', false)) {
            return;
        }

        Notification::route('mail', $this->email)
            ->notify(new CourseRegistrationStatusUpdated($this));
    }

    /** @return BelongsTo<Group, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /** @return HasMany<CoursePayment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(CoursePayment::class);
    }

    /**
     * Total amount this member still owes (unpaid payments).
     */
    public function outstandingTotal(): float
    {
        return (float) $this->payments()->where('status', 'unpaid')->sum('amount');
    }

    /**
     * Total amount this member has paid.
     */
    public function paidTotal(): float
    {
        return (float) $this->payments()->where('status', 'paid')->sum('amount');
    }

    /**
     * The account that submitted this registration, if the registrant was
     * logged in. Null for guest registrations.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
