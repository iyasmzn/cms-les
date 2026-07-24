<?php

namespace App\Models;

use Database\Factories\GroupMemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GroupMember extends Model
{
    /** @use HasFactory<GroupMemberFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
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
    }

    /** @return BelongsTo<Group, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
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
