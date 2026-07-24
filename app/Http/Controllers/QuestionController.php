<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(): View
    {
        $questions = Question::published()->answered()->general()
            ->orderByDesc('answered_at')
            ->paginate(10);

        $appName = config('app.name');

        $seo = [
            'title' => "Tanya Jawab | {$appName}",
            'description' => "Kirim pertanyaan seputar pesantren dan baca jawaban dari ustaz/ustazah {$appName}.",
            'canonical' => route('questions.index'),
        ];

        return view('questions.index', compact('questions', 'seo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
            'question' => ['required', 'string', 'max:2000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        if ($validated['post_id'] ?? null) {
            $post = Post::findOrFail($validated['post_id']);

            if (! $post->allow_questions) {
                throw ValidationException::withMessages([
                    'question' => 'Pertanyaan tidak diizinkan pada artikel ini.',
                ]);
            }
        }

        Question::create([
            'post_id' => $validated['post_id'] ?? null,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'question' => $validated['question'],
        ]);

        return back()->with('success', 'Pertanyaan Anda telah terkirim. Kami akan segera menjawabnya.');
    }
}
