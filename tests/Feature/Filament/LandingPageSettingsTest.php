<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\LandingPageSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LandingPageSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'View:LandingPageSettings', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_open_the_page(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(LandingPageSettings::getUrl())
            ->assertSuccessful();
    }

    public function test_author_cannot_open_the_page(): void
    {
        $author = User::factory()->create()->assignRole('author');

        $this->actingAs($author)
            ->get(LandingPageSettings::getUrl())
            ->assertForbidden();
    }

    public function test_it_persists_section_content_and_seo_settings(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(LandingPageSettings::class)
            ->fillForm([
                'home_meta_title' => 'Pesantren Modern Terbaik',
                'home_meta_description' => 'Deskripsi meta khusus halaman depan.',
                'section_programs_eyebrow' => 'Program Kami',
                'section_programs_title' => 'Program Andalan Pesantren',
                'section_programs_subtitle' => 'Deskripsi program yang sudah diubah.',
                'section_donasi_title' => 'Mari Berdonasi',
                'section_donasi_title_highlight' => 'Untuk Santri Berprestasi',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Pesantren Modern Terbaik', Setting::get('home_meta_title'));
        $this->assertSame('Deskripsi meta khusus halaman depan.', Setting::get('home_meta_description'));
        $this->assertSame('Program Kami', Setting::get('section_programs_eyebrow'));
        $this->assertSame('Program Andalan Pesantren', Setting::get('section_programs_title'));
        $this->assertSame('Deskripsi program yang sudah diubah.', Setting::get('section_programs_subtitle'));
        $this->assertSame('Mari Berdonasi', Setting::get('section_donasi_title'));
        $this->assertSame('Untuk Santri Berprestasi', Setting::get('section_donasi_title_highlight'));
    }

    public function test_it_keeps_section_order_and_visibility_when_saving_content(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(LandingPageSettings::class)
            ->fillForm(['section_blog_title' => 'Kabar Terbaru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $order = json_decode(Setting::get('section_order'), true);

        $this->assertIsArray($order);
        $this->assertContains('section_programs', array_column($order, 'key'));
        $this->assertContains('section_blog', array_column($order, 'key'));
    }
}
