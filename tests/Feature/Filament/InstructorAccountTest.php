<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Teachers\Pages\ViewTeacher;
use App\Models\Teacher;
use App\Models\User;
use App\Services\InstructorAccountService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InstructorAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'ViewAny:Teacher', 'View:Teacher', 'Create:Teacher', 'Update:Teacher',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User',
        ] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function admin(): User
    {
        return User::factory()->create()->assignRole('super_admin');
    }

    public function test_view_page_shows_teacher_data(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@sekolah.test']);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('budi@sekolah.test');
    }

    public function test_create_account_action_provisions_user_with_default_password(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@sekolah.test']);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->assertActionVisible('createAccount')
            ->callAction('createAccount', ['email' => 'budi@sekolah.test'])
            ->assertHasNoActionErrors();

        $user = User::whereEmail('budi@sekolah.test')->firstOrFail();

        $this->assertSame('Budi Santoso', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check(
            app(InstructorAccountService::class)->defaultPassword(),
            $user->password,
        ));

        // Roles yang dibutuhkan agar bisa login ke panel sebagai instruktur.
        $this->assertTrue($user->hasRole('instructor'));
        $this->assertTrue($user->hasRole('panel_user'));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));

        $this->assertSame($user->id, $teacher->fresh()->user_id);
    }

    public function test_create_account_action_uses_password_when_filled(): void
    {
        $teacher = Teacher::factory()->create(['email' => 'sari@sekolah.test']);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->callAction('createAccount', [
                'email' => 'sari@sekolah.test',
                'password' => 'rahasia12345',
            ])
            ->assertHasNoActionErrors();

        $user = User::whereEmail('sari@sekolah.test')->firstOrFail();

        $this->assertTrue(Hash::check('rahasia12345', $user->password));
    }

    public function test_create_account_action_links_existing_user_instead_of_duplicating(): void
    {
        $existing = User::factory()->create(['email' => 'lama@sekolah.test']);
        $teacher = Teacher::factory()->create(['email' => 'lama@sekolah.test']);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->callAction('createAccount', ['email' => 'lama@sekolah.test'])
            ->assertHasNoActionErrors();

        $this->assertSame(1, User::whereEmail('lama@sekolah.test')->count());
        $this->assertSame($existing->id, $teacher->fresh()->user_id);
        $this->assertTrue($existing->fresh()->hasRole('instructor'));
    }

    public function test_create_account_action_rejects_email_linked_to_another_teacher(): void
    {
        $taken = User::factory()->create(['email' => 'dipakai@sekolah.test']);
        Teacher::factory()->create(['user_id' => $taken->id]);

        $teacher = Teacher::factory()->create(['email' => null]);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->callAction('createAccount', ['email' => 'dipakai@sekolah.test'])
            ->assertHasActionErrors(['email']);

        $this->assertNull($teacher->fresh()->user_id);
    }

    public function test_create_account_action_hidden_when_teacher_already_has_account(): void
    {
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->assertActionHidden('createAccount')
            ->assertActionVisible('resetPassword');
    }

    public function test_reset_password_action_sets_default_password_when_left_empty(): void
    {
        $user = User::factory()->create(['password' => 'passwordlama']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);

        $this->actingAs($this->admin());

        Livewire::test(ViewTeacher::class, ['record' => $teacher->id])
            ->callAction('resetPassword')
            ->assertHasNoActionErrors();

        $this->assertTrue(Hash::check(
            app(InstructorAccountService::class)->defaultPassword(),
            $user->fresh()->password,
        ));
    }

    public function test_granting_roles_creates_them_when_missing(): void
    {
        Role::query()->whereIn('name', InstructorAccountService::ROLES)->delete();

        $user = User::factory()->create();

        app(InstructorAccountService::class)->grantInstructorRoles($user);

        $this->assertTrue($user->hasRole('instructor'));
        $this->assertTrue($user->hasRole('panel_user'));
    }
}
