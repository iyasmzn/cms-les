<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TeacherResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['ViewAny:Teacher', 'View:Teacher', 'Create:Teacher', 'Update:Teacher'] as $name) {
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

    public function test_position_options_come_from_active_teacher_categories(): void
    {
        Category::create(['type' => Category::TYPE_TEACHER, 'name' => 'Guru Fisika', 'sort_order' => 1, 'is_active' => true]);
        Category::create(['type' => Category::TYPE_TEACHER, 'name' => 'Jabatan Nonaktif', 'sort_order' => 2, 'is_active' => false]);
        Category::create(['type' => Category::TYPE_POST, 'name' => 'Berita', 'sort_order' => 1, 'is_active' => true]);

        $options = Category::optionsForType(Category::TYPE_TEACHER);

        $this->assertArrayHasKey('Guru Fisika', $options);
        $this->assertArrayNotHasKey('Jabatan Nonaktif', $options); // inactive excluded
        $this->assertArrayNotHasKey('Berita', $options); // other type excluded
    }

    public function test_teacher_can_be_created_with_a_category_position(): void
    {
        Category::create(['type' => Category::TYPE_TEACHER, 'name' => 'Guru Fisika', 'sort_order' => 1, 'is_active' => true]);

        $this->actingAs($this->admin());

        Livewire::test(CreateTeacher::class)
            ->fillForm([
                'name' => 'Budi Santoso',
                'position' => 'Guru Fisika',
                'sort_order' => 0,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Teacher::class, [
            'name' => 'Budi Santoso',
            'position' => 'Guru Fisika',
        ]);
    }

    public function test_editing_teacher_keeps_position_not_registered_as_category(): void
    {
        // Simulates a teacher imported with a jabatan that has no matching category.
        $teacher = Teacher::factory()->create(['position' => 'Jabatan Import Aneh']);

        $this->actingAs($this->admin());

        Livewire::test(EditTeacher::class, ['record' => $teacher->id])
            ->assertSuccessful()
            ->assertFormSet(['position' => 'Jabatan Import Aneh'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Teacher::class, [
            'id' => $teacher->id,
            'position' => 'Jabatan Import Aneh',
        ]);
    }

    public function test_seeder_registers_existing_teacher_positions_as_categories(): void
    {
        // A jabatan not part of the default list must still become a category.
        Teacher::factory()->create(['position' => 'Jabatan Unik XYZ']);

        (new CategorySeeder)->run();

        $this->assertDatabaseHas(Category::class, [
            'type' => Category::TYPE_TEACHER,
            'name' => 'Jabatan Unik XYZ',
        ]);

        // Default jabatan seeded too.
        $this->assertDatabaseHas(Category::class, [
            'type' => Category::TYPE_TEACHER,
            'name' => 'Kepala Sekolah',
        ]);
    }
}
