<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Greeting;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GreetingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Greeting');
    }

    public function view(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('View:Greeting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Greeting');
    }

    public function update(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('Update:Greeting');
    }

    public function delete(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('Delete:Greeting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Greeting');
    }

    public function restore(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('Restore:Greeting');
    }

    public function forceDelete(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('ForceDelete:Greeting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Greeting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Greeting');
    }

    public function replicate(AuthUser $authUser, Greeting $greeting): bool
    {
        return $authUser->can('Replicate:Greeting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Greeting');
    }
}
