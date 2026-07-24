<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'upcoming');

        $query = Event::published();

        if ($filter === 'upcoming') {
            $query->upcoming()->ordered();
        } elseif ($filter === 'past') {
            $query->where('starts_at', '<', now())->orderBy('starts_at', 'desc');
        } else {
            $query->orderBy('starts_at', 'desc');
        }

        $events = $query->paginate(self::PER_PAGE)->withQueryString();

        $appName = config('app.name');

        $seo = [
            'title' => "Kegiatan & Event | {$appName}",
            'description' => "Informasi kegiatan dan event terbaru dari {$appName}. Pengajian, seminar, workshop, dan berbagai kegiatan pesantren.",
            'canonical' => route('events.index'),
        ];

        return view('events.index', compact('events', 'filter', 'seo'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        $related = Event::published()
            ->where('id', '!=', $event->id)
            ->orderBy('starts_at', 'desc')
            ->limit(3)
            ->get();

        $seo = [
            'title' => "{$event->title} | ".config('app.name'),
            'description' => $event->excerpt ?? Str::limit(strip_tags((string) $event->content), 155),
            'canonical' => route('events.show', $event),
            'og_image' => $event->thumbnail_url,
        ];

        return view('events.show', compact('event', 'related', 'seo'));
    }
}
