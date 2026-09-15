<?php

namespace App\Http\Controllers;

use App\Enum\ConferenceReviewerStatus;
use App\Http\Requests\StoreConferenceReviewerRequest;
use App\Models\Conference;
use App\Models\User;
use App\Notifications\ReviewerInvitedNotification;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ConferenceReviewerController extends Controller
{
    //
    use AuthorizesRequests;

    public function store(StoreConferenceReviewerRequest $request, Conference $conference)
    {
        $validated = $request->validated();

        $reviewer = User::query()
            ->byUsernameOrEmail($validated['reviewer'])
            ->firstOrFail();

        try {
            $conference->reviewerInvitations()->attach($reviewer->id, [
                'status' => ConferenceReviewerStatus::PENDING,
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()
                    ->with('status', 'Reviewer was already invited.');
            }

            throw $e;
        }

        $reviewer->notify(
            new ReviewerInvitedNotification($conference)
        );

        return back()
            ->with('status', 'Reviewer invited successfully.');
    }

    public function destroy(Conference $conference, User $user)
    {
        $this->authorize('manageReviewers', $conference);

        $conference->reviewerInvitations()->detach($user->id);

        return back()
            ->with('status', 'Reviewer removed successfully.');
    }
}
