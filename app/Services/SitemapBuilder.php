<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Post;
use App\Models\Program;
use App\Models\StaticPage;
use App\Models\Story;
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
     * Build the sitemap from the current published content.
     */
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        /* ── Homepage ── */
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'));

        /* ── Static index pages ── */
        foreach ([
            ['route' => 'blog.index',      'priority' => 0.8, 'freq' => 'daily'],
            ['route' => 'events.index',    'priority' => 0.8, 'freq' => 'weekly'],
            ['route' => 'programs.index',  'priority' => 0.7, 'freq' => 'monthly'],
            ['route' => 'stories.index',   'priority' => 0.6, 'freq' => 'weekly'],
            ['route' => 'teachers.index',  'priority' => 0.6, 'freq' => 'monthly'],
            ['route' => 'downloads.index', 'priority' => 0.6, 'freq' => 'weekly'],
            ['route' => 'gallery.index',   'priority' => 0.5, 'freq' => 'weekly'],
            ['route' => 'ppdb.index',      'priority' => 0.7, 'freq' => 'monthly'],
            ['route' => 'donasi.index',    'priority' => 0.5, 'freq' => 'monthly'],
            ['route' => 'kontak',          'priority' => 0.5, 'freq' => 'monthly'],
        ] as $page) {
            try {
                $sitemap->add(
                    Url::create(route($page['route']))
                        ->setPriority($page['priority'])
                        ->setChangeFrequency($page['freq'])
                );
            } catch (\Exception) {
                // skip if route doesn't exist
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

        /* ── Stories ── */
        Story::where('is_published', true)
            ->each(function (Story $story) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('stories.show', $story->slug))
                        ->setLastModificationDate($story->updated_at)
                        ->setPriority(0.5)
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

        return $sitemap;
    }
}
