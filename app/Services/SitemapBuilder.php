<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Product;
use App\Models\Program;
use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapBuilder
{
    /**
     * Cache key for the rendered sitemap XML. Invalidated whenever content
     * that appears in the sitemap is created, updated, or deleted.
     */
    public const CACHE_KEY = 'sitemap.xml';

    /**
     * Active jenjang, resolved once per build since both the index-page
     * condition and the per-jenjang URLs need them.
     *
     * @var Collection<int, Institution>|null
     */
    private ?Collection $activeInstitutions = null;

    /**
     * Jenjang whose course page is reachable, and jenjang whose PPDB page is.
     *
     * The two sets are disjoint by design: `has_groups` jenjang register through
     * the group flow, so `ppdb.show` redirects them to `courses.show`, while
     * `courses.show` aborts with 404 for jenjang without groups. Listing either
     * URL for the wrong set would hand Search Console a redirect or a 404.
     *
     * @var Collection<int, Institution>|null
     */
    private ?Collection $courseInstitutions = null;

    /** @var Collection<int, Institution>|null */
    private ?Collection $ppdbInstitutions = null;

    /**
     * Build the sitemap from the current published content.
     *
     * Only URLs that answer 200 belong here: a listed URL that 404s or
     * redirects is reported as an indexing error in Search Console. That rules
     * out pages behind a disabled feature flag (the `feature` middleware aborts
     * with 404) and pages that redirect elsewhere.
     */
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        /* ── Homepage ── */
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'));

        /* ── Static index pages ── */
        foreach ($this->indexPages() as $page) {
            try {
                $sitemap->add(
                    Url::create(route($page['route']))
                        ->setPriority($page['priority'])
                        ->setChangeFrequency($page['freq'])
                );
            } catch (\Throwable $e) {
                Log::warning('Sitemap: halaman indeks dilewati karena route gagal di-resolve.', [
                    'route' => $page['route'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        /* ── Blog posts ── */
        Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->each(function (Post $post) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('blog.show', $post->slug))
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.7)
                        ->setChangeFrequency('monthly')
                );
            });

        /* ── Events ── */
        Event::where('is_published', true)
            ->each(function (Event $event) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('events.show', $event->slug))
                        ->setLastModificationDate($event->updated_at)
                        ->setPriority(0.7)
                        ->setChangeFrequency('monthly')
                );
            });

        /* ── Programs ── */
        Program::where('is_published', true)
            ->each(function (Program $program) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('programs.show', $program->slug))
                        ->setLastModificationDate($program->updated_at)
                        ->setPriority(0.6)
                        ->setChangeFrequency('monthly')
                );
            });

        /* ── Static pages ── */
        StaticPage::where('is_active', true)
            ->each(function (StaticPage $page) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('page.show', $page->slug))
                        ->setLastModificationDate($page->updated_at)
                        ->setPriority(0.5)
                        ->setChangeFrequency('monthly')
                );
            });

        /* ── PPDB per jenjang (yang tidak memakai alur grup) ── */
        foreach ($this->ppdbInstitutions() as $institution) {
            $sitemap->add(
                Url::create(route('ppdb.show', $institution->slug))
                    ->setLastModificationDate($institution->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
            );
        }

        /* ── Courses per jenjang ── */
        foreach ($this->courseInstitutions() as $institution) {
            $sitemap->add(
                Url::create(route('courses.show', $institution->slug))
                    ->setLastModificationDate($institution->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('weekly')
            );
        }

        /* ── Produk (hanya saat fitur toko aktif) ── */
        if (feature_enabled('toko')) {
            Product::query()->available()->each(function (Product $product) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('products.show', $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setPriority(0.5)
                        ->setChangeFrequency('weekly')
                );
            });
        }

        /*
         * Teacher detail pages (`/guru/{teacher}`) are intentionally omitted:
         * individual staff profiles are not meant to rank in search. The
         * `/guru` index above is enough for discovery.
         */

        return $sitemap;
    }

    /**
     * Index pages that are currently reachable, each with its crawl hints.
     *
     * @return list<array{route: string, priority: float, freq: string}>
     */
    private function indexPages(): array
    {
        $pages = [
            ['route' => 'blog.index',      'priority' => 0.8, 'freq' => 'daily'],
            ['route' => 'events.index',    'priority' => 0.8, 'freq' => 'weekly'],
            ['route' => 'programs.index',  'priority' => 0.7, 'freq' => 'monthly'],
            ['route' => 'teachers.index',  'priority' => 0.6, 'freq' => 'monthly'],
            ['route' => 'downloads.index', 'priority' => 0.6, 'freq' => 'weekly'],
            ['route' => 'gallery.index',   'priority' => 0.5, 'freq' => 'weekly'],
            ['route' => 'contact.index',   'priority' => 0.5, 'freq' => 'monthly'],
            ['route' => 'courses.index',   'priority' => 0.8, 'freq' => 'weekly'],
        ];

        /*
         * `/ppdb` redirects straight to the only jenjang when just one is
         * active, so it is listed only when it actually renders a selector.
         * Either way the per-jenjang URLs carry the content.
         */
        if ($this->activeInstitutions()->count() > 1) {
            $pages[] = ['route' => 'ppdb.index', 'priority' => 0.7, 'freq' => 'monthly'];
        }

        if (feature_enabled('toko')) {
            $pages[] = ['route' => 'products.index', 'priority' => 0.6, 'freq' => 'weekly'];
        }

        return $pages;
    }

    /**
     * @return Collection<int, Institution>
     */
    private function activeInstitutions(): Collection
    {
        return $this->activeInstitutions ??= Institution::query()->active()->ordered()->get();
    }

    /**
     * @return Collection<int, Institution>
     */
    private function courseInstitutions(): Collection
    {
        return $this->courseInstitutions ??= $this->activeInstitutions()
            ->filter(fn (Institution $institution): bool => (bool) $institution->has_groups)
            ->values();
    }

    /**
     * @return Collection<int, Institution>
     */
    private function ppdbInstitutions(): Collection
    {
        return $this->ppdbInstitutions ??= $this->activeInstitutions()
            ->reject(fn (Institution $institution): bool => (bool) $institution->has_groups)
            ->values();
    }
}
