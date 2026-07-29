<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GroupSessionTest extends TestCase
{
    use RefreshDatabase;

    private function makeGroup(array $overrides = []): Group
    {
        return Group::factory()->create(array_merge([
            'days' => ['mon', 'wed'],
            'start_time' => '16:00',
            'end_time' => '17:30',
            'location' => 'Main Pool',
        ], $overrides));
    }

    public function test_it_generates_sessions_matching_the_weekly_pattern(): void
    {
        $group = $this->makeGroup();

        $created = $group->generateSessions(
            Carbon::parse('2026-08-01'),
            Carbon::parse('2026-08-21'),
            50000,
        );

        $this->assertSame($group->sessions()->count(), $created);
        $this->assertGreaterThan(0, $created);

        // Every generated session falls on Monday (1) or Wednesday (3) and
        // inherits the group's time, location, and fee.
        foreach ($group->sessions as $session) {
            $this->assertContains($session->date->dayOfWeek, [1, 3]);
            $this->assertSame('16:00:00', $session->start_time);
            $this->assertSame('Main Pool', $session->location);
            $this->assertEquals(50000.0, (float) $session->fee);
            $this->assertSame('scheduled', $session->status);
        }
    }

    public function test_it_skips_dates_that_already_have_a_session(): void
    {
        $group = $this->makeGroup();

        $start = Carbon::parse('2026-08-01');
        $end = Carbon::parse('2026-08-21');

        $first = $group->generateSessions($start, $end);
        $second = $group->generateSessions($start, $end);

        $this->assertGreaterThan(0, $first);
        $this->assertSame(0, $second);
        $this->assertSame($first, $group->sessions()->count());
    }

    public function test_it_generates_nothing_without_a_weekly_pattern(): void
    {
        $group = $this->makeGroup(['days' => []]);

        $created = $group->generateSessions(
            Carbon::parse('2026-08-01'),
            Carbon::parse('2026-08-31'),
        );

        $this->assertSame(0, $created);
    }

    public function test_it_generates_nothing_when_end_is_before_start(): void
    {
        $group = $this->makeGroup();

        $created = $group->generateSessions(
            Carbon::parse('2026-08-31'),
            Carbon::parse('2026-08-01'),
        );

        $this->assertSame(0, $created);
    }
}
