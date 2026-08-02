<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create();

        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:User", 'web'));

        $admin->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin);
    }

    public function test_the_verification_toggle_reflects_an_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertFormSet(['email_verified_at' => false]);
    }

    public function test_the_verification_toggle_reflects_a_verified_user(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertFormSet(['email_verified_at' => true]);
    }

    public function test_it_can_unverify_an_existing_user(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['email_verified_at' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_it_can_verify_an_existing_user(): void
    {
        $user = User::factory()->unverified()->create();

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['email_verified_at' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_it_keeps_verification_untouched_when_editing_other_fields(): void
    {
        $user = User::factory()->create();
        $verifiedAt = $user->email_verified_at;

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['name' => 'Nama Baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($verifiedAt->equalTo($user->email_verified_at));
    }

    public function test_it_can_create_a_verified_user(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Budi Santoso',
                'email' => 'budi@example.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'email_verified_at' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNotNull(User::where('email', 'budi@example.test')->sole()->email_verified_at);
    }

    public function test_it_can_create_an_unverified_user(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Siti Aminah',
                'email' => 'siti@example.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'email_verified_at' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNull(User::where('email', 'siti@example.test')->sole()->email_verified_at);
    }
}
