<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $category = $request->query('category', '');

        $downloads = Download::active()
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->byCategory($category))
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Download::active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "Unduhan | {$siteName}",
            'description' => "Unduh dokumen, formulir, surat edaran, dan pengumuman resmi dari {$siteName}.",
            'canonical' => route('downloads.index'),
        ];

        return view('downloads.index', compact('downloads', 'categories', 'search', 'category', 'seo'));
    }

    public function download(Download $download): StreamedResponse|RedirectResponse
    {
        abort_unless($download->is_active, 404);

        if (! Storage::disk('public')->exists($download->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $download->increment('download_count');

        return Storage::disk('public')->download(
            $download->file_path,
            $download->original_filename,
        );
    }
}
