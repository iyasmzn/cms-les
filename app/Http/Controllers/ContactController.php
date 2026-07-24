<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $contactItems = contact_items();

        $appName = config('app.name');

        $seo = [
            'title' => "Kontak | {$appName}",
            'description' => "Hubungi {$appName}. Temukan informasi kontak, alamat, dan cara menghubungi kami.",
            'canonical' => route('contact.index'),
        ];

        return view('contact.index', compact('contactItems', 'seo'));
    }
}
