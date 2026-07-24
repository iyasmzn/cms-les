<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
