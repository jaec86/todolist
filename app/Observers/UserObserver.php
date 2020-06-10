<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    public function updating(User $user)
    {
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    }
    
    public function updated(User $user)
    {
        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
        }
    }
}
