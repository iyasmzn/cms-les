<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ErrorPageSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ErrorPageSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'View:ErrorPageSettings', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_open_the_page(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(ErrorPageSettings::getUrl())
            ->assertSuccessful();
    }

    public function test_author_cannot_open_the_page(): void
    {
        $author = User::factory()->create()->assignRole('author');

        $this->actingAs($author)
            ->get(ErrorPageSettings::getUrl())
            ->assertForbidden();
    }

    public function test_it_persists_error_settings(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(ErrorPageSettings::class)
            ->fillForm([
                'error_404_title' => 'Tidak Ketemu',
                'error_404_message' => 'Silakan periksa kembali alamatnya.',
                'error_show_home_button' => false,
                'error_support_email' => 'help@sekolah.sch.id',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Tidak Ketemu', Setting::get('error_404_title'));
        $this->assertSame('help@sekolah.sch.id', Setting::get('error_support_email'));
        $this->assertFalse((bool) Setting::get('error_show_home_button'));
    }
}
