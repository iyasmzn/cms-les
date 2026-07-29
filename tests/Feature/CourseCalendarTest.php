<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\User;
use App\Support\CalendarMonth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_month_builds_a_monday_first_grid(): void
    {
        $calendar = CalendarMonth::fromString('2026-08');

        $weeks = $calendar->weeks();

        $this->assertSame('August 2026', $calendar->label());
        $this->assertSame('2026-07', $calendar->previousParam());
        $this->assertSame('2026-09', $calendar->nextParam());
        // First cell is a Monday, every week has 7 days.
        $this->assertSame('Monday', $weeks[0][0]->format('l'));
        foreach ($weeks as $week) {
            $this->assertCount(7, $week);
        }
    }

    public function test_calendar_requires_login(): void
    {
        $this->get(route('courses.calendar'))->assertRedirect(route('login'));
    }

    public function test_calendar_shows_only_sessions_of_groups_the_member_joined(): void
    {
        $user = User::factory()->create();

        $myGroup = Group::factory()->create(['name' => 'My Swim Group']);
        GroupMember::factory()->active()->create(['group_id' => $myGroup->id, 'user_id' => $user->id]);
        GroupSession::factory()->create(['group_id' => $myGroup->id, 'date' => '2026-08-10']);

        $otherGroup = Group::factory()->create(['name' => 'Stranger Group']);
        GroupSession::factory()->create(['group_id' => $otherGroup->id, 'date' => '2026-08-11']);

        $response = $this->actingAs($user)->get(route('courses.calendar', ['month' => '2026-08']));

        $response->assertStatus(200);
        $response->assertSee('My Swim Group');
        $response->assertDontSee('Stranger Group');
    }

    public function test_calendar_hides_cancelled_sessions(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['name' => 'Cancelled Test Group']);
        GroupMember::factory()->active()->create(['group_id' => $group->id, 'user_id' => $user->id]);
        GroupSession::factory()->cancelled()->create(['group_id' => $group->id, 'date' => '2026-08-12']);

        $response = $this->actingAs($user)->get(route('courses.calendar', ['month' => '2026-08']));

        $response->assertStatus(200);
        $response->assertDontSee('Cancelled Test Group');
    }
}
