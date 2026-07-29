<?php

namespace Tests\Feature;

use App\Models\CoursePayment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Institution;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\CourseRegistrationStatusUpdated;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CourseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private Institution $course;

    private Group $group;

    protected function setUp(): void
    {
        parent::setUp();

        $this->course = Institution::factory()->create([
            'slug' => 'swimming',
            'has_groups' => true,
            'is_active' => true,
        ]);

        $this->group = Group::factory()->create([
            'institution_id' => $this->course->id,
            'slug' => 'beginner-a',
            'capacity' => 2,
            'is_active' => true,
        ]);
    }

    public function test_courses_index_lists_course_institutions(): void
    {
        Institution::factory()->create(['has_groups' => false, 'name' => 'Regular Jenjang']);

        $response = $this->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertSee($this->course->name);
        $response->assertDontSee('Regular Jenjang');
    }

    public function test_course_show_page_lists_groups(): void
    {
        $response = $this->get(route('courses.show', $this->course));

        $response->assertStatus(200);
        $response->assertSee($this->group->name);
    }

    public function test_show_page_returns_404_for_non_course_institution(): void
    {
        $plain = Institution::factory()->create(['has_groups' => false]);

        $this->get(route('courses.show', $plain))->assertNotFound();
    }

    public function test_registration_form_is_accessible_for_an_open_group(): void
    {
        $response = $this->get(route('courses.register', [$this->course, $this->group]));

        $response->assertStatus(200);
        $response->assertSee('Submit Registration');
    }

    public function test_it_stores_a_pending_member(): void
    {
        $response = $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Jane Doe',
            'phone' => '08123456789',
            'gender' => 'female',
            'email' => 'jane@example.com',
        ]);

        $response->assertRedirect(route('courses.show', $this->course));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('group_members', [
            'group_id' => $this->group->id,
            'full_name' => 'Jane Doe',
            'status' => 'pending',
        ]);

        $member = GroupMember::first();
        $this->assertNotNull($member->registration_number);
    }

    public function test_guest_registration_offers_account_creation(): void
    {
        $this->followingRedirects()
            ->post(route('courses.register.store', [$this->course, $this->group]), [
                'full_name' => 'Guest Wants Account',
                'phone' => '08123123123',
                'email' => 'guest@example.com',
            ])
            ->assertStatus(200)
            ->assertSee('Create an account');
    }

    public function test_logged_in_registration_does_not_offer_account_creation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->followingRedirects()
            ->post(route('courses.register.store', [$this->course, $this->group]), [
                'full_name' => 'Already A Member',
                'phone' => '08129999999',
            ])
            ->assertStatus(200)
            ->assertDontSee('Create an account');
    }

    public function test_registration_requires_name_and_phone(): void
    {
        $response = $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => '',
            'phone' => '',
        ]);

        $response->assertSessionHasErrors(['full_name', 'phone']);
        $this->assertDatabaseCount('group_members', 0);
    }

    public function test_it_blocks_registration_when_the_group_is_full(): void
    {
        GroupMember::factory()->count(2)->create([
            'group_id' => $this->group->id,
            'status' => 'active',
        ]);

        $response = $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Late Comer',
            'phone' => '08123456789',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('group_members', ['full_name' => 'Late Comer']);
    }

    public function test_schedule_label_formats_days_and_times(): void
    {
        $group = Group::factory()->create([
            'days' => ['mon', 'wed'],
            'start_time' => '16:00',
            'end_time' => '17:30',
        ]);

        $this->assertSame('Mon & Wed, 16:00–17:30', $group->fresh()->scheduleLabel());
    }

    public function test_schedule_label_orders_days_and_handles_single_day(): void
    {
        $group = Group::factory()->create([
            'days' => ['sat'],
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        $this->assertSame('Sat, 08:00–10:00', $group->fresh()->scheduleLabel());
    }

    public function test_group_binding_is_scoped_to_its_institution(): void
    {
        $otherCourse = Institution::factory()->create(['has_groups' => true]);

        $this->get(route('courses.register', [$otherCourse, $this->group]))
            ->assertNotFound();
    }

    public function test_it_blocks_duplicate_signup_in_the_same_group(): void
    {
        GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'phone' => '08123456789',
            'status' => 'pending',
        ]);

        $response = $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Same Phone',
            'phone' => '08123456789',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseMissing('group_members', ['full_name' => 'Same Phone']);
    }

    public function test_it_notifies_admins_on_registration(): void
    {
        $admin = User::factory()->create();
        Role::findOrCreate('super_admin', 'web');
        $admin->assignRole('super_admin');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Notified Registrant',
            'phone' => '08987654321',
        ]);

        $this->assertCount(1, $admin->fresh()->notifications);
    }

    public function test_homepage_shows_the_courses_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Our Courses');
        $response->assertSee($this->course->name);
    }

    public function test_registration_is_linked_to_the_logged_in_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Logged In Registrant',
            'phone' => '08111111111',
        ]);

        $this->assertDatabaseHas('group_members', [
            'full_name' => 'Logged In Registrant',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_registration_is_not_linked_to_any_user(): void
    {
        $this->post(route('courses.register.store', [$this->course, $this->group]), [
            'full_name' => 'Guest Registrant',
            'phone' => '08222222222',
        ]);

        $this->assertDatabaseHas('group_members', [
            'full_name' => 'Guest Registrant',
            'user_id' => null,
        ]);
    }

    public function test_logging_in_claims_matching_guest_registrations(): void
    {
        $user = User::factory()->create(['email' => 'claim@example.com']);

        $mine = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => null,
            'email' => 'claim@example.com',
        ]);
        $other = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => null,
            'email' => 'someone.else@example.com',
        ]);

        event(new Login('web', $user, false));

        $this->assertSame($user->id, $mine->fresh()->user_id);
        $this->assertNull($other->fresh()->user_id);
    }

    public function test_logging_in_claims_registrations_matching_phone(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com', 'phone' => '08123400000']);

        $byPhone = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => null,
            'email' => 'different@example.com',
            'phone' => '08123400000',
        ]);

        event(new Login('web', $user, false));

        $this->assertSame($user->id, $byPhone->fresh()->user_id);
    }

    public function test_member_is_emailed_when_status_changes_to_active(): void
    {
        Setting::set('mail_enabled', '1');
        Notification::fake();

        $member = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'email' => 'member@example.com',
            'status' => 'pending',
        ]);

        $member->update(['status' => 'active']);

        Notification::assertSentOnDemand(CourseRegistrationStatusUpdated::class);
    }

    public function test_member_is_not_emailed_when_mail_is_disabled(): void
    {
        Setting::set('mail_enabled', '0');
        Notification::fake();

        $member = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'email' => 'member@example.com',
            'status' => 'pending',
        ]);

        $member->update(['status' => 'active']);

        Notification::assertNothingSent();
    }

    public function test_my_courses_requires_login(): void
    {
        $this->get(route('courses.mine'))->assertRedirect(route('login'));
    }

    public function test_my_courses_shows_payment_summary(): void
    {
        $user = User::factory()->create();
        $member = GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => $user->id,
            'full_name' => 'Paying Member',
        ]);
        CoursePayment::factory()->create([
            'group_member_id' => $member->id,
            'amount' => 25000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($user)->get(route('courses.mine'))
            ->assertStatus(200)
            ->assertSee('Outstanding')
            ->assertSee('Rp25.000');
    }

    public function test_my_courses_lists_only_the_users_own_registrations(): void
    {
        $user = User::factory()->create();

        GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => $user->id,
            'full_name' => 'My Own Registration',
        ]);
        GroupMember::factory()->create([
            'group_id' => $this->group->id,
            'full_name' => 'Someone Elses Registration',
        ]);

        $this->actingAs($user)->get(route('courses.mine'))
            ->assertStatus(200)
            ->assertSee('My Own Registration')
            ->assertDontSee('Someone Elses Registration');
    }
}
