<?php

namespace App\Http\Controllers;

use App\Services\SitemapBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Serve the sitemap, cached until content changes. The XML is rendered
     * during a real HTTP request so absolute URLs use the live domain rather
     * than the CLI's APP_URL fallback.
     */
    public function index(SitemapBuilder $builder): Response
    {
        $xml = Cache::rememberForever(
            SitemapBuilder::CACHE_KEY,
            fn (): string => $builder->build()->render(),
        );

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
