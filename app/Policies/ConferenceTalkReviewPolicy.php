<?php

namespace App\Policies;

use App\Models\Conference;
use App\Models\User;

class ConferenceTalkReviewPolicy
{
    public function create(User $user, Conference $conference): bool
    {
       return $conference->reviewers()
            ->where('users.id', $user->id)
            ->exists();
    }
}
