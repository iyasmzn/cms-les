<?php

namespace Tests\Feature;

use App\Filament\Auth\EditProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_renders_the_professional_sections(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertOk()
            ->assertSee('Informasi Akun')
            ->assertSee('Detail Akun')
            ->assertSee('Keamanan')
            ->assertSee('Bergabung Sejak')
            ->assertSee('Metode Masuk');
    }

    public function test_profile_name_can_be_updated(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);
        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->fillForm(['name' => 'Nama Baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'name' => 'Nama Baru',
        ]);
    }
}
