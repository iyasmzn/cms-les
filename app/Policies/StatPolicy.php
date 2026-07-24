<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Stat;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Stat');
    }

    public function view(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('View:Stat');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Stat');
    }

    public function update(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('Update:Stat');
    }

    public function delete(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('Delete:Stat');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Stat');
    }

    public function restore(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('Restore:Stat');
    }

    public function forceDelete(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('ForceDelete:Stat');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Stat');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Stat');
    }

    public function replicate(AuthUser $authUser, Stat $stat): bool
    {
        return $authUser->can('Replicate:Stat');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Stat');
    }

}