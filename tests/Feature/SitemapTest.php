<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Teacher;
use App\Services\SitemapBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Render the sitemap through a real request so URLs use the request host.
     */
    private function sitemapXml(): string
    {
        return $this->get(route('sitemap'))->assertOk()->getContent();
    }

    /**
     * Every `<loc>` in the sitemap, reduced to a request path.
     *
     * @return list<string>
     */
    private function sitemapPaths(): array
    {
        preg_match_all('#<loc>(.*?)</loc>#', $this->sitemapXml(), $matches);

        return array_map(
            fn (string $url): string => parse_url(html_entity_decode($url), PHP_URL_PATH) ?: '/',
            $matches[1],
        );
    }

    /**
     * The guard that matters: a listed URL that 404s or redirects is reported as
     * an indexing failure in Search Console, so nothing but a 200 may be listed.
     */
    public function test_every_listed_url_responds_with_200(): void
    {
        Post::factory()->create();
        Institution::factory()->create();
        Institution::factory()->create(['has_groups' => true]);
        Product::factory()->create();

        $paths = $this->sitemapPaths();
        $this->assertNotEmpty($paths);

        $broken = [];

        foreach ($paths as $path) {
            $status = $this->get($path)->getStatusCode();

            if ($status !== 200) {
                $broken[$path] = $status;
            }
        }

        $this->assertSame([], $broken, 'URL sitemap yang tidak menjawab 200: '.json_encode($broken));
    }

    public function test_contact_page_is_listed(): void
    {
        $this->assertContains('/kontak', $this->sitemapPaths());
    }

    public function test_products_are_listed_while_toko_is_enabled(): void
    {
        Setting::set('feature_toko', true);
        $product = Product::factory()->create();

        $paths = $this->sitemapPaths();

        $this->assertContains('/produk', $paths);
        $this->assertContains("/produk/{$product->slug}", $paths);
    }

    public function test_products_are_omitted_once_toko_is_disabled(): void
    {
        $product = Product::factory()->create();
        Setting::set('feature_toko', false);

        $paths = $this->sitemapPaths();

        $this->assertNotContains('/produk', $paths);
        $this->assertNotContains("/produk/{$product->slug}", $paths);
    }

    public function test_unavailable_product_is_omitted(): void
    {
        Setting::set('feature_toko', true);
        $product = Product::factory()->create(['is_available' => false]);

        $this->assertNotContains("/produk/{$product->slug}", $this->sitemapPaths());
    }

    /**
     * `/ppdb` redirects to the only jenjang when just one is active, so listing
     * it would put a redirecting URL in the sitemap.
     */
    public function test_ppdb_index_is_omitted_when_a_single_jenjang_is_active(): void
    {
        $institution = Institution::factory()->create();

        $paths = $this->sitemapPaths();

        $this->assertNotContains('/ppdb', $paths);
        $this->assertContains("/ppdb/{$institution->slug}", $paths);
    }

    public function test_ppdb_index_is_listed_when_several_jenjang_are_active(): void
    {
        $first = Institution::factory()->create();
        $second = Institution::factory()->create();

        $paths = $this->sitemapPaths();

        $this->assertContains('/ppdb', $paths);
        $this->assertContains("/ppdb/{$first->slug}", $paths);
        $this->assertContains("/ppdb/{$second->slug}", $paths);
    }

    /**
     * A `has_groups` jenjang registers through the group flow, so `ppdb.show`
     * redirects it to `courses.show`. Only the course URL may be listed.
     */
    public function test_ppdb_page_is_omitted_for_a_jenjang_that_has_groups(): void
    {
        $institution = Institution::factory()->create(['has_groups' => true]);

        $this->get(route('ppdb.show', $institution->slug))
            ->assertRedirect(route('courses.show', $institution->slug));

        $paths = $this->sitemapPaths();

        $this->assertNotContains("/ppdb/{$institution->slug}", $paths);
        $this->assertContains("/courses/{$institution->slug}", $paths);
    }

    public function test_courses_index_is_always_listed(): void
    {
        $this->assertContains('/courses', $this->sitemapPaths());
    }

    public function test_course_page_is_listed_for_a_jenjang_that_has_groups(): void
    {
        $institution = Institution::factory()->create(['has_groups' => true]);

        $this->assertContains("/courses/{$institution->slug}", $this->sitemapPaths());
    }

    /**
     * `courses.show` aborts with 404 unless the jenjang has groups, so listing
     * one without them would hand Search Console a broken URL.
     */
    public function test_course_page_is_omitted_for_a_jenjang_without_groups(): void
    {
        $institution = Institution::factory()->create(['has_groups' => false]);

        $this->get(route('courses.show', $institution->slug))->assertNotFound();
        $this->assertNotContains("/courses/{$institution->slug}", $this->sitemapPaths());
    }

    public function test_course_page_is_omitted_for_an_inactive_jenjang(): void
    {
        $institution = Institution::factory()->inactive()->create(['has_groups' => true]);

        $this->assertNotContains("/courses/{$institution->slug}", $this->sitemapPaths());
    }

    public function test_inactive_jenjang_is_omitted(): void
    {
        $institution = Institution::factory()->inactive()->create();

        $this->assertNotContains("/ppdb/{$institution->slug}", $this->sitemapPaths());
    }

    /**
     * Individual staff profiles are deliberately kept out of search results.
     */
    public function test_teacher_detail_pages_are_not_listed(): void
    {
        $teacher = Teacher::factory()->create();

        $paths = $this->sitemapPaths();

        $this->assertContains('/guru', $paths);
        $this->assertNotContains("/guru/{$teacher->getKey()}", $paths);
    }

    public function test_unpublished_post_is_omitted(): void
    {
        $post = Post::factory()->draft()->create();

        $this->assertNotContains("/blog/{$post->slug}", $this->sitemapPaths());
    }

    public function test_saving_content_invalidates_the_cached_sitemap(): void
    {
        $this->sitemapXml();
        $this->assertTrue(Cache::has(SitemapBuilder::CACHE_KEY));

        Post::factory()->create();

        $this->assertFalse(Cache::has(SitemapBuilder::CACHE_KEY));
    }

    public function test_saving_a_jenjang_invalidates_the_cached_sitemap(): void
    {
        $this->sitemapXml();

        Institution::factory()->create();

        $this->assertFalse(Cache::has(SitemapBuilder::CACHE_KEY));
    }

    public function test_saving_a_product_invalidates_the_cached_sitemap(): void
    {
        $this->sitemapXml();

        Product::factory()->create();

        $this->assertFalse(Cache::has(SitemapBuilder::CACHE_KEY));
    }

    /**
     * Without this, disabling a feature would leave the cached XML advertising
     * URLs that now 404, with no content change to trigger a rebuild.
     */
    public function test_changing_a_setting_invalidates_the_cached_sitemap(): void
    {
        $this->sitemapXml();

        Setting::set('feature_toko', false);

        $this->assertFalse(Cache::has(SitemapBuilder::CACHE_KEY));
    }

    public function test_disabling_toko_refreshes_the_served_sitemap(): void
    {
        $this->assertContains('/produk', $this->sitemapPaths());

        Setting::set('feature_toko', false);

        $this->assertNotContains('/produk', $this->sitemapPaths());
    }

    public function test_robots_txt_points_at_an_absolute_sitemap_url(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Sitemap: '.route('sitemap'), escape: false);
        $this->assertStringContainsString('http', route('sitemap'));
    }

    public function test_robots_txt_keeps_crawlers_off_private_and_file_routes(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Disallow: /unduhan/*/download', escape: false)
            ->assertSee('Disallow: /my-courses', escape: false);
    }
}
