<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;



    /**
     * Check if user is different of current user - Pour éviter de se parler à soi-même : )
     * @param User $user
     * @param User $to
     * @return bool
     */
    public function talkTo(User $user, User $to)
    {
        return $user->id !== $to->id;
    }
}
