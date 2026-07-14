<?php

namespace App\Http\Controllers;

use App\Enum\TalkSubmissionStatus;
use App\Http\Requests\ChangeStatusTalkSubmissionRequest;
use App\Http\Requests\StoreTalkSubmissionRequest;
use App\Models\Conference;
use App\Models\ConferenceTalk;
use App\Models\Talk;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TalkSubmissionController extends Controller
{
    //
    use AuthorizesRequests;

    public function store(Conference $conference, Talk $talk)
    {
        $this->authorize('submitTalk', $talk);

        $result = $conference->talks()->syncWithoutDetaching([
            $talk->id => ['status' => TalkSubmissionStatus::PENDING],
        ]);

        if (empty($result['attached'])) {
            return redirect()
                ->route('conferences.show', $conference)
                ->with('status', 'Talk was already submitted.');
        }

        return redirect()
            ->route('conferences.show', $conference)
            ->with('status', 'Talk submitted successfully.');
    }

    public function changeStatus(ChangeStatusTalkSubmissionRequest $request, Conference $conference, Talk $talk)
    {
        $this->authorize('manageSubmissions', $conference);
        $validated = $request->validated();
        $talkWithPivot = $conference->talks()->withPivot('status')->findOrFail($talk->id);

        $currentStatus = $talkWithPivot->pivot->status;
        $newStatus = TalkSubmissionStatus::from($validated['status']);

        if (!$currentStatus->canStatusChangeTo($newStatus)) {
            return back()->withErrors([
                'status' => 'This status change is not allowed.',
            ]);
        }

        $conference->talks()->updateExistingPivot($talk->id, [
            'status' => $newStatus->value
        ]);

        return back()->with('status', 'Talk status updated.');
    }
}
