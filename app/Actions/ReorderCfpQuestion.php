<?php

namespace App\Actions;

use App\Models\CfpQuestion;
use Illuminate\Support\Facades\DB;

final class ReorderCfpQuestion
{

    public function execute(CfpQuestion $question, string $direction): void
    {
        DB::transaction(function () use ($question, $direction) {
            $question = CfpQuestion::query()
                ->whereKey($question->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $neighbor = $this->findNeighbor($question, $direction);

            if(!$neighbor) {
                return;
            }

            $position = $question->position;

            $question->update(['position' => $neighbor->position]);
            $neighbor->update(['position' => $position]);
        });
    }

    private function findNeighbor(CfpQuestion $question, string $direction): ?CfpQuestion
    {
        $query = CfpQuestion::query()
            ->where('cfp_questions.conference_id', $question->conference_id)
            ->active();

        if ($direction === 'up') {
            $query->where('position','<',$question->position)
                ->orderBy('position', 'desc');
        } else {
            $query->where('position','>',$question->position)
                ->orderBy('position', 'asc');
        }

        return $query
            ->lockForUpdate()
            ->first();
    }
}
