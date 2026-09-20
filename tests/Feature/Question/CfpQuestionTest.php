<?php

namespace CfpQuestion;

use App\Enum\QuestionType;
use App\Models\CfpAnswer;
use App\Models\CfpQuestion;
use App\Models\Conference;
use App\Models\ConferenceTalk;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeConferenceForUser($user, array $attributes = []): Conference
{
    return Conference::factory()->create([
        'user_id' => $user->id,
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

it('stores a cfp question', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $this->actingAs($user)
        ->post(route('conferences.questions.store', $conference), [
            'question' => 'Do you need a projector?',
            'type' => QuestionType::FREE_TEXT->value,
            'required' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cfp_questions', [
        'conference_id' => $conference->id,
        'question' => 'Do you need a projector?',
        'required' => true,
        'position' => 1,
    ]);
});

it('places a new cfp question after existing active questions', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    makeCfpQuestion($conference, [
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.questions.store', $conference), [
            'question' => 'Third question',
            'type' => QuestionType::FREE_TEXT->value,
            'required' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cfp_questions', [
        'conference_id' => $conference->id,
        'question' => 'Third question',
        'position' => 3,
    ]);
});

it('ignores inactive questions when determining the next position', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    makeCfpQuestion($conference, [
        'position' => 10,
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->post(route('conferences.questions.store', $conference), [
            'question' => 'Second active question',
            'type' => QuestionType::FREE_TEXT->value,
            'required' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cfp_questions', [
        'conference_id' => $conference->id,
        'question' => 'Second active question',
        'position' => 2,
    ]);
});

it('updates a cfp question', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $question = makeCfpQuestion($conference, [
        'question' => 'Old question',
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.update', [$conference, $question]),
            [
                'question' => 'Updated question',
                'type' => QuestionType::FREE_TEXT->value,
                'required' => true,
            ]
        )
        ->assertRedirect();

    $this->assertDatabaseHas('cfp_questions', [
        'id' => $question->id,
        'question' => 'Updated question',
        'required' => true,
    ]);
});

it('does not update a question belonging to another conference', function () {
    $user = makeUser();

    $conference = makeConferenceForUser($user);
    $otherConference = makeConferenceForUser($user);

    $question = makeCfpQuestion($otherConference);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.update', [$conference, $question]),
            [
                'question' => 'Updated question',
                'type' => QuestionType::FREE_TEXT->value,
                'required' => false,
            ]
        )
        ->assertNotFound();

    $this->assertDatabaseHas('cfp_questions', [
        'id' => $question->id,
        'question' => 'Test question',
    ]);
});

it('deletes a cfp question when it has no answers', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $question = makeCfpQuestion($conference);

    $this->actingAs($user)
        ->delete(
            route('conferences.questions.destroy', [$conference, $question])
        )
        ->assertRedirect();

    $this->assertDatabaseMissing('cfp_questions', [
        'id' => $question->id,
    ]);
});

it('archives a cfp question when it already has answers', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $talk = Talk::factory()->create();

    $question = makeCfpQuestion($conference);

    $conference->talks()->attach($talk->id, [
        'talk_revision_id' => $talk->currentRevision?->id,
    ]);

    $submission = ConferenceTalk::where('conference_id', $conference->id)
        ->where('talk_id', $talk->id)
        ->firstOrFail();

    CfpAnswer::create([
        'conference_talk_id' => $submission->id,
        'cfp_question_id' => $question->id,
        'answer' => 'Some answer',
    ]);

    $this->actingAs($user)
        ->delete(
            route('conferences.questions.destroy', [$conference, $question])
        )
        ->assertRedirect();

    $this->assertDatabaseHas('cfp_questions', [
        'id' => $question->id,
        'is_active' => false,
    ]);
});

it('moves a cfp question up', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $first = makeCfpQuestion($conference, [
        'question' => 'First',
        'position' => 1,
    ]);

    $second = makeCfpQuestion($conference, [
        'question' => 'Second',
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $second]),
            ['direction' => 'up']
        )
        ->assertRedirect();

    expect($first->fresh()->position)->toBe(2)
        ->and($second->fresh()->position)->toBe(1);
});

it('moves a cfp question down', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $first = makeCfpQuestion($conference, [
        'question' => 'First',
        'position' => 1,
    ]);

    $second = makeCfpQuestion($conference, [
        'question' => 'Second',
        'position' => 2,
    ]);

    $third = makeCfpQuestion($conference, [
        'question' => 'Third',
        'position' => 3,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $first]),
            ['direction' => 'down']
        )
        ->assertRedirect();

    expect($first->fresh()->position)->toBe(2)
        ->and($second->fresh()->position)->toBe(1)
        ->and($third->fresh()->position)->toBe(3);
});

it('does nothing when moving the first question up', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $first = makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    $second = makeCfpQuestion($conference, [
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $first]),
            ['direction' => 'up']
        )
        ->assertRedirect();

    expect($first->fresh()->position)->toBe(1)
        ->and($second->fresh()->position)->toBe(2);
});

it('does nothing when moving the last question down', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $first = makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    $second = makeCfpQuestion($conference, [
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $second]),
            ['direction' => 'down']
        )
        ->assertRedirect();

    expect($first->fresh()->position)->toBe(1)
        ->and($second->fresh()->position)->toBe(2);
});

it('skips inactive questions when moving down', function () {
    $user = makeUser();
    $conference = makeConferenceForUser($user);

    $first = makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    $inactive = makeCfpQuestion($conference, [
        'position' => 2,
        'is_active' => false,
    ]);

    $third = makeCfpQuestion($conference, [
        'position' => 3,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $first]),
            ['direction' => 'down']
        )
        ->assertRedirect();

    expect($first->fresh()->position)->toBe(3)
        ->and($inactive->fresh()->position)->toBe(2)
        ->and($third->fresh()->position)->toBe(1);
});

it('never reorders a question with a question from another conference', function () {
    $user = makeUser();

    $conference = makeConferenceForUser($user);
    $otherConference = makeConferenceForUser($user);

    $question = makeCfpQuestion($conference, [
        'position' => 1,
    ]);

    $otherQuestion = makeCfpQuestion($otherConference, [
        'position' => 2,
    ]);

    $this->actingAs($user)
        ->patch(
            route('conferences.questions.move', [$conference, $question]),
            ['direction' => 'down']
        )
        ->assertRedirect();

    expect($question->fresh()->position)->toBe(1)
        ->and($otherQuestion->fresh()->position)->toBe(2);
});
