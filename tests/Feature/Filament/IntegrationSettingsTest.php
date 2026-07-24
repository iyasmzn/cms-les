<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\IntegrationSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IntegrationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'View:IntegrationSettings', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_can_save_turnstile_keys_via_panel(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(IntegrationSettings::class)
            ->fillForm([
                'turnstile_enabled' => true,
                'turnstile_site_key' => '0x4AAAA_site_key',
                'turnstile_secret_key' => '0x4AAAA_secret_key',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue((bool) Setting::get('turnstile_enabled'));
        $this->assertSame('0x4AAAA_site_key', Setting::get('turnstile_site_key'));
        $this->assertSame('0x4AAAA_secret_key', Setting::get('turnstile_secret_key'));
    }

    public function test_secret_key_is_kept_when_left_blank_on_save(): void
    {
        Setting::set('turnstile_secret_key', 'existing-secret');

        $admin = User::factory()->create()->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(IntegrationSettings::class)
            ->fillForm([
                'turnstile_enabled' => true,
                'turnstile_site_key' => 'new-site-key',
                'turnstile_secret_key' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('existing-secret', Setting::get('turnstile_secret_key'));
        $this->assertSame('new-site-key', Setting::get('turnstile_site_key'));
    }
}
