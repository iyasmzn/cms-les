<?php

namespace App\Models;

use Database\Factories\GroupSessionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class GroupSession extends Model
{
    /** @use HasFactory<GroupSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
        'date',
        'start_time',
        'end_time',
        'location',
        'topic',
        'fee',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'fee' => 'decimal:2',
    ];

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
     * Create an unpaid bill for every active member of this session's group at
     * the session fee, skipping members already billed. Safe to re-run. Returns
     * the number of bills created; 0 when the session has no fee.
     */
    public function billActiveMembers(): int
    {
        if ($this->fee === null) {
            return 0;
        }

        $alreadyBilled = $this->payments()->pluck('group_member_id')->all();

        $memberIds = $this->group->members()
            ->where('status', 'active')
            ->whereNotIn('id', $alreadyBilled)
            ->pluck('id');

        foreach ($memberIds as $memberId) {
            $this->payments()->create([
                'group_member_id' => $memberId,
                'amount' => $this->fee,
                'status' => 'unpaid',
            ]);
        }

        return $memberIds->count();
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * The session location, falling back to the group's default location.
     */
    public function resolvedLocation(): ?string
    {
        return $this->location ?: $this->group?->location;
    }

    /**
     * The time window as "HH:MM–HH:MM", or null when not set.
     */
    public function timeLabel(): ?string
    {
        if (blank($this->start_time) || blank($this->end_time)) {
            return null;
        }

        $format = fn (string $time): string => substr($time, 0, 5);

        return $format($this->start_time).'–'.$format($this->end_time);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('date')->orderBy('start_time');
    }
}
