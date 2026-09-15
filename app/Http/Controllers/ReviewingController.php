<?php

namespace App\Http\Controllers;

use App\Enum\ConferenceReviewerStatus;
use App\Models\Conference;
use Illuminate\Http\Request;

class ReviewingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $reviewingConferences  = $user->reviewingConferences;
        $pendingInvitations = $user->pendingReviewerInvitations;

        return view('reviewing.index', compact('reviewingConferences','pendingInvitations'));
    }

    public function accept(Conference $conference)
    {
        $user = auth()->user();

        $invitation = $conference->reviewerInvitations()
            ->whereKey($user->id)
            ->firstOrFail();

        $currentStatus = $invitation->pivot->status;

        abort_unless($currentStatus->canTransitionTo(ConferenceReviewerStatus::ACCEPTED), 409);

        $conference->reviewerInvitations()->updateExistingPivot($user->id,[
            'status' => ConferenceReviewerStatus::ACCEPTED
        ]);

        return redirect()->route('conferences.show', $conference);
    }

    public function decline(Conference $conference)
    {
        $user = auth()->user();

        $conference->pendingReviewers()
            ->whereKey($user->id)
            ->firstOrFail();

        $conference->reviewerInvitations()->detach($user->id);

        return redirect()->route('reviewing.index');
    }
}
