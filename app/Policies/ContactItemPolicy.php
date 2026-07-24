<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContactItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContactItem');
    }

    public function view(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('View:ContactItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContactItem');
    }

    public function update(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('Update:ContactItem');
    }

    public function delete(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('Delete:ContactItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ContactItem');
    }

    public function restore(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('Restore:ContactItem');
    }

    public function forceDelete(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('ForceDelete:ContactItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContactItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContactItem');
    }

    public function replicate(AuthUser $authUser, ContactItem $contactItem): bool
    {
        return $authUser->can('Replicate:ContactItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContactItem');
    }

}