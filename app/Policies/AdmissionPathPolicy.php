<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AdmissionPath;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AdmissionPathPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdmissionPath');
    }

    public function view(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('View:AdmissionPath');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdmissionPath');
    }

    public function update(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('Update:AdmissionPath');
    }

    public function delete(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('Delete:AdmissionPath');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AdmissionPath');
    }

    public function restore(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('Restore:AdmissionPath');
    }

    public function forceDelete(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('ForceDelete:AdmissionPath');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AdmissionPath');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AdmissionPath');
    }

    public function replicate(AuthUser $authUser, AdmissionPath $admissionPath): bool
    {
        return $authUser->can('Replicate:AdmissionPath');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AdmissionPath');
    }
}
