<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\SpmbRegistrations\Pages\EditSpmbRegistration;
use App\Models\SpmbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SpmbRegistrationResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->grantSpmbPermissions($this->user);

        $this->actingAs($this->user);
    }

    /**
     * Grant the Shield permissions the SpmbRegistrationResource pages require.
     * In production these are seeded and synced onto the super_admin role.
     */
    private function grantSpmbPermissions(User $user): void
    {
        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:SpmbRegistration", 'web'));

        $user->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Regression: a 16-digit NIK must round-trip as an exact string. Casting
     * it to a float turned it into scientific notation (e.g. 2.97E+15), which
     * both corrupted the value and overflowed the VARCHAR(16) column.
     */
    public function test_editing_preserves_full_sixteen_digit_nik(): void
    {
        $registration = SpmbRegistration::factory()->pending()->create([
            'nik' => '2975378084746545',
        ]);

        Livewire::test(EditSpmbRegistration::class, ['record' => $registration->id])
            ->fillForm(['status' => 'verified'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(SpmbRegistration::class, [
            'id' => $registration->id,
            'nik' => '2975378084746545',
            'status' => 'verified',
        ]);
    }

    public function test_editing_rejects_nik_that_is_not_sixteen_digits(): void
    {
        $registration = SpmbRegistration::factory()->create();

        Livewire::test(EditSpmbRegistration::class, ['record' => $registration->id])
            ->fillForm(['nik' => '12345'])
            ->call('save')
            ->assertHasFormErrors(['nik']);
    }
}
