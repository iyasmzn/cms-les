<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Serve robots.txt from the live request instead of a static file, so the
     * `Sitemap:` directive is an absolute URL on the domain actually being
     * crawled. The sitemap protocol requires an absolute URL there; a relative
     * path is silently ignored by crawlers.
     */
    public function index(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /checkout',
            'Disallow: /keranjang',
            /*
             * Personal, login-gated pages: nothing for a crawler to index.
             */
            'Disallow: /my-courses',
            /*
             * File streams rather than pages: crawling them wastes crawl budget
             * and inflates each document's download_count.
             */
            'Disallow: /unduhan/*/download',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
