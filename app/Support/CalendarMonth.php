<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * A month's calendar grid: the weeks (Monday-first) that cover a given month,
 * including the leading/trailing days from adjacent months, plus navigation
 * helpers. Used by both the member calendar page and the admin dashboard widget.
 */
class CalendarMonth
{
    public Carbon $start;

    public function __construct(public int $year, public int $month)
    {
        $this->start = Carbon::create($year, $month, 1)->startOfDay();
    }

    /**
     * Build from a "YYYY-MM" string, falling back to the current month when the
     * value is missing or malformed.
     */
    public static function fromString(?string $value): self
    {
        if ($value !== null && preg_match('/^(\d{4})-(\d{2})$/', $value, $matches)) {
            $month = (int) $matches[2];

            if ($month >= 1 && $month <= 12) {
                return new self((int) $matches[1], $month);
            }
        }

        $now = Carbon::now();

        return new self($now->year, $now->month);
    }

    public function label(): string
    {
        return $this->start->format('F Y');
    }

    public function param(): string
    {
        return $this->start->format('Y-m');
    }

    public function previousParam(): string
    {
        return $this->start->copy()->subMonthNoOverflow()->format('Y-m');
    }

    public function nextParam(): string
    {
        return $this->start->copy()->addMonthNoOverflow()->format('Y-m');
    }

    public function rangeStart(): Carbon
    {
        return $this->start->copy()->startOfWeek(Carbon::MONDAY);
    }

    public function rangeEnd(): Carbon
    {
        return $this->start->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
    }

    /**
     * The calendar grid as an array of weeks, each week an array of 7 Carbon
     * dates from Monday to Sunday.
     *
     * @return array<int, array<int, Carbon>>
     */
    public function weeks(): array
    {
        $weeks = [];
        $cursor = $this->rangeStart();
        $end = $this->rangeEnd();

        while ($cursor->lte($end)) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $week[] = $cursor->copy();
                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * Whether the given date belongs to this calendar's month (not a spillover
     * day from an adjacent month).
     */
    public function isCurrentMonth(Carbon $date): bool
    {
        return $date->year === $this->year && $date->month === $this->month;
    }
}
