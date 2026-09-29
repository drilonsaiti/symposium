<?php

namespace App\Http\Controllers;

use App\Actions\SaveConferenceTalkReview;
use App\Http\Requests\StoreConferenceTalkReviewRequest;
use App\Models\Conference;
use App\Models\ConferenceTalk;
use App\Models\Talk;
use Illuminate\Http\Request;

class ConferenceTalkReviewController extends Controller
{
    public function store(
        StoreConferenceTalkReviewRequest $request,
        Conference $conference,
        Talk $talk,
        SaveConferenceTalkReview $action
    ) {
        $submission = ConferenceTalk::where('conference_id', $conference->id)
            ->where('talk_id', $talk->id)
            ->firstOrFail();

        $review = $action->execute($submission, $request->user(), $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Review saved.',
                'review' => $review,
            ]);
        }

        return redirect()->back()->with('success', 'Review saved.');
    }
}
