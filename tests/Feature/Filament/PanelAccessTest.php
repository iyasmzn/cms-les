<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\GeneralSettings;
use App\Filament\Widgets\SpmbRegistrationsChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post',
            'View:GeneralSettings', 'View:Dashboard',
            'View:StatsOverviewWidget', 'View:SpmbRegistrationsChartWidget',
        ] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web'])
            ->syncPermissions(['ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post']);

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_author_sees_dashboard_greeting_but_no_data_widgets(): void
    {
        $author = User::factory()->create(['name' => 'Ustadz Penulis'])->assignRole('author');

        $this->actingAs($author)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Ustadz Penulis')   // greeting card
            ->assertDontSee('Total Postingan'); // stats widget is permission-gated
    }

    public function test_author_cannot_open_settings_page(): void
    {
        $author = User::factory()->create()->assignRole('author');

        $this->actingAs($author)
            ->get(GeneralSettings::getUrl())
            ->assertForbidden();
    }

    public function test_super_admin_can_open_dashboard(): void
    {
        $admin = User::factory()->create()->assignRole('super_admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    }

    public function test_basic_panel_user_sees_greeting_card(): void
    {
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create(['name' => 'Pak Dashboard'])->assignRole('panel_user');

        $this->actingAs($user)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Pak Dashboard');
    }

    public function test_widgets_respect_view_permission(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $limited = User::factory()->create();
        $limited->givePermissionTo(['View:Dashboard', 'View:StatsOverviewWidget']);

        $this->actingAs($limited);

        $this->assertTrue(StatsOverviewWidget::canView());
        $this->assertFalse(SpmbRegistrationsChartWidget::canView());
    }
}
