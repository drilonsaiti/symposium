<?php

namespace App\Actions;

use App\Models\ConferenceTalk;
use App\Models\ConferenceTalkReview;
use App\Models\User;

final class SaveConferenceTalkReview
{
    public function execute(ConferenceTalk $submission, User $reviewer, array $data): ConferenceTalkReview
    {
        return ConferenceTalkReview::updateOrCreate(
            [
                'conference_talk_id' => $submission->id,
                'user_id' => $reviewer->id,
            ],
            [
                'score' => $data['score'],
                'recommendation' => $data['recommendation'],
                'note' => $data['note'] ?? null,
            ]
        );
    }
}
