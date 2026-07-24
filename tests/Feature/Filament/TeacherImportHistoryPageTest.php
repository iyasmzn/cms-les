<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\TeacherImportHistoryPage;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Models\User;
use App\Services\TeacherImportHistory;
use App\Services\TeacherImportResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TeacherImportHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        Permission::findOrCreate('View:TeacherImportHistoryPage', 'web');
        Permission::findOrCreate('ViewAny:Teacher', 'web');

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());
        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::factory()->create()->assignRole('super_admin'));
    }

    public function test_teacher_list_page_renders_with_import_actions(): void
    {
        Livewire::test(ListTeachers::class)
            ->assertSuccessful()
            ->assertActionExists('import')
            ->assertActionExists('export')
            ->assertActionExists('downloadTemplate')
            ->assertActionExists('history');
    }

    /**
     * Full-page HTTP render exercises the sidebar, which enforces the Filament
     * rule that a navigation group and its items cannot both declare icons.
     */
    public function test_panel_pages_render_full_layout_including_sidebar(): void
    {
        $this->get(ListTeachers::getUrl())->assertSuccessful();
        $this->get(TeacherImportHistoryPage::getUrl())->assertSuccessful();
    }

    public function test_user_without_permission_cannot_open_history_page(): void
    {
        // An author can reach the panel but lacks View:TeacherImportHistoryPage.
        $this->actingAs(User::factory()->create()->assignRole('author'));

        $this->get(TeacherImportHistoryPage::getUrl())->assertForbidden();
    }

    public function test_page_renders_with_volatility_warning(): void
    {
        Livewire::test(TeacherImportHistoryPage::class)
            ->assertSuccessful()
            ->assertSee('tidak disimpan di database')
            ->assertSee('Belum ada riwayat import');
    }

    public function test_page_shows_recorded_imported_and_failed_rows(): void
    {
        (new TeacherImportHistory)->record(
            new TeacherImportResult(
                created: 1,
                imported: [
                    ['row' => 2, 'action' => 'created', 'attributes' => ['name' => 'Ahmad Fauzi', 'nip' => '1990', 'is_active' => true]],
                ],
                failures: [
                    ['row' => 3, 'reason' => 'Kolom Nama wajib diisi.', 'attributes' => ['name' => null]],
                ],
            ),
            'data-guru.xlsx',
        );

        Livewire::test(TeacherImportHistoryPage::class)
            ->assertSuccessful()
            ->assertSee('data-guru.xlsx')
            ->assertSee('Ahmad Fauzi')
            ->assertSee('Kolom Nama wajib diisi.');
    }

    public function test_clear_action_removes_history(): void
    {
        $history = new TeacherImportHistory;
        $history->record(new TeacherImportResult(created: 1), 'data.xlsx');

        Livewire::test(TeacherImportHistoryPage::class)
            ->callAction('clear')
            ->assertNotified();

        $this->assertSame([], $history->all());
    }
}
