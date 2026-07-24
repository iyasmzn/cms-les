<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ProtectsAgainstSpam;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    use ProtectsAgainstSpam;

    public function store(Request $request, Post $post): RedirectResponse
    {
        if (! $post->allow_comments) {
            throw ValidationException::withMessages([
                'body' => 'Komentar tidak diizinkan pada artikel ini.',
            ]);
        }

        $user = $request->user();

        $rules = [
            'body' => ['required', 'string', 'max:2000'],
        ];

        // Guests must supply a name and pass the spam checks; members are trusted.
        if ($user === null) {
            $rules['name'] = ['required', 'string', 'max:150'];
            $rules = [...$rules, ...$this->spamProtectionRules($request)];
        }

        $validated = $request->validate($rules);

        $autoPublish = $user !== null
            ? setting_bool('comment_user_auto_publish', true)
            : setting_bool('comment_guest_auto_publish', true);

        $post->comments()->create([
            'user_id' => $user?->id,
            'guest_name' => $user === null ? $validated['name'] : null,
            'body' => $validated['body'],
            'is_approved' => $autoPublish,
        ]);

        $message = $autoPublish
            ? 'Komentar Anda telah dikirim.'
            : 'Komentar Anda telah dikirim dan menunggu persetujuan admin.';

        return back()
            ->with('comment_success', $message)
            ->withFragment('komentar');
    }
}
