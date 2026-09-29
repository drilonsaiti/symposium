<?php

namespace Tests\Feature\Conference;

use App\Enum\ConferenceReviewerStatus;
use App\Enum\ReviewRecommendation;
use App\Models\Conference;
use App\Models\ConferenceTalk;
use App\Models\ConferenceTalkReview;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

function makeConferenceWithSubmission(): array
{
    $owner = makeUser();
    $speaker = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $speaker->id,
    ]);

    $conference->talks()->attach($talk->id, [
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $submission = ConferenceTalk::query()
        ->where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    return compact('owner', 'speaker', 'conference', 'talk', 'submission');
}

function addReviewerWithStatus(Conference $conference, ConferenceReviewerStatus $status)
{
    $reviewer = makeUser();

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => $status,
    ]);

    return $reviewer;
}

function addAcceptedReviewer(Conference $conference)
{
    return addReviewerWithStatus($conference, ConferenceReviewerStatus::ACCEPTED);
}

function addPendingReviewer(Conference $conference)
{
    return addReviewerWithStatus($conference, ConferenceReviewerStatus::PENDING);
}

/*
|--------------------------------------------------------------------------
| Creating / updating reviews
|--------------------------------------------------------------------------
*/

it('allows an accepted reviewer to create a review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Strong proposal.',
            ]
        )
        ->assertRedirect()
        ->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 5,
        'recommendation' => ReviewRecommendation::YES->value,
        'note' => 'Strong proposal.',
    ]);
});

it('allows an accepted reviewer to update their own review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 3,
        'recommendation' => ReviewRecommendation::MAYBE,
        'note' => 'Needs work.',
    ]);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Changed my mind after reviewing it again.',
            ]
        )
        ->assertRedirect()
        ->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 5,
        'recommendation' => ReviewRecommendation::YES->value,
        'note' => 'Changed my mind after reviewing it again.',
    ]);

    expect(
        ConferenceTalkReview::query()
            ->where('conference_talk_id', $submission->id)
            ->where('user_id', $reviewer->id)
            ->count()
    )->toBe(1);
});

it('does not create a second review row when reviewer submits again', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 4,
                'recommendation' => ReviewRecommendation::MAYBE->value,
                'note' => 'First review.',
            ]
        )
        ->assertRedirect();

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Updated review.',
            ]
        )
        ->assertRedirect();

    expect(
        ConferenceTalkReview::query()
            ->where('conference_talk_id', $submission->id)
            ->where('user_id', $reviewer->id)
            ->count()
    )->toBe(1);

    $this->assertDatabaseHas('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 5,
        'recommendation' => ReviewRecommendation::YES->value,
        'note' => 'Updated review.',
    ]);
});

it('stores review against the correct conference talk submission', function () {
    $owner = makeUser();
    $speaker = makeUser();

    $firstConference = Conference::factory()->create(['user_id' => $owner->id]);
    $secondConference = Conference::factory()->create(['user_id' => $owner->id]);

    $talk = Talk::factory()->create(['user_id' => $speaker->id]);

    $firstConference->talks()->attach($talk->id, [
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $secondConference->talks()->attach($talk->id, [
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $firstSubmission = ConferenceTalk::query()
        ->where('conference_id', $firstConference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    $secondSubmission = ConferenceTalk::query()
        ->where('conference_id', $secondConference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    $reviewer = addAcceptedReviewer($firstConference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$firstConference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Review for first conference.',
            ]
        )
        ->assertRedirect()
        ->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas('conference_talk_reviews', [
        'conference_talk_id' => $firstSubmission->id,
        'user_id' => $reviewer->id,
    ]);

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $secondSubmission->id,
        'user_id' => $reviewer->id,
    ]);
});

it('returns 404 when the talk was not submitted to the conference', function () {
    $conference = Conference::factory()->create(['user_id' => makeUser()->id]);

    $talk = Talk::factory()->create(['user_id' => makeUser()->id]);

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
            ]
        )
        ->assertNotFound();
});

/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
*/

it('redirects guests to login', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
    ] = makeConferenceWithSubmission();

    $this->post(
        route('conferences.talks.review.store', [$conference, $talk]),
        [
            'score' => 5,
            'recommendation' => ReviewRecommendation::YES->value,
        ]
    )->assertRedirect(route('login'));

    $this->assertDatabaseCount('conference_talk_reviews', 0);
});

it('does not allow a pending reviewer to submit a review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addPendingReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 4,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Should not be saved.',
            ]
        )
        ->assertForbidden();

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
    ]);
});

it('does not allow a declined reviewer to submit a review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    // Adjust DECLINED to whatever your enum case is actually called.
    $reviewer = addReviewerWithStatus($conference, ConferenceReviewerStatus::DECLINED);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
            ]
        )
        ->assertForbidden();

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
    ]);
});

it('does not allow a reviewer of another conference to submit a review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $otherConference = Conference::factory()->create(['user_id' => makeUser()->id]);
    $reviewer = addAcceptedReviewer($otherConference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
            ]
        )
        ->assertForbidden();

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
    ]);
});

