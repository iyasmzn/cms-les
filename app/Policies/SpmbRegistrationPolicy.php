<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SpmbRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpmbRegistrationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SpmbRegistration');
    }

    public function view(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('View:SpmbRegistration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SpmbRegistration');
    }

    public function update(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('Update:SpmbRegistration');
    }

    public function delete(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('Delete:SpmbRegistration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SpmbRegistration');
    }

    public function restore(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('Restore:SpmbRegistration');
    }

    public function forceDelete(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('ForceDelete:SpmbRegistration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SpmbRegistration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SpmbRegistration');
    }

    public function replicate(AuthUser $authUser, SpmbRegistration $spmbRegistration): bool
    {
        return $authUser->can('Replicate:SpmbRegistration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SpmbRegistration');
    }

}