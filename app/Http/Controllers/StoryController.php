<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $stories = Story::published()
            ->ordered()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $appName = config('app.name');

        $seo = [
            'title' => "Cerita Santri | {$appName}",
            'description' => "Kisah inspiratif dan pengalaman santri di {$appName}. Cerita perjuangan, prestasi, dan kehidupan pesantren.",
            'canonical' => route('stories.index'),
        ];

        return view('stories.index', compact('stories', 'seo'));
    }

    public function show(Story $story): View
    {
        abort_unless($story->is_published, 404);

        $related = Story::published()
            ->where('id', '!=', $story->id)
            ->ordered()
            ->limit(3)
            ->get();

        $seo = [
            'title' => "{$story->title} | ".config('app.name'),
            'description' => $story->meta_description,
            'canonical' => route('stories.show', $story),
            'og_image' => $story->thumbnail_url,
        ];

        return view('stories.show', compact('story', 'related', 'seo'));
    }
}
