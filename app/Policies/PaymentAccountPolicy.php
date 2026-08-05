<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentAccount;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PaymentAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentAccount');
    }

    public function view(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('View:PaymentAccount');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentAccount');
    }

    public function update(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('Update:PaymentAccount');
    }

    public function delete(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('Delete:PaymentAccount');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentAccount');
    }

    public function restore(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('Restore:PaymentAccount');
    }

    public function forceDelete(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('ForceDelete:PaymentAccount');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentAccount');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentAccount');
    }

    public function replicate(AuthUser $authUser, PaymentAccount $paymentAccount): bool
    {
        return $authUser->can('Replicate:PaymentAccount');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentAccount');
    }
}
