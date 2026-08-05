<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Groups\GroupResource;
use App\Filament\Resources\Groups\Pages\EditGroup;
use App\Filament\Resources\Groups\Pages\ListGroups;
use App\Filament\Resources\Groups\Pages\ViewGroup;
use App\Filament\Resources\Groups\RelationManagers\SessionsRelationManager;
use App\Filament\Resources\Institutions\Pages\EditInstitution;
use App\Filament\Resources\Institutions\RelationManagers\GroupsRelationManager;
use App\Models\Group;
use App\Models\GroupSession;
use App\Models\Institution;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class GroupResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->grantPermissions($user, 'Group');
        $this->grantPermissions($user, 'Institution');
        $this->actingAs($user);
    }

    private function grantPermissions(User $user, string $model): void
    {
        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny', 'Reorder'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:{$model}", 'web'));

        $user->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_it_lists_groups(): void
    {
        $groups = Group::factory()->count(3)->create();

        Livewire::test(ListGroups::class)
            ->assertCanSeeTableRecords($groups);
    }

    public function test_groups_relation_manager_is_visible_for_course_institutions(): void
    {
        $course = Institution::factory()->create(['has_groups' => true]);

        $this->assertTrue(
            GroupsRelationManager::canViewForRecord($course, EditInstitution::class),
        );
    }

    public function test_groups_relation_manager_is_hidden_for_plain_institutions(): void
    {
        $plain = Institution::factory()->create(['has_groups' => false]);

        $this->assertFalse(
            GroupsRelationManager::canViewForRecord($plain, EditInstitution::class),
        );
    }

    public function test_it_renders_the_group_view_page(): void
    {
        $group = Group::factory()->create();

        Livewire::test(ViewGroup::class, ['record' => $group->id])
            ->assertSuccessful()
            ->assertSee($group->name);
    }

    public function test_it_toggles_a_group_active_from_the_view_page(): void
    {
        $group = Group::factory()->create(['is_active' => true]);

        Livewire::test(ViewGroup::class, ['record' => $group->id])
            ->callAction('toggleActive')
            ->assertNotified();

        $this->assertFalse($group->refresh()->is_active);

        Livewire::test(ViewGroup::class, ['record' => $group->id])
            ->callAction('toggleActive');

        $this->assertTrue($group->refresh()->is_active);
    }

    public function test_the_view_page_exposes_only_the_toggle_and_edit_actions(): void
    {
        $group = Group::factory()->create();

        Livewire::test(ViewGroup::class, ['record' => $group->id])
            ->assertActionVisible('toggleActive')
            ->assertActionVisible('edit')
            ->assertActionDoesNotExist('delete');
    }

    public function test_clicking_a_group_row_opens_the_view_page(): void
    {
        $group = Group::factory()->create();

        Livewire::test(ListGroups::class)
            ->assertActionHasUrl(
                TestAction::make('view')->table($group),
                GroupResource::getUrl('view', ['record' => $group]),
            );
    }

    public function test_sessions_relation_manager_lists_sessions(): void
    {
        $group = Group::factory()->create();
        $sessions = GroupSession::factory()->count(3)->create(['group_id' => $group->id]);

        Livewire::test(SessionsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($sessions);
    }

    public function test_it_marks_a_session_completed_from_the_table(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create([
            'group_id' => $group->id,
            'status' => 'scheduled',
        ]);

        Livewire::test(SessionsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])
            ->callAction(TestAction::make('markCompleted')->table($session));

        $this->assertSame('completed', $session->refresh()->status);
    }

    public function test_it_cancels_a_session_from_the_table(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create([
            'group_id' => $group->id,
            'status' => 'scheduled',
        ]);

        Livewire::test(SessionsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])
            ->callAction(TestAction::make('markCancelled')->table($session));

        $this->assertSame('cancelled', $session->refresh()->status);
    }

    public function test_it_hides_the_mark_completed_action_for_completed_sessions(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create([
            'group_id' => $group->id,
            'status' => 'completed',
        ]);

        Livewire::test(SessionsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])
            ->assertActionHidden(TestAction::make('markCompleted')->table($session));
    }

    public function test_it_marks_sessions_completed_in_bulk(): void
    {
        $group = Group::factory()->create();
        $sessions = GroupSession::factory()->count(3)->create([
            'group_id' => $group->id,
            'status' => 'scheduled',
        ]);

        Livewire::test(SessionsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])
            ->selectTableRecords($sessions->pluck('id')->all())
            ->callAction(TestAction::make('markCompleted')->table()->bulk());

        foreach ($sessions as $session) {
            $this->assertSame('completed', $session->refresh()->status);
        }
    }
}
