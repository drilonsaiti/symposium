<?php

namespace Talk;

use App\Enum\QuestionType;
use App\Enum\TalkSubmissionStatus;
use App\Events\TalkWasSubmitted;
use App\Models\CfpAnswer;
use App\Models\CfpQuestion;
use App\Models\Conference;
use App\Models\ConferenceTalk;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');

    Event::fake();
});

uses(RefreshDatabase::class);

function makeOpenConference(array $attributes = []): Conference
{
    return Conference::factory()->create([
        'cfp_starts_at' => now()->subDay(),
        'cfp_ends_at' => now()->addDay(),
        ...$attributes,
    ]);
}

function makeCfpQuestion(
    Conference $conference,
    array $attributes = []
): CfpQuestion {
    return CfpQuestion::create([
        'conference_id' => $conference->id,
        'question' => 'Test question',
        'type' => QuestionType::FREE_TEXT,
        'required' => false,
        'is_active' => true,
        'position' => 1,
        ...$attributes,
    ]);
}

it('speaker can submit a talk to a conference', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'bio_id' => null,
            'answers' => [],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionHas('status', 'Talk submitted successfully.');

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'status' => TalkSubmissionStatus::PENDING->value,
    ]);

    Event::assertDispatched(TalkWasSubmitted::class);
});

it('saves cfp answers when submitting a talk', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $question = makeCfpQuestion($conference, [
        'question' => 'Do you need any AV equipment?',
        'required' => true,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [
                $question->id => 'I need a projector.',
            ],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionHas('status', 'Talk submitted successfully.');

    $submission = ConferenceTalk::where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    $this->assertDatabaseHas('cfp_answers', [
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $question->id,
        'answer' => 'I need a projector.',
    ]);
});

it('requires an answer for a required cfp question', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $question = makeCfpQuestion($conference, [
        'question' => 'Why do you want to give this talk?',
        'required' => true,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [],
        ])
        ->assertSessionHasErrors(
            "answers.{$question->id}"
        );

    $this->assertDatabaseMissing('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);

    Event::assertNotDispatched(TalkWasSubmitted::class);
});

it('allows an optional cfp question to remain unanswered', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    makeCfpQuestion($conference, [
        'question' => 'Any additional comments?',
        'required' => false,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionDoesntHaveErrors()
        ->assertSessionHas('status', 'Talk submitted successfully.');

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);

    Event::assertDispatched(TalkWasSubmitted::class);
});

it('ignores inactive cfp questions during submission validation', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    makeCfpQuestion($conference, [
        'question' => 'Old required question',
        'required' => true,
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionDoesntHaveErrors()
        ->assertSessionHas('status', 'Talk submitted successfully.');

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);
});

it('does not submit the same talk twice', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $conference->talks()->attach($talk->id, [
        'status' => TalkSubmissionStatus::PENDING->value,
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionHas('status', 'Talk was already submitted.');

    expect(
        ConferenceTalk::where('conference_id', $conference->id)
            ->where('talk_id', $talk->id)
            ->count()
    )->toBe(1);

    Event::assertNotDispatched(TalkWasSubmitted::class);
});

it('does not change existing cfp answers when submitting the same talk twice', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $question = makeCfpQuestion($conference, [
        'question' => 'What equipment do you need?',
        'required' => false,
    ]);

    $conference->talks()->attach($talk->id, [
        'status' => TalkSubmissionStatus::PENDING->value,
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $submission = ConferenceTalk::where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    CfpAnswer::create([
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $question->id,
        'answer' => 'Original answer',
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [
                $question->id => 'Changed answer',
            ],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionHas('status', 'Talk was already submitted.');

    $this->assertDatabaseHas('cfp_answers', [
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $question->id,
        'answer' => 'Original answer',
    ]);

    $this->assertDatabaseMissing('cfp_answers', [
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $question->id,
        'answer' => 'Changed answer',
    ]);

    Event::assertNotDispatched(TalkWasSubmitted::class);
});

it('saves multiple cfp answers for a submission', function () {
    $user = makeUser();

    $conference = makeOpenConference();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $firstQuestion = makeCfpQuestion($conference, [
        'question' => 'What equipment do you need?',
        'required' => true,
        'position' => 1,
    ]);

    $secondQuestion = makeCfpQuestion($conference, [
        'question' => 'Have you spoken publicly before?',
        'required' => true,
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.talks.submit', [$conference, $talk]), [
            'answers' => [
                $firstQuestion->id => 'Projector and microphone',
                $secondQuestion->id => 'Yes, several times',
            ],
        ])
        ->assertRedirect(route('conferences.show', $conference))
        ->assertSessionHas('status', 'Talk submitted successfully.');

    $submission = ConferenceTalk::where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    $this->assertDatabaseHas('cfp_answers', [
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $firstQuestion->id,
        'answer' => 'Projector and microphone',
    ]);

    $this->assertDatabaseHas('cfp_answers', [
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $secondQuestion->id,
        'answer' => 'Yes, several times',
    ]);

    expect($submission->answers()->count())->toBe(2);
});

it('conference talk has many cfp answers', function () {
    $conference = makeOpenConference();

    $talk = Talk::factory()->create();

    $conference->talks()->attach($talk->id, [
        'status' => TalkSubmissionStatus::PENDING->value,
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $submission = ConferenceTalk::where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    $firstQuestion = makeCfpQuestion($conference, [
        'question' => 'First question',
        'position' => 1,
    ]);

    $secondQuestion = makeCfpQuestion($conference, [
        'question' => 'Second question',
        'position' => 2,
    ]);

    CfpAnswer::create([
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $firstQuestion->id,
        'answer' => 'First answer',
    ]);

    CfpAnswer::create([
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $secondQuestion->id,
        'answer' => 'Second answer',
    ]);

    expect($submission->answers)
        ->toHaveCount(2);

    expect(
        $submission->answers
            ->pluck('answer')
            ->all()
    )->toEqualCanonicalizing([
        'First answer',
        'Second answer',
    ]);
});
