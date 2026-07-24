<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPostPermissions();
    }

    /**
     * Create the Post permissions and blog roles that Shield generates in
     * production, so authorization checks behave the same under RefreshDatabase.
     */
    private function seedPostPermissions(): void
    {
        $permissions = [
            'ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post', 'Delete:Post',
            'DeleteAny:Post', 'Replicate:Post', 'Reorder:Post', 'Publish:Post', 'ViewAll:Post',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web'])
            ->syncPermissions([
                'ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post', 'Replicate:Post', 'Reorder:Post',
            ]);

        Role::firstOrCreate(['name' => 'author_super', 'guard_name' => 'web'])
            ->syncPermissions([
                'ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post', 'Delete:Post', 'DeleteAny:Post',
                'Replicate:Post', 'Reorder:Post', 'Publish:Post', 'ViewAll:Post',
            ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function author(): User
    {
        return User::factory()->create()->assignRole('author');
    }

    private function superAuthor(): User
    {
        return User::factory()->create()->assignRole('author_super');
    }

    private function seedPostCategory(): void
    {
        Category::create([
            'type' => Category::TYPE_POST,
            'name' => 'Berita',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    // ── Query scoping ──────────────────────────────────────────────

    public function test_author_only_sees_their_own_posts_in_query(): void
    {
        $author = $this->author();
        $own = Post::factory()->create(['user_id' => $author->id]);
        $foreign = Post::factory()->create();

        $this->actingAs($author);

        $ids = PostResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($own->id));
        $this->assertFalse($ids->contains($foreign->id));
    }

    public function test_super_author_sees_all_posts_in_query(): void
    {
        $superAuthor = $this->superAuthor();
        $own = Post::factory()->create(['user_id' => $superAuthor->id]);
        $foreign = Post::factory()->create();

        $this->actingAs($superAuthor);

        $ids = PostResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($own->id));
        $this->assertTrue($ids->contains($foreign->id));
    }

    public function test_author_list_page_hides_other_authors_posts(): void
    {
        $author = $this->author();
        $own = Post::factory()->create(['user_id' => $author->id]);
        $foreign = Post::factory()->create();

        $this->actingAs($author);

        Livewire::test(ListPosts::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$own])
            ->assertCanNotSeeTableRecords([$foreign]);
    }

    // ── Authorization ──────────────────────────────────────────────

    public function test_author_cannot_view_or_update_foreign_post(): void
    {
        $author = $this->author();
        $foreign = Post::factory()->create();

        $this->actingAs($author);

        $this->assertFalse(Gate::allows('view', $foreign));
        $this->assertFalse(Gate::allows('update', $foreign));
    }

    public function test_author_can_view_and_update_own_post(): void
    {
        $author = $this->author();
        $own = Post::factory()->create(['user_id' => $author->id]);

        $this->actingAs($author);

        $this->assertTrue(Gate::allows('view', $own));
        $this->assertTrue(Gate::allows('update', $own));
    }

    public function test_author_cannot_publish_but_super_author_can(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($this->author());
        $this->assertFalse(Gate::allows('publish', $post));

        $this->actingAs($this->superAuthor());
        $this->assertTrue(Gate::allows('publish', $post));
    }

    // ── Create ─────────────────────────────────────────────────────

    public function test_author_created_post_is_unpublished_and_owned(): void
    {
        $author = $this->author();
        $this->seedPostCategory();
        $this->actingAs($author);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Kegiatan Santri Baru',
                'slug' => 'kegiatan-santri-baru',
                'content' => '<p>Isi artikel percobaan.</p>',
                'category' => 'Berita',
                'read_time' => 3,
                'author' => $author->name,
                'author_initials' => 'AB',
                'image_source' => 'upload',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Post::class, [
            'slug' => 'kegiatan-santri-baru',
            'user_id' => $author->id,
            'is_published' => false,
        ]);
    }

    public function test_super_author_can_publish_on_create(): void
    {
        $superAuthor = $this->superAuthor();
        $this->seedPostCategory();
        $this->actingAs($superAuthor);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Pengumuman Resmi',
                'slug' => 'pengumuman-resmi',
                'content' => '<p>Konten pengumuman.</p>',
                'category' => 'Berita',
                'read_time' => 2,
                'author' => 'Admin',
                'author_initials' => 'AD',
                'image_source' => 'upload',
                'is_published' => true,
                'published_at' => now()->format('Y-m-d H:i:s'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Post::class, [
            'slug' => 'pengumuman-resmi',
            'user_id' => $superAuthor->id,
            'is_published' => true,
        ]);
    }

    // ── Edit: publish state guarded ────────────────────────────────

    public function test_author_cannot_flip_publish_state_on_edit(): void
    {
        $author = $this->author();
        $this->seedPostCategory();
        $post = Post::factory()->draft()->create(['user_id' => $author->id, 'category' => 'Berita']);

        $this->actingAs($author);

        Livewire::test(EditPost::class, ['record' => $post->id])
            ->fillForm(['title' => 'Judul Diperbarui'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id,
            'title' => 'Judul Diperbarui',
            'is_published' => false,
        ]);
    }
}
