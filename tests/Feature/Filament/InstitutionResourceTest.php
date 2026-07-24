<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Institutions\Pages\CreateInstitution;
use App\Filament\Resources\Institutions\Pages\EditInstitution;
use App\Filament\Resources\Institutions\Pages\ListInstitutions;
use App\Filament\Resources\Institutions\RelationManagers\PpdbFieldsRelationManager;
use App\Models\Institution;
use App\Models\PpdbField;
use App\Models\User;
use App\Policies\InstitutionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InstitutionResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->grantInstitutionPermissions($user);
        $this->actingAs($user);
    }

    /**
     * Grant the Shield permissions the InstitutionResource pages require.
     */
    private function grantInstitutionPermissions(User $user): void
    {
        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny', 'Reorder'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:Institution", 'web'));

        $user->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_it_lists_institutions(): void
    {
        $institutions = Institution::factory()->count(3)->create();

        Livewire::test(ListInstitutions::class)
            ->assertCanSeeTableRecords($institutions);
    }

    public function test_it_creates_an_institution(): void
    {
        Livewire::test(CreateInstitution::class)
            ->fillForm([
                'name' => 'SMA',
                'slug' => 'sma',
                'short_name' => 'SMA',
                'color' => 'warning',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Institution::class, [
            'name' => 'SMA',
            'slug' => 'sma',
            'short_name' => 'SMA',
            'is_active' => true,
        ]);
    }

    public function test_it_creates_an_external_link_jenjang(): void
    {
        Livewire::test(CreateInstitution::class)
            ->fillForm([
                'name' => 'SMK',
                'slug' => 'smk',
                'form_mode' => Institution::FORM_MODE_EXTERNAL_LINK,
                'external_url' => 'https://ppdb.smk.example',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Institution::class, [
            'slug' => 'smk',
            'form_mode' => Institution::FORM_MODE_EXTERNAL_LINK,
            'external_url' => 'https://ppdb.smk.example',
        ]);
    }

    public function test_it_requires_a_name_and_slug(): void
    {
        Livewire::test(CreateInstitution::class)
            ->fillForm(['name' => null, 'slug' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'slug' => 'required']);
    }

    public function test_it_rejects_a_duplicate_slug(): void
    {
        Institution::factory()->create(['slug' => 'sd']);

        Livewire::test(CreateInstitution::class)
            ->fillForm(['name' => 'SD Lain', 'slug' => 'sd'])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_it_updates_an_institution(): void
    {
        $institution = Institution::factory()->create();

        Livewire::test(EditInstitution::class, ['record' => $institution->id])
            ->fillForm(['name' => 'Nama Jenjang Baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Institution::class, [
            'id' => $institution->id,
            'name' => 'Nama Jenjang Baru',
        ]);
    }

    public function test_field_builder_is_only_available_for_internal_jenjang(): void
    {
        $internal = Institution::factory()->create();
        $external = Institution::factory()->externalLink()->create();

        $this->assertTrue(PpdbFieldsRelationManager::canViewForRecord($internal, EditInstitution::class));
        $this->assertFalse(PpdbFieldsRelationManager::canViewForRecord($external, EditInstitution::class));
    }

    public function test_field_builder_is_not_blocked_by_a_phantom_permission(): void
    {
        // PpdbField has no dedicated policy (like the RegistrationWave relation
        // manager), so the field builder follows Institution edit access rather
        // than an ungenerated *:PpdbField permission that Shield never creates.
        $this->assertNull(Gate::getPolicyFor(PpdbField::class));
        $this->assertInstanceOf(InstitutionPolicy::class, Gate::getPolicyFor(Institution::class));
    }
}
