<?php

namespace Tests\Feature;

use App\Models\CoursePayment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_page_requires_login(): void
    {
        $this->get(route('courses.billing'))->assertRedirect(route('login'));
    }

    public function test_it_shows_the_members_bills_with_totals(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['name' => 'Freestyle Beginner']);
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $session = GroupSession::factory()->create([
            'group_id' => $group->id,
            'date' => today()->subWeek(),
            'topic' => 'Breathing Drill',
        ]);

        CoursePayment::factory()->paid()->create([
            'group_member_id' => $registration->id,
            'group_session_id' => $session->id,
            'amount' => 50000,
        ]);

        CoursePayment::factory()->create([
            'group_member_id' => $registration->id,
            'amount' => 75000,
        ]);

        $response = $this->actingAs($user)->get(route('courses.billing'));

        $response->assertStatus(200);
        $response->assertSee('Freestyle Beginner');
        $response->assertSee('Breathing Drill');
        $response->assertSee('Rp125.000');
        $response->assertSee('Rp75.000');
        $response->assertSee('Rp50.000');
        $response->assertSee('outstanding');
    }

    public function test_it_hides_bills_belonging_to_other_members(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $mine = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'full_name' => 'My Own Registration',
        ]);
        $theirs = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'full_name' => 'Someone Else Entirely',
        ]);

        CoursePayment::factory()->create(['group_member_id' => $mine->id, 'amount' => 30000]);
        CoursePayment::factory()->create(['group_member_id' => $theirs->id, 'amount' => 999000]);

        $response = $this->actingAs($user)->get(route('courses.billing'));

        $response->assertStatus(200);
        $response->assertSee('My Own Registration');
        $response->assertDontSee('Someone Else Entirely');
        $response->assertDontSee('Rp999.000');
    }

    public function test_it_can_filter_bills_to_a_single_registration(): void
    {
        $user = User::factory()->create();

        $first = GroupMember::factory()->active()->create([
            'group_id' => Group::factory()->create(['name' => 'Morning Group'])->id,
            'user_id' => $user->id,
        ]);
        $second = GroupMember::factory()->active()->create([
            'group_id' => Group::factory()->create(['name' => 'Evening Group'])->id,
            'user_id' => $user->id,
        ]);

        CoursePayment::factory()->create(['group_member_id' => $first->id, 'amount' => 30000]);
        CoursePayment::factory()->create(['group_member_id' => $second->id, 'amount' => 45000]);

        $response = $this->actingAs($user)->get(route('courses.billing', ['registration' => $first->id]));

        $response->assertStatus(200);
        $response->assertSee('Rp30.000');
        $response->assertDontSee('Rp45.000');
    }

    public function test_filtering_by_someone_elses_registration_is_not_found(): void
    {
        $user = User::factory()->create();
        $theirs = GroupMember::factory()->active()->create();

        $this->actingAs($user)
            ->get(route('courses.billing', ['registration' => $theirs->id]))
            ->assertNotFound();
    }

    public function test_an_unpaid_bill_links_to_the_payment_form(): void
    {
        $user = User::factory()->create();
        $registration = GroupMember::factory()->active()->create(['user_id' => $user->id]);

        $unpaid = CoursePayment::factory()->create(['group_member_id' => $registration->id]);
        $paid = CoursePayment::factory()->paid()->create(['group_member_id' => $registration->id]);

        $response = $this->actingAs($user)->get(route('courses.billing'));

        $response->assertStatus(200);
        $response->assertSee(route('courses.bills.pay', $unpaid), false);
        $response->assertDontSee(route('courses.bills.pay', $paid), false);
    }

    public function test_it_shows_an_empty_state_when_nothing_is_billed(): void
    {
        $user = User::factory()->create();
        GroupMember::factory()->active()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('courses.billing'));

        $response->assertStatus(200);
        $response->assertSee('No Bills Yet');
    }

    public function test_waived_bills_count_towards_billed_but_not_outstanding(): void
    {
        $user = User::factory()->create();
        $registration = GroupMember::factory()->active()->create(['user_id' => $user->id]);

        CoursePayment::factory()->paid()->create(['group_member_id' => $registration->id, 'amount' => 20000]);
        CoursePayment::factory()->waived()->create(['group_member_id' => $registration->id, 'amount' => 20000]);

        $totals = $registration->refresh()->paymentTotals();

        $this->assertEqualsWithDelta(40000.0, $totals['billed'], 0.01);
        $this->assertEqualsWithDelta(20000.0, $totals['paid'], 0.01);
        $this->assertEqualsWithDelta(20000.0, $totals['waived'], 0.01);
        $this->assertEqualsWithDelta(0.0, $totals['outstanding'], 0.01);

        $this->actingAs($user)
            ->get(route('courses.billing'))
            ->assertStatus(200)
            ->assertSee('Waived');
    }

    public function test_the_session_list_shows_the_payment_status_of_each_session(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $registration = GroupMember::factory()->active()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $session = GroupSession::factory()->create([
            'group_id' => $group->id,
            'date' => today()->addDay(),
        ]);

        CoursePayment::factory()->create([
            'group_member_id' => $registration->id,
            'group_session_id' => $session->id,
            'amount' => 60000,
        ]);

        $response = $this->actingAs($user)->get(route('courses.sessions', $registration));

        $response->assertStatus(200);
        $response->assertSee('Rp60.000 · Unpaid', false);
    }
}
