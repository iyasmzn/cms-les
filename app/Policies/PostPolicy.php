<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Post');
    }

    public function view(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('View:Post') && $this->owns($authUser, $post);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Post');
    }

    public function update(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('Update:Post') && $this->owns($authUser, $post);
    }

    public function delete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('Delete:Post') && $this->owns($authUser, $post);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Post');
    }

    public function restore(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('Restore:Post');
    }

    public function forceDelete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('ForceDelete:Post');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Post');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Post');
    }

    public function replicate(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('Replicate:Post');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Post');
    }

    /**
     * Whether the user may publish or unpublish an article. Regular authors
     * lack this permission and must have an admin or super author publish
     * their drafts.
     */
    public function publish(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('Publish:Post');
    }

    /**
     * Whether the user may see articles authored by other users. Regular
     * authors are restricted to their own articles.
     */
    public function viewAll(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAll:Post');
    }

    /**
     * A user may access a specific article when they own it or hold the
     * ViewAll:Post permission (admins and super authors).
     */
    protected function owns(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('ViewAll:Post') || $post->user_id === $authUser->getKey();
    }
}
