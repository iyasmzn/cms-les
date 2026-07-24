<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Slide;
use Illuminate\Auth\Access\HandlesAuthorization;

class SlidePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Slide');
    }

    public function view(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('View:Slide');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Slide');
    }

    public function update(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('Update:Slide');
    }

    public function delete(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('Delete:Slide');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Slide');
    }

    public function restore(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('Restore:Slide');
    }

    public function forceDelete(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('ForceDelete:Slide');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Slide');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Slide');
    }

    public function replicate(AuthUser $authUser, Slide $slide): bool
    {
        return $authUser->can('Replicate:Slide');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Slide');
    }

}