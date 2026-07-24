<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Donation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DonationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Donation');
    }

    public function view(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('View:Donation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Donation');
    }

    public function update(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('Update:Donation');
    }

    public function delete(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('Delete:Donation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Donation');
    }

    public function restore(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('Restore:Donation');
    }

    public function forceDelete(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('ForceDelete:Donation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Donation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Donation');
    }

    public function replicate(AuthUser $authUser, Donation $donation): bool
    {
        return $authUser->can('Replicate:Donation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Donation');
    }
}
