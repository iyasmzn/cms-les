<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_question_and_is_redirected_to_login(): void
    {
        $post = Post::factory()->create(['allow_questions' => true]);

        $response = $this->post(route('questions.store'), [
            'post_id' => $post->id,
            'question' => 'Apa syarat pendaftaran?',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_authenticated_user_can_submit_question_when_post_allows_questions(): void
    {
        $user = User::factory()->create(['name' => 'Budi', 'email' => 'budi@example.com']);
        $post = Post::factory()->create(['allow_questions' => true]);

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'post_id' => $post->id,
            'question' => 'Apa syarat pendaftaran?',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'is_anonymous' => false,
            'question' => 'Apa syarat pendaftaran?',
        ]);
    }

    public function test_user_can_submit_question_anonymously(): void
    {
        $user = User::factory()->create(['name' => 'Budi']);

        $this->actingAs($user)->post(route('questions.store'), [
            'question' => 'Pertanyaan anonim.',
            'is_anonymous' => '1',
        ]);

        $this->assertDatabaseHas('questions', [
            'user_id' => $user->id,
            'name' => 'Budi',
            'is_anonymous' => true,
            'question' => 'Pertanyaan anonim.',
        ]);
    }

    public function test_anonymous_question_hides_the_name_on_the_public_page(): void
    {
        Question::factory()->answered()->anonymous()->create([
            'name' => 'Budi Rahasia',
            'question' => 'Pertanyaan yang disembunyikan namanya.',
            'is_published' => true,
        ]);

        $response = $this->get(route('questions.index'));

        $response->assertStatus(200);
        $response->assertSee('Anonim');
        $response->assertDontSee('Budi Rahasia');
    }

    public function test_question_is_rejected_when_post_disallows_questions(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['allow_questions' => false]);

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'post_id' => $post->id,
            'question' => 'Mencoba bertanya.',
        ]);

        $response->assertSessionHasErrors('question');
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_general_question_without_post_is_still_accepted(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'question' => 'Pertanyaan umum.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'post_id' => null,
            'user_id' => $user->id,
            'question' => 'Pertanyaan umum.',
        ]);
    }

    public function test_question_form_is_hidden_when_post_disallows_questions(): void
    {
        $post = Post::factory()->create(['allow_questions' => false]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('Kolom tanya jawab untuk artikel ini ditutup.');
    }
}
