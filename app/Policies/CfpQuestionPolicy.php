<?php

namespace App\Policies;

use App\Models\CfpQuestion;
use App\Models\Conference;
use App\Models\User;

class CfpQuestionPolicy
{
    public function create(User $user, Conference $conference): bool
    {
        return $user->id === $conference->user_id;
    }
    public function manageQuestions(User $user, CfpQuestion $question): bool
    {
        return $user->id === $question->conference->user_id;
    }
}
