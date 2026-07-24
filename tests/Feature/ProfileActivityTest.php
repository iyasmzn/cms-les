<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_the_users_own_comments(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'Komentar milik saya sendiri.',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Komentar milik saya sendiri.');
    }

    public function test_profile_does_not_show_other_users_activity(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create();

        Comment::factory()->create([
            'user_id' => $other->id,
            'post_id' => $post->id,
            'body' => 'Komentar orang lain.',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertDontSee('Komentar orang lain.');
    }
}
