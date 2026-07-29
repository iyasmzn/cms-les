<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Groups\Pages\EditGroup;
use App\Filament\Resources\Groups\Pages\ListGroups;
use App\Filament\Resources\Groups\RelationManagers\SessionsRelationManager;
use App\Filament\Resources\Institutions\Pages\EditInstitution;
use App\Filament\Resources\Institutions\RelationManagers\GroupsRelationManager;
use App\Models\Group;
use App\Models\GroupSession;
use App\Models\Institution;
use App\Models\User;
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
}
