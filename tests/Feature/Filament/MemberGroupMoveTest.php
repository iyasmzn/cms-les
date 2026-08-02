<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Groups\Pages\EditGroup;
use App\Filament\Resources\Groups\RelationManagers\MembersRelationManager;
use App\Models\CoursePayment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Institution;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MemberGroupMoveTest extends TestCase
{
    use RefreshDatabase;

    private Institution $course;

    private Group $beginner;

    private Group $advanced;

    protected function setUp(): void
    {
        parent::setUp();

        $this->course = Institution::factory()->create(['has_groups' => true, 'is_active' => true]);

        $this->beginner = Group::factory()->create([
            'institution_id' => $this->course->id,
            'name' => 'Beginner',
            'is_active' => true,
        ]);

        $this->advanced = Group::factory()->create([
            'institution_id' => $this->course->id,
            'name' => 'Advanced',
            'is_active' => true,
        ]);
    }

    private function grantGroupPermissions(User $user, array $actions): void
    {
        $permissions = collect($actions)
            ->map(fn (string $name): Permission => Permission::findOrCreate($name, 'web'));

        $user->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();

        $this->grantGroupPermissions($admin, [
            'ViewAny:Group', 'View:Group', 'Create:Group', 'Update:Group',
            'Delete:Group', 'DeleteAny:Group', 'ViewAll:Group', 'MoveMember:Group',
        ]);

        return $admin;
    }

    /**
     * A user linked to the coach profile of the given group.
     */
    private function instructorFor(Group $group): User
    {
        $user = User::factory()->create();

        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $group->update(['teacher_id' => $teacher->id]);

        $this->grantGroupPermissions($user, [
            'ViewAny:Group', 'View:Group', 'Update:Group', 'MoveMember:Group',
        ]);

        return $user;
    }

    private function moveMember(GroupMember $member, Group $from, Group $to): Testable
    {
        return Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $from,
            'pageClass' => EditGroup::class,
        ])->callAction(
            TestAction::make('moveGroup')->table($member),
            ['group_id' => $to->id],
        );
    }

    public function test_an_admin_can_move_a_member_to_a_sibling_group(): void
    {
        $this->actingAs($this->admin());

        $member = GroupMember::factory()->active()->create(['group_id' => $this->beginner->id]);

        $this->moveMember($member, $this->beginner, $this->advanced);

        $this->assertSame($this->advanced->id, $member->fresh()->group_id);
    }

    public function test_moving_keeps_registration_number_join_date_and_payments(): void
    {
        $this->actingAs($this->admin());

        $member = GroupMember::factory()->active()->create([
            'group_id' => $this->beginner->id,
            'joined_at' => '2026-01-15',
        ]);
        $payment = CoursePayment::factory()->create([
            'group_member_id' => $member->id,
            'amount' => 50000,
        ]);

        $registrationNumber = $member->registration_number;

        $this->moveMember($member, $this->beginner, $this->advanced);

        $member->refresh();

        $this->assertSame($registrationNumber, $member->registration_number);
        $this->assertSame('2026-01-15', $member->joined_at->toDateString());
        $this->assertDatabaseHas('course_payments', ['id' => $payment->id, 'group_member_id' => $member->id]);
    }

    public function test_it_refuses_to_move_into_a_full_group(): void
    {
        $this->actingAs($this->admin());

        $this->advanced->update(['capacity' => 1]);
        GroupMember::factory()->active()->create(['group_id' => $this->advanced->id]);

        $member = GroupMember::factory()->active()->create(['group_id' => $this->beginner->id]);

        $this->moveMember($member, $this->beginner, $this->advanced);

        $this->assertSame($this->beginner->id, $member->fresh()->group_id);
    }

    public function test_it_refuses_to_move_into_a_group_of_another_course(): void
    {
        $this->actingAs($this->admin());

        $otherCourse = Institution::factory()->create(['has_groups' => true]);
        $foreign = Group::factory()->create(['institution_id' => $otherCourse->id, 'is_active' => true]);

        $member = GroupMember::factory()->active()->create(['group_id' => $this->beginner->id]);

        $this->moveMember($member, $this->beginner, $foreign);

        $this->assertSame($this->beginner->id, $member->fresh()->group_id);
    }

    public function test_it_refuses_when_the_member_already_holds_a_spot_in_the_target(): void
    {
        $this->actingAs($this->admin());

        $user = User::factory()->create();
        $member = GroupMember::factory()->active()->create([
            'group_id' => $this->beginner->id,
            'user_id' => $user->id,
        ]);
        GroupMember::factory()->active()->create([
            'group_id' => $this->advanced->id,
            'user_id' => $user->id,
        ]);

        $this->moveMember($member, $this->beginner, $this->advanced);

        $this->assertSame($this->beginner->id, $member->fresh()->group_id);
    }

    public function test_an_instructor_can_move_a_member_within_their_own_group(): void
    {
        $this->actingAs($this->instructorFor($this->beginner));

        $member = GroupMember::factory()->active()->create(['group_id' => $this->beginner->id]);

        $this->moveMember($member, $this->beginner, $this->advanced);

        $this->assertSame($this->advanced->id, $member->fresh()->group_id);
    }

    public function test_an_instructor_cannot_move_members_of_a_group_they_do_not_coach(): void
    {
        // Coaches Beginner, but tries to act on Advanced.
        $this->actingAs($this->instructorFor($this->beginner));

        $member = GroupMember::factory()->active()->create(['group_id' => $this->advanced->id]);

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->advanced,
            'pageClass' => EditGroup::class,
        ])->assertActionHidden(TestAction::make('moveGroup')->table($member));
    }

    public function test_an_instructor_only_sees_the_groups_they_coach(): void
    {
        $instructor = $this->instructorFor($this->beginner);

        $this->assertTrue($instructor->can('view', $this->beginner));
        $this->assertFalse($instructor->can('view', $this->advanced));
    }

    public function test_a_plain_admin_is_not_narrowed_to_coached_groups(): void
    {
        $admin = $this->admin();

        $this->assertFalse($admin->isInstructor());
        $this->assertTrue($admin->can('view', $this->beginner));
        $this->assertTrue($admin->can('view', $this->advanced));
    }

    public function test_an_instructor_cannot_delete_members(): void
    {
        $this->actingAs($this->instructorFor($this->beginner));

        $member = GroupMember::factory()->active()->create(['group_id' => $this->beginner->id]);

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->beginner,
            'pageClass' => EditGroup::class,
        ])->assertActionHidden(TestAction::make('delete')->table($member));
    }
}
