<?php

namespace App\Console\Commands;

use App\Services\SitemapBuilder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('sitemap:generate')]
#[Description('Invalidate the cached sitemap so it regenerates on the next request')]
class GenerateSitemap extends Command
{
    public function handle(): int
    {
        Cache::forget(SitemapBuilder::CACHE_KEY);

        $this->info('Sitemap cache cleared; it will regenerate on the next request to /sitemap.xml.');

        return self::SUCCESS;
    }
}
