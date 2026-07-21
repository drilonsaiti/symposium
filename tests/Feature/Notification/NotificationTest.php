<?php

namespace Notification;

use App\Enum\TalkSubmissionStatus;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use App\Events\TalkWasSubmitted;
use App\Events\SubmissionStatusChanged;
use App\Listeners\NotifyConferenceOwnerOfSubmission;
use App\Listeners\NotifySubmitterOfStatusChange;
use App\Notifications\TalkSubmittedNotification;
use App\Notifications\SubmissionStatusChangedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');
});

uses(RefreshDatabase::class);

it('notifies conference owner when a talk is submitted', function () {
    Notification::fake();

    $talkUser = makeUser();
    $conferenceOwner = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceOwner->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $event = new TalkWasSubmitted($conference, $talk);

    (new NotifyConferenceOwnerOfSubmission())->handle($event);

    Notification::assertSentTo(
        $conferenceOwner,
        TalkSubmittedNotification::class,
        function (
            TalkSubmittedNotification $notification,
            array $channels
        ) use ($conference, $talk) {
            return $notification->conference->is($conference)
                && $notification->talk->is($talk)
                && in_array('database', $channels);
        }
    );
});

it('notifies talk submitter when submission status changes', function () {
    Notification::fake();

    $talkUser = makeUser();
    $conferenceOwner = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceOwner->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $event = new SubmissionStatusChanged(
        $conference,
        $talk,
        TalkSubmissionStatus::ACCEPTED
    );

    (new NotifySubmitterOfStatusChange())->handle($event);

    Notification::assertSentTo(
        $talkUser,
        SubmissionStatusChangedNotification::class,
        function (
            SubmissionStatusChangedNotification $notification,
            array $channels
        ) use ($conference, $talk) {
            return $notification->conference->is($conference)
                && $notification->talk->is($talk)
                && $notification->status === TalkSubmissionStatus::ACCEPTED;
        }
    );
});

it('creates the correct talk submitted notification data', function () {
    $conferenceOwner = makeUser();
    $talkUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceOwner->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
        'title' => 'Laravel Testing',
    ]);

    $notification = new TalkSubmittedNotification(
        $conference,
        $talk
    );

    $data = $notification->toDatabase($conferenceOwner);

    expect($data)
        ->type->toBe('talk_submitted')
        ->title->toBe('New talk submitted')
        ->conference_id->toBe($conference->id)
        ->talk_id->toBe($talk->id)
        ->talk_title->toBe('Laravel Testing');
});

it('creates the correct accepted status notification data', function () {
    $conferenceOwner = makeUser();
    $talkUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceOwner->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
        'title' => 'Laravel Testing',
    ]);

    $notification = new SubmissionStatusChangedNotification(
        $conference,
        $talk,
        TalkSubmissionStatus::ACCEPTED
    );

    $data = $notification->toDatabase($talkUser);

    expect($data)
        ->type->toBe('submission_status_changed')
        ->title->toBe('Talk accepted')
        ->status->toBe('accepted')
        ->talk_id->toBe($talk->id);
});

it('sends email when a talk is accepted', function () {
    $conference = Conference::factory()->create();
    $talkUser = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $notification = new SubmissionStatusChangedNotification(
        $conference,
        $talk,
        TalkSubmissionStatus::ACCEPTED
    );

    expect($notification->via($talkUser))
        ->toContain('database')
        ->toContain('mail');
});

it('sends email when a talk is rejected', function () {
    $conference = Conference::factory()->create();
    $talkUser = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $notification = new SubmissionStatusChangedNotification(
        $conference,
        $talk,
        TalkSubmissionStatus::REJECTED
    );

    expect($notification->via($talkUser))
        ->toContain('database')
        ->toContain('mail');
});

it('does not send email for pending status', function () {
    $conference = Conference::factory()->create();
    $talkUser = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $notification = new SubmissionStatusChangedNotification(
        $conference,
        $talk,
        TalkSubmissionStatus::PENDING
    );

    expect($notification->via($talkUser))
        ->toContain('database')
        ->not->toContain('mail');
});
