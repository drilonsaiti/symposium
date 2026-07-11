<?php

namespace App\Policies;

use App\Models\Talk;
use App\Models\User;

class TalkPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function update(User $user, Talk $talk)
    {
        return $user->id === $talk->user_id;
    }

    public function view(User $user, Talk $talk)
    {
        return $user->id === $talk->user_id;
    }

    public function delete(User $user, Talk $talk)
    {
        return $user->id === $talk->user_id;
    }
}
