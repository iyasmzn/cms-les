<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AuthorRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuthorRequest');
    }

    public function view(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('View:AuthorRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuthorRequest');
    }

    public function update(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('Update:AuthorRequest');
    }

    public function delete(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('Delete:AuthorRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuthorRequest');
    }

    public function restore(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('Restore:AuthorRequest');
    }

    public function forceDelete(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('ForceDelete:AuthorRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuthorRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuthorRequest');
    }

    public function replicate(AuthUser $authUser, AuthorRequest $authorRequest): bool
    {
        return $authUser->can('Replicate:AuthorRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuthorRequest');
    }

}