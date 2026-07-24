<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TestimonialResourceTest extends TestCase
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
        $testimonials = Testimonial::factory()->count(3)->create();

        Livewire::test(ListTestimonials::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($testimonials);
    }

    public function test_list_page_can_search(): void
    {
        $visible = Testimonial::factory()->create(['name' => 'Budi Santoso']);
        $hidden = Testimonial::factory()->create(['name' => 'Ani Lestari']);

        Livewire::test(ListTestimonials::class)
            ->searchTable('Budi')
            ->assertCanSeeTableRecords([$visible])
            ->assertCanNotSeeTableRecords([$hidden]);
    }

    // ── Create ────────────────────────────────────────────────────

    public function test_create_page_can_render(): void
    {
        Livewire::test(CreateTestimonial::class)
            ->assertSuccessful();
    }

    public function test_can_create_testimonial(): void
    {
        Livewire::test(CreateTestimonial::class)
            ->fillForm([
                'name' => 'Dewi Rahayu',
                'class_year' => '2020',
                'graduation_year' => '2023',
                'message' => 'Sekolah ini luar biasa dan telah membentuk karakter saya.',
                'is_published' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $this->assertDatabaseHas(Testimonial::class, [
            'name' => 'Dewi Rahayu',
            'class_year' => '2020',
            'graduation_year' => '2023',
            'is_published' => true,
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        Livewire::test(CreateTestimonial::class)
            ->fillForm([
                'name' => null,
                'message' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'message' => 'required',
            ])
            ->assertNotNotified();
    }

    // ── Edit ──────────────────────────────────────────────────────

    public function test_edit_page_can_render(): void
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->id])
            ->assertSuccessful()
            ->assertFormSet([
                'name' => $testimonial->name,
                'message' => $testimonial->message,
            ]);
    }

    public function test_can_edit_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->id])
            ->fillForm(['name' => 'Nama Diperbarui', 'is_published' => false])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Testimonial::class, [
            'id' => $testimonial->id,
            'name' => 'Nama Diperbarui',
            'is_published' => false,
        ]);
    }

    // ── Model / Factory ───────────────────────────────────────────

    public function test_factory_creates_valid_records(): void
    {
        $testimonial = Testimonial::factory()->create();

        $this->assertDatabaseHas(Testimonial::class, ['id' => $testimonial->id]);
        $this->assertNotEmpty($testimonial->name);
        $this->assertNotEmpty($testimonial->message);
    }

    public function test_published_scope_returns_only_published(): void
    {
        Testimonial::factory()->create(['is_published' => true]);
        Testimonial::factory()->create(['is_published' => true]);
        Testimonial::factory()->create(['is_published' => false]);

        $this->assertCount(2, Testimonial::published()->get());
    }

    public function test_photo_url_returns_fallback_when_no_photo(): void
    {
        $testimonial = Testimonial::factory()->create(['photo' => null]);

        $this->assertStringContainsString('ui-avatars.com', $testimonial->photo_url);
    }
}
