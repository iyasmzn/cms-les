<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Comments\Pages\EditComment;
use App\Models\Comment;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommentResourceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_admin_can_reply_to_a_comment(): void
    {
        $admin = $this->admin();
        $comment = Comment::factory()->create(['admin_reply' => null, 'replied_at' => null]);

        $this->actingAs($admin);

        Livewire::test(EditComment::class, ['record' => $comment->id])
            ->fillForm(['admin_reply' => 'Terima kasih atas komentarnya.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $comment->refresh();

        $this->assertSame('Terima kasih atas komentarnya.', $comment->admin_reply);
        $this->assertNotNull($comment->replied_at);
    }

    public function test_clearing_reply_clears_replied_at(): void
    {
        $admin = $this->admin();
        $comment = Comment::factory()->replied()->create();

        $this->actingAs($admin);

        Livewire::test(EditComment::class, ['record' => $comment->id])
            ->fillForm(['admin_reply' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $comment->refresh();

        $this->assertNull($comment->admin_reply);
        $this->assertNull($comment->replied_at);
    }

    public function test_admin_can_delete_a_comment(): void
    {
        $admin = $this->admin();
        $comment = Comment::factory()->create();

        $this->actingAs($admin);

        Livewire::test(EditComment::class, ['record' => $comment->id])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_admin_can_toggle_comment_visibility(): void
    {
        $admin = $this->admin();
        $comment = Comment::factory()->create(['is_approved' => true]);

        $this->actingAs($admin);

        Livewire::test(EditComment::class, ['record' => $comment->id])
            ->fillForm(['is_approved' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($comment->refresh()->is_approved);
    }
}
