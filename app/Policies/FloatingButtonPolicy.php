<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FloatingButton;
use Illuminate\Auth\Access\HandlesAuthorization;

class FloatingButtonPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FloatingButton');
    }

    public function view(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('View:FloatingButton');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FloatingButton');
    }

    public function update(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('Update:FloatingButton');
    }

    public function delete(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('Delete:FloatingButton');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FloatingButton');
    }

    public function restore(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('Restore:FloatingButton');
    }

    public function forceDelete(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('ForceDelete:FloatingButton');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FloatingButton');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FloatingButton');
    }

    public function replicate(AuthUser $authUser, FloatingButton $floatingButton): bool
    {
        return $authUser->can('Replicate:FloatingButton');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FloatingButton');
    }

}