<?php

namespace App\Listeners;

use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class ClaimGuestCourseRegistrations
{
    /**
     * When a user logs in, attach any guest course registrations that were made
     * with their email address or phone number so they appear under "My Courses".
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        if (blank($user->email) && blank($user->phone)) {
            return;
        }

        GroupMember::query()
            ->whereNull('user_id')
            ->where(function ($query) use ($user): void {
                if (filled($user->email)) {
                    $query->orWhere('email', $user->email);
                }

                if (filled($user->phone)) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->update(['user_id' => $user->id]);
    }
}
