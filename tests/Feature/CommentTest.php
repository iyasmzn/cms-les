<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_post_comment_with_a_name(): void
    {
        $post = Post::factory()->create(['allow_comments' => true]);

        $response = $this->post(route('comments.store', $post), [
            'name' => 'Pengunjung',
            'body' => 'Komentar dari tamu.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('comment_success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => null,
            'guest_name' => 'Pengunjung',
            'body' => 'Komentar dari tamu.',
            'is_approved' => true,
        ]);
    }

    public function test_guest_comment_requires_a_name(): void
    {
        $post = Post::factory()->create(['allow_comments' => true]);

        $response = $this->post(route('comments.store', $post), [
            'body' => 'Tanpa nama.',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_authenticated_user_can_post_comment_without_typing_a_name(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['allow_comments' => true]);

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'body' => 'Artikel yang bermanfaat, terima kasih.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('comment_success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'guest_name' => null,
            'body' => 'Artikel yang bermanfaat, terima kasih.',
            'is_approved' => true,
        ]);
    }

    public function test_guest_comment_is_held_for_approval_when_setting_disabled(): void
    {
        Setting::set('comment_guest_auto_publish', false);

        $post = Post::factory()->create(['allow_comments' => true]);

        $this->post(route('comments.store', $post), [
            'name' => 'Pengunjung',
            'body' => 'Komentar menunggu moderasi.',
        ]);

        $this->assertDatabaseHas('comments', [
            'guest_name' => 'Pengunjung',
            'is_approved' => false,
        ]);
    }

    public function test_member_comment_is_held_for_approval_when_setting_disabled(): void
    {
        Setting::set('comment_user_auto_publish', false);

        $user = User::factory()->create();
        $post = Post::factory()->create(['allow_comments' => true]);

        $this->actingAs($user)->post(route('comments.store', $post), [
            'body' => 'Komentar member menunggu moderasi.',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'is_approved' => false,
        ]);
    }

    public function test_comment_is_rejected_when_post_disallows_comments(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['allow_comments' => false]);

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'body' => 'Mencoba berkomentar.',
        ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['allow_comments' => true]);

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_approved_comments_are_shown_on_the_article_page(): void
    {
        $post = Post::factory()->create(['allow_comments' => true]);
        Comment::factory()->create(['post_id' => $post->id, 'body' => 'Komentar tampil']);
        Comment::factory()->hidden()->create(['post_id' => $post->id, 'body' => 'Komentar tersembunyi']);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('Komentar tampil');
        $response->assertDontSee('Komentar tersembunyi');
    }

    public function test_guest_comment_displays_the_typed_name(): void
    {
        $post = Post::factory()->create(['allow_comments' => true]);
        Comment::factory()->guest()->create([
            'post_id' => $post->id,
            'guest_name' => 'Tamu Budi',
            'body' => 'Halo dari tamu.',
        ]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('Tamu Budi');
    }
}
