<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Group');
    }

    public function view(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('View:Group') && $this->reaches($authUser, $group);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Group');
    }

    public function update(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('Update:Group') && $this->reaches($authUser, $group);
    }

    public function delete(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('Delete:Group');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Group');
    }

    public function restore(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('Restore:Group');
    }

    public function forceDelete(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('ForceDelete:Group');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Group');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Group');
    }

    public function replicate(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('Replicate:Group');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Group');
    }

    /**
     * Whether the user may see every group rather than only the ones they
     * coach. Drives both this policy and the resource's query scoping.
     */
    public function viewAll(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAll:Group');
    }

    /**
     * Whether the user may move a member from this group into a sibling group.
     * Falls back to Update:Group so existing admins keep working before the
     * MoveMember:Group permission has been seeded.
     */
    public function moveMember(AuthUser $authUser, Group $group): bool
    {
        return ($authUser->can('MoveMember:Group') || $authUser->can('Update:Group'))
            && $this->reaches($authUser, $group);
    }

    /**
     * Narrows accounts that are linked to a coach profile down to their own
     * groups. Everyone else is governed by the plain Group permissions, so
     * existing admins are unaffected.
     */
    protected function reaches(AuthUser $authUser, Group $group): bool
    {
        if (! ($authUser instanceof User) || ! $authUser->isInstructor()) {
            return true;
        }

        return $authUser->can('ViewAll:Group') || $authUser->coaches($group);
    }
}
