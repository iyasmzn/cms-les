<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Greeting;
use App\Models\Institution;
use App\Models\Media;
use App\Models\Post;
use App\Models\Product;
use App\Models\Program;
use App\Models\Slide;
use App\Models\Stat;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // 1 featured (first) + up to 6 in the grid
        $posts = Post::published()
            ->latest('published_at')
            ->limit(7)
            ->get();

        $stats = Stat::ordered()->get();
        $slides = Slide::active()->get();
        $greetings = Greeting::published()->get();
        $testimonials = Testimonial::published()->get();
        $contactItems = contact_items();

        $upcomingEvents = Event::published()
            ->upcoming()
            ->ordered()
            ->limit(3)
            ->get();

        $programs = Program::published()
            ->ordered()
            ->limit(6)
            ->get();

        $featuredProducts = Product::available()
            ->ordered()
            ->limit(8)
            ->get();

        $galleryMedia = Media::inGallery()->limit(6)->get();

        $courses = Institution::query()
            ->active()
            ->where('has_groups', true)
            ->withCount(['groups' => fn ($query) => $query->where('is_active', true)])
            ->ordered()
            ->limit(6)
            ->get();

        return view('welcome', compact(
            'posts', 'stats', 'slides', 'greetings', 'testimonials', 'contactItems',
            'upcomingEvents', 'programs', 'featuredProducts',
            'galleryMedia', 'courses',
        ));
    }
}
