<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthorRequestController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAuthor()) {
            return redirect()->route('home')
                ->with('info', 'Anda sudah menjadi author.');
        }

        $existingRequest = $user->authorRequest;

        return view('auth.author-request', compact('existingRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAuthor()) {
            return redirect()->route('home');
        }

        if ($user->hasPendingAuthorRequest()) {
            return back()->with('info', 'Permintaan Anda sedang dalam proses peninjauan.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:50', 'max:1000'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
        ]);

        AuthorRequest::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($validated, [
                'status' => 'pending',
                'admin_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ])
        );

        return back()->with('success', 'Permintaan Anda telah dikirim. Admin akan meninjau dalam 1-3 hari kerja.');
    }
}
