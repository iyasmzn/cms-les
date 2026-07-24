<?php

namespace Tests\Feature;

use App\Filament\Resources\Comments\CommentResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_without_comment_permission_cannot_access_comment_management(): void
    {
        Permission::firstOrCreate(['name' => 'ViewAny:Comment', 'guard_name' => 'web']);

        $author = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole($author);

        $this->actingAs($user);

        $this->assertFalse(CommentResource::canViewAny());
    }

    public function test_user_with_comment_permission_can_access_comment_management(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'ViewAny:Comment', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $this->actingAs($user);

        $this->assertTrue(CommentResource::canViewAny());
    }
}
