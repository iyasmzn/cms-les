<?php

namespace App\Models;

use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'teacher_id',
        'name',
        'slug',
        'level',
        'days',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'days' => 'array',
        'capacity' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Weekday keys mapped to their short display labels, in week order.
     *
     * @return array<string, string>
     */
    public static function dayOptions(): array
    {
        return [
            'mon' => 'Mon',
            'tue' => 'Tue',
            'wed' => 'Wed',
            'thu' => 'Thu',
            'fri' => 'Fri',
            'sat' => 'Sat',
            'sun' => 'Sun',
        ];
    }

    /**
     * The course/institution this group belongs to.
     *
     * @return BelongsTo<Institution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * The coach/instructor assigned to this group (optional).
     *
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** @return HasMany<GroupMember, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /** @return HasMany<GroupSession, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(GroupSession::class);
    }

    /**
     * Carbon dayOfWeek numbers (0=Sun … 6=Sat) for this group's weekly pattern.
     *
     * @return array<int, int>
     */
    private function scheduledWeekdays(): array
    {
        $map = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];

        return collect($this->days ?? [])
            ->map(fn (string $day): ?int => $map[$day] ?? null)
            ->filter(fn (?int $value): bool => $value !== null)
            ->values()
            ->all();
    }

    /**
     * Create dated sessions from the group's weekly pattern between two dates,
     * inheriting the group's time and location. Existing dates are skipped so
     * the generator is safe to re-run. Returns the number of sessions created.
     */
    public function generateSessions(Carbon $start, Carbon $end, ?float $fee = null): int
    {
        $weekdays = $this->scheduledWeekdays();

        if ($weekdays === [] || $start->gt($end)) {
            return 0;
        }

        $existing = $this->sessions()
            ->pluck('date')
            ->map(fn (Carbon $date): string => $date->toDateString())
            ->all();

        $created = 0;

        for ($date = $start->copy()->startOfDay(); $date->lte($end); $date->addDay()) {
            if (! in_array($date->dayOfWeek, $weekdays, true)) {
                continue;
            }

            if (in_array($date->toDateString(), $existing, true)) {
                continue;
            }

            $this->sessions()->create([
                'date' => $date->toDateString(),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'location' => $this->location,
                'fee' => $fee,
                'status' => 'scheduled',
            ]);

            $created++;
        }

        return $created;
    }

    /**
     * The selected weekdays as short labels, e.g. "Mon & Wed".
     */
    public function daysLabel(): ?string
    {
        $options = self::dayOptions();

        $labels = collect($this->days ?? [])
            ->filter(fn (string $day): bool => isset($options[$day]))
            ->sortBy(fn (string $day): int => array_search($day, array_keys($options), true))
            ->map(fn (string $day): string => $options[$day])
            ->values();

        if ($labels->isEmpty()) {
            return null;
        }

        if ($labels->count() === 1) {
            return $labels->first();
        }

        return $labels->slice(0, -1)->implode(', ').' & '.$labels->last();
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
     * A human-readable schedule, e.g. "Mon & Wed, 16:00–17:30".
     */
    public function scheduleLabel(): ?string
    {
        return collect([$this->daysLabel(), $this->timeLabel()])
            ->filter()
            ->implode(', ') ?: null;
    }

    /**
     * Number of members currently counting against this group's capacity
     * (pending and active registrations).
     */
    public function takenSeats(): int
    {
        return $this->members()->whereIn('status', ['pending', 'active'])->count();
    }

    /**
     * Remaining seats, or null when the group has no capacity limit.
     */
    public function remainingSeats(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->takenSeats());
    }

    /**
     * Whether the group can still accept new members: active and not full.
     */
    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $remaining = $this->remainingSeats();

        return $remaining === null || $remaining > 0;
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
