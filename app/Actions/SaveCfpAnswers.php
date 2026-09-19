<?php

namespace App\Actions;

use App\Models\ConferenceTalk;

final class SaveCfpAnswers
{

    public function execute(ConferenceTalk $submission,array $answers): void
    {
        foreach ($answers as $questionId => $answer)
        {
            if ($answer === null || $answer === ''){
                continue;
            }

            $submission->answers()
                ->updateOrCreate(
                    ['cfp_question_id' => $questionId],
                    ['answer' => $answer]
                );
        }
    }
}
