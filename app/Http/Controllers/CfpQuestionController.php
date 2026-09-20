<?php

namespace App\Http\Controllers;

use App\Actions\DeleteOrArchiveCfpQuestion;
use App\Actions\ReorderCfpQuestion;
use App\Http\Requests\MoveCfpQuestionRequest;
use App\Http\Requests\StoreCfpQuestionRequest;
use App\Http\Requests\UpdateCfpQuestionRequest;
use App\Models\CfpQuestion;
use App\Models\Conference;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CfpQuestionController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreCfpQuestionRequest $request,Conference $conference)
    {
        $this->authorize('create', [CfpQuestion::class, $conference]);

        $position = $conference->cfpQuestions()
            ->active()
            ->max('position') + 1;

        $conference->cfpQuestions()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return redirect()->back();
    }

    public function update(
        UpdateCfpQuestionRequest $request,
        Conference $conference,
        CfpQuestion $question
    ) {
        $this->authorize('manageQuestions', $question);

        abort_unless($question->conference_id === $conference->id, 404);

        $question->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Conference $conference, CfpQuestion $question,DeleteOrArchiveCfpQuestion $action)
    {
        $this->authorize('manageQuestions', $question);

        abort_unless($question->conference_id === $conference->id, 404);

        $action->execute($question);

        return redirect()->back();

    }

    public function move(MoveCfpQuestionRequest $request,Conference $conference, CfpQuestion $question,ReorderCfpQuestion $action)
    {
        abort_unless($question->conference_id === $conference->id, 404);

        $data = $request->validated();
        $action->execute($question, $data['direction']);

        return redirect()->back();
    }
}
