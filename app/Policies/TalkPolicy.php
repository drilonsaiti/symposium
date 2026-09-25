<?php

namespace App\Policies;

use App\Models\ConferenceReviewer;
use App\Models\ConferenceTalk;
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
        if ($user->id === $talk->user_id) {
            return true;
        }

        return $talk->conferences()
            ->where(function ($query) use ($user){
                $query->where('conferences.user_id',$user->id)
                    ->orWhereHas('reviewers', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            })
            ->exists();
    }

    public function create(User $user)
    {
        return true;
    }

    public function delete(User $user, Talk $talk)
    {
        return $user->id === $talk->user_id;
    }

    public function submitTalk(User $user, Talk $talk)
    {
        return $user->id === $talk->user_id;
    }
}
