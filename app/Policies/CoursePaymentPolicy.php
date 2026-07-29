<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CoursePayment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CoursePaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CoursePayment');
    }

    public function view(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('View:CoursePayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CoursePayment');
    }

    public function update(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('Update:CoursePayment');
    }

    public function delete(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('Delete:CoursePayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CoursePayment');
    }

    public function restore(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('Restore:CoursePayment');
    }

    public function forceDelete(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('ForceDelete:CoursePayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CoursePayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CoursePayment');
    }

    public function replicate(AuthUser $authUser, CoursePayment $coursePayment): bool
    {
        return $authUser->can('Replicate:CoursePayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CoursePayment');
    }
}
