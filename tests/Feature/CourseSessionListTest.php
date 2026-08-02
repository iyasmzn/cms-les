<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSessionListTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_list_requires_login(): void
    {
        $registration = GroupMember::factory()->active()->create();

        $this->get(route('courses.sessions', $registration))->assertRedirect(route('login'));
    }

    public function test_a_member_can_see_their_own_group_sessions(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['name' => 'Freestyle Beginner']);
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        GroupSession::factory()->create([
            'group_id' => $group->id,
            'date' => today()->addDays(3),
            'topic' => 'Breathing Drill',
        ]);

        $response = $this->actingAs($user)->get(route('courses.sessions', $registration));

        $response->assertStatus(200);
        $response->assertSee('Freestyle Beginner');
        $response->assertSee('Breathing Drill');
        $response->assertSee('Upcoming Sessions');
    }

    public function test_it_separates_upcoming_from_past_sessions(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        GroupSession::factory()->create(['group_id' => $group->id, 'date' => today()->addDay(), 'topic' => 'Future Topic']);
        GroupSession::factory()->completed()->create(['group_id' => $group->id, 'date' => today()->subDay(), 'topic' => 'Old Topic']);

        $response = $this->actingAs($user)->get(route('courses.sessions', $registration));

        $response->assertStatus(200);
        $response->assertViewHas('upcoming', fn ($upcoming) => $upcoming->pluck('topic')->all() === ['Future Topic']);
        $response->assertViewHas('past', fn ($past) => $past->pluck('topic')->all() === ['Old Topic']);
    }

    public function test_it_still_shows_cancelled_sessions(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        GroupSession::factory()->cancelled()->create([
            'group_id' => $group->id,
            'date' => today()->addDays(2),
            'topic' => 'Called Off Session',
        ]);

        $response = $this->actingAs($user)->get(route('courses.sessions', $registration));

        $response->assertStatus(200);
        $response->assertSee('Called Off Session');
        $response->assertSee('Cancelled');
    }

    public function test_a_member_cannot_see_another_members_registration(): void
    {
        $user = User::factory()->create();
        $someoneElse = GroupMember::factory()->active()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->get(route('courses.sessions', $someoneElse))
            ->assertNotFound();
    }

    public function test_a_guest_registration_is_not_reachable_by_a_logged_in_member(): void
    {
        $user = User::factory()->create();
        $guestRegistration = GroupMember::factory()->active()->create(['user_id' => null]);

        $this->actingAs($user)
            ->get(route('courses.sessions', $guestRegistration))
            ->assertNotFound();
    }

    public function test_it_links_to_the_calendar_filtered_by_the_group(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('courses.sessions', $registration));

        $response->assertStatus(200);
        $response->assertSee(route('courses.calendar', ['group' => $group->id]), false);
    }

    public function test_my_courses_links_each_registration_to_its_session_list(): void
    {
        $user = User::factory()->create();
        $registration = GroupMember::factory()->active()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('courses.mine'));

        $response->assertStatus(200);
        $response->assertSee(route('courses.sessions', $registration), false);
    }
}
