<?php

namespace Tests\Feature;

use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ppdb_page_uses_custom_title_and_subtitle(): void
    {
        $institution = Institution::factory()->create([
            'slug' => 'custom-unit',
            'page_title' => 'Welcome To Our Special Unit',
            'page_subtitle' => 'A uniquely worded introduction paragraph.',
        ]);

        $this->get(route('ppdb.show', $institution))
            ->assertStatus(200)
            ->assertSee('Welcome To Our Special Unit')
            ->assertSee('A uniquely worded introduction paragraph.');
    }

    public function test_ppdb_page_redirects_course_institutions_to_the_courses_page(): void
    {
        $course = Institution::factory()->create([
            'slug' => 'swim-course-unit',
            'has_groups' => true,
        ]);

        $this->get(route('ppdb.show', $course))
            ->assertRedirect(route('courses.show', $course));
    }

    public function test_ppdb_page_renders_content_blocks(): void
    {
        $institution = Institution::factory()->create([
            'slug' => 'blocks-unit',
            'blocks' => [
                ['type' => 'cta_button', 'label' => 'Download Brochure Here', 'url' => 'https://example.com/brochure'],
            ],
        ]);

        $this->get(route('ppdb.show', $institution))
            ->assertStatus(200)
            ->assertSee('Download Brochure Here')
            ->assertSee('https://example.com/brochure');
    }

    public function test_ppdb_page_falls_back_to_default_title(): void
    {
        $institution = Institution::factory()->create([
            'short_name' => 'ZZQ',
            'page_title' => null,
            'page_subtitle' => null,
        ]);

        $this->get(route('ppdb.show', $institution))
            ->assertStatus(200)
            ->assertSee('PPDB ZZQ');
    }
}