it('does not allow an unrelated user to submit a review', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $user = makeUser();

    $this->actingAs($user)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
            ]
        )
        ->assertForbidden();

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $user->id,
    ]);
});

it('does not allow the conference owner to submit a review', function () {
    [
        'owner' => $owner,
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $this->actingAs($owner)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 5,
                'recommendation' => ReviewRecommendation::YES->value,
                'note' => 'Owner review.',
            ]
        )
        ->assertForbidden();

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $owner->id,
    ]);
});

it('owner can view submissions but cannot create reviews', function () {
    [
        'owner' => $owner,
        'conference' => $conference,
    ] = makeConferenceWithSubmission();

    expect(Gate::forUser($owner)->allows('viewSubmissions', $conference))->toBeTrue();

    expect(
        Gate::forUser($owner)->allows('create', [ConferenceTalkReview::class, $conference])
    )->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

it('requires a score', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            ['recommendation' => ReviewRecommendation::YES->value]
        )
        ->assertSessionHasErrors('score');
});

it('requires score to be between one and five', function (int $score) {
    [
        'conference' => $conference,
        'talk' => $talk,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => $score,
                'recommendation' => ReviewRecommendation::YES->value,
            ]
        )
        ->assertSessionHasErrors('score');
})->with([
    'zero' => 0,
    'six' => 6,
    'negative' => -1,
]);

it('requires a valid recommendation', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 4,
                'recommendation' => 'absolutely',
            ]
        )
        ->assertSessionHasErrors('recommendation');
});

it('allows the review note to be nullable', function () {
    [
        'conference' => $conference,
        'talk' => $talk,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $this->actingAs($reviewer)
        ->post(
            route('conferences.talks.review.store', [$conference, $talk]),
            [
                'score' => 4,
                'recommendation' => ReviewRecommendation::MAYBE->value,
                'note' => null,
            ]
        )
        ->assertRedirect()
        ->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas('conference_talk_reviews', [
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 4,
        'recommendation' => ReviewRecommendation::MAYBE->value,
        'note' => null,
    ]);
});

/*
|--------------------------------------------------------------------------
| Visibility (what each role sees on the conference page)
|--------------------------------------------------------------------------
*/

it('reviewer sees only their own review', function () {
    [
        'conference' => $conference,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $firstReviewer = addAcceptedReviewer($conference);
    $secondReviewer = addAcceptedReviewer($conference);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $firstReviewer->id,
        'score' => 5,
        'recommendation' => ReviewRecommendation::YES,
        'note' => 'First reviewer private note.',
    ]);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $secondReviewer->id,
        'score' => 2,
        'recommendation' => ReviewRecommendation::NO,
        'note' => 'Second reviewer private note.',
    ]);

    $this->actingAs($firstReviewer)
        ->get(route('conferences.show', $conference))
        ->assertOk()
        ->assertSee('First reviewer private note.')
        ->assertDontSee('Second reviewer private note.');
});

it('conference owner can see all reviewer reviews', function () {
    [
        'owner' => $owner,
        'conference' => $conference,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $firstReviewer = addAcceptedReviewer($conference);
    $secondReviewer = addAcceptedReviewer($conference);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $firstReviewer->id,
        'score' => 5,
        'recommendation' => ReviewRecommendation::YES,
        'note' => 'First reviewer note.',
    ]);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $secondReviewer->id,
        'score' => 3,
        'recommendation' => ReviewRecommendation::MAYBE,
        'note' => 'Second reviewer note.',
    ]);

    $this->actingAs($owner)
        ->get(route('conferences.show', $conference))
        ->assertOk()
        ->assertSee('First reviewer note.')
        ->assertSee('Second reviewer note.');
});

it('speaker cannot see reviewer review data', function () {
    [
        'speaker' => $speaker,
        'conference' => $conference,
        'submission' => $submission,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 1,
        'recommendation' => ReviewRecommendation::NO,
        'note' => 'Private reviewer feedback that speaker must not see.',
    ]);

    // The conference page is where reviews are rendered, so test it there.
    $this->actingAs($speaker)
        ->get(route('conferences.show', $conference))
        ->assertOk()
        ->assertDontSee('Private reviewer feedback that speaker must not see.');
});

/*
|--------------------------------------------------------------------------
| Cascade
|--------------------------------------------------------------------------
*/

it('deletes reviews when the talk is removed from the conference', function () {
    [
        'submission' => $submission,
        'conference' => $conference,
        'talk' => $talk,
    ] = makeConferenceWithSubmission();

    $reviewer = addAcceptedReviewer($conference);

    $review = ConferenceTalkReview::create([
        'conference_talk_id' => $submission->id,
        'user_id' => $reviewer->id,
        'score' => 4,
        'recommendation' => ReviewRecommendation::YES,
        'note' => 'Will be deleted.',
    ]);

    $conference->talks()->detach($talk->id);

    $this->assertDatabaseMissing('conference_talk_reviews', [
        'id' => $review->id,
    ]);
});
