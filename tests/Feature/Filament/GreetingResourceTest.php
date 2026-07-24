<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Greetings\Pages\CreateGreeting;
use App\Filament\Resources\Greetings\Pages\EditGreeting;
use App\Filament\Resources\Greetings\Pages\ListGreetings;
use App\Models\Greeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GreetingResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // ── List ──────────────────────────────────────────────────────

    public function test_list_page_can_render(): void
    {
        $greetings = Greeting::factory()->count(3)->create();

        Livewire::test(ListGreetings::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($greetings);
    }

    public function test_list_page_can_search(): void
    {
        $visible = Greeting::factory()->create(['name' => 'Budi Santoso']);
        $hidden = Greeting::factory()->create(['name' => 'Ani Lestari']);

        Livewire::test(ListGreetings::class)
            ->searchTable('Budi')
            ->assertCanSeeTableRecords([$visible])
            ->assertCanNotSeeTableRecords([$hidden]);
    }

    // ── Create ────────────────────────────────────────────────────

    public function test_create_page_can_render(): void
    {
        Livewire::test(CreateGreeting::class)
            ->assertSuccessful();
    }

    public function test_can_create_greeting(): void
    {
        Livewire::test(CreateGreeting::class)
            ->fillForm([
                'name' => 'KH. Ahmad Dahlan',
                'position' => 'Kepala Yayasan',
                'excerpt' => 'Kami berkomitmen memberikan pendidikan terbaik untuk generasi Qurani.',
                'is_published' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $this->assertDatabaseHas(Greeting::class, [
            'name' => 'KH. Ahmad Dahlan',
            'position' => 'Kepala Yayasan',
            'is_published' => true,
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        Livewire::test(CreateGreeting::class)
            ->fillForm([
                'name' => null,
                'position' => null,
                'excerpt' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'position' => 'required',
                'excerpt' => 'required',
            ])
            ->assertNotNotified();
    }

    // ── Edit ──────────────────────────────────────────────────────

    public function test_edit_page_can_render(): void
    {
        $greeting = Greeting::factory()->create();

        Livewire::test(EditGreeting::class, ['record' => $greeting->id])
            ->assertSuccessful()
            ->assertFormSet([
                'name' => $greeting->name,
                'excerpt' => $greeting->excerpt,
            ]);
    }

    public function test_can_edit_greeting(): void
    {
        $greeting = Greeting::factory()->create();

        Livewire::test(EditGreeting::class, ['record' => $greeting->id])
            ->fillForm(['name' => 'Nama Diperbarui', 'is_published' => false])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Greeting::class, [
            'id' => $greeting->id,
            'name' => 'Nama Diperbarui',
            'is_published' => false,
        ]);
    }

    // ── Model / Factory ───────────────────────────────────────────

    public function test_factory_creates_valid_records(): void
    {
        $greeting = Greeting::factory()->create();

        $this->assertDatabaseHas(Greeting::class, ['id' => $greeting->id]);
        $this->assertNotEmpty($greeting->name);
        $this->assertNotEmpty($greeting->position);
    }

    public function test_published_scope_returns_only_published(): void
    {
        Greeting::factory()->published()->count(2)->create();
        Greeting::factory()->create(['is_published' => false]);

        $this->assertCount(2, Greeting::published()->get());
    }

    public function test_photo_url_returns_fallback_when_no_photo(): void
    {
        $greeting = Greeting::factory()->create(['photo' => null]);

        $this->assertStringContainsString('ui-avatars.com', $greeting->photo_url);
    }

    // ── Homepage ──────────────────────────────────────────────────

    public function test_homepage_shows_published_greetings(): void
    {
        $published = Greeting::factory()->published()->create(['name' => 'KH. Tokoh Tampil']);
        $hidden = Greeting::factory()->create(['is_published' => false, 'name' => 'Tokoh Disembunyikan']);

        $this->get('/')
            ->assertOk()
            ->assertSee($published->name)
            ->assertDontSee($hidden->name);
    }
}
