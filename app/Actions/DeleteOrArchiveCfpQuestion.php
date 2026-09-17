<?php

namespace App\Actions;

use App\Models\CfpQuestion;

class DeleteOrArchiveCfpQuestion
{

    public function execute(CfpQuestion $question): void
    {
        if ($question->canBeDeleted()) {
            $question->delete();
        } else {
            $question->update(['is_active' => false]);
        }
    }
}
