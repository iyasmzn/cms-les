<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $category = $request->query('category');

        $posts = Post::published()
            ->when($category, fn ($q) => $q->byCategory($category))
            ->latest('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Post::published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $appName = config('app.name');

        $seo = [
            'title' => $category
                ? "{$category} — Blog | {$appName}"
                : "Blog & Berita Sekolah | {$appName}",
            'description' => $category
                ? "Baca artikel dan berita terbaru kategori {$category} dari {$appName}."
                : "Temukan berita, artikel, dan informasi terkini dari {$appName}. Prestasi siswa, kegiatan sekolah, dan pengumuman resmi.",
            'canonical' => route('blog.index', $category ? ['category' => $category] : []),
            'og_image' => asset('images/og-blog.jpg'),
        ];

        return view('blog.index', compact('posts', 'categories', 'category', 'seo'));
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $seo = [
            'title' => "{$post->title} | ".config('app.name'),
            'description' => $post->meta_description,
            'canonical' => $post->canonical_url,
            'og_image' => $post->thumbnail_url,
            'og_type' => 'article',
            'published' => $post->published_at?->toIso8601String(),
            'author' => $post->author,
            'category' => $post->category,
        ];

        $postQuestions = Question::published()->answered()
            ->where('post_id', $post->id)
            ->orderByDesc('answered_at')
            ->get();

        $comments = $post->comments()
            ->approved()
            ->with('user')
            ->latest()
            ->get();

        return view('blog.show', compact('post', 'related', 'seo', 'postQuestions', 'comments'));
    }
}
