<?php

namespace Talk;

use App\Enum\TalkSubmissionStatus;
use App\Enum\TalkType;
use App\Models\Conference;
use App\Models\Tag;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use App\Events\TalkWasSubmitted;
use App\Events\SubmissionStatusChanged;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');
});

uses(RefreshDatabase::class);


it('authenticated user sees only their own talks', function () {
    $this->withoutExceptionHandling();

    $user = makeUser();

    $talk = Talk::factory()->create(['user_id' => $user->id]);
    $otherTalk = Talk::factory()->create();

    $this->actingAs($user)->get(route('talks.index'))->assertSee($talk->title);
    $this->actingAs($user)->get(route('talks.index'))->assertDontSee($otherTalk->title);
});

it('guest redirected to login page', function () {
    $this->get(route('talks.index'))->assertRedirect(route('login'));
});

it('authenticated user can create talk', function () {
    $user = makeUser();
    $this->actingAs($user)->get(route('talks.create'))->assertOk();
    $this->actingAs($user)->post(route('talks.store'), [
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));
    $this->assertDatabaseHas('talks', [
        'title' => 'Test Title',
    ]);
});

it('validation fails with missing title', function () {
    $user = makeUser();
    $this->actingAs($user)->post(route('talks.store'), [
        'title' => '',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertSessionHasErrors();

    $this->assertDatabaseMissing('talks', [
        'title' => '',
    ]);
});

it('user_id is set from auth,not from request input', function () {
    $user = makeUser();
    $this->actingAs($user)->post(route('talks.store'), [
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));
    $this->assertDatabaseHas('talks', [
        'user_id' => $user->id,
    ]);
});

it('owner can edit their talk', function () {
    $user = makeUser();
    $talk = Talk::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user)->get(route('talks.edit', $talk))->assertOk();

    $this->actingAs($user)->put(route('talks.update', $talk), [
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));

    $this->assertDatabaseHas('talks', [
        'title' => 'Test Title',
    ]);
});

it('another authenticated user cannot edit someone else talk', function () {
    $user = makeUser();
    $otherUser = makeUser();
    $talk = Talk::factory()->create(['user_id' => $otherUser->id]);
    $this->actingAs($user)->get(route('talks.edit', $talk))->assertForbidden();
    $this->actingAs($user)->put(route('talks.update', $talk), [
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertForbidden();
});

it('owner can delete their talk', function () {
    $user = makeUser();
    $talk = Talk::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user)->delete(route('talks.destroy', $talk))->assertRedirect(route('talks.index'));
    $this->assertDatabaseMissing('talks', ['id' => $talk->id]);
});

it('another authenticated user cannot delete someone else talk', function () {
    $user = makeUser();
    $otherUser = makeUser();
    $talk = Talk::factory()->create(['user_id' => $otherUser->id]);
    $this->actingAs($user)->delete(route('talks.destroy', $talk))->assertForbidden();
});

it('submit talk to conference', function () {
    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $this->actingAs($talkUser)->post(route('conferences.talks.submit', [
        'conference' => $conference,
        'talk' => $talk,
    ]))->assertRedirect(route('conferences.show', $conference));

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);
});


it('change status of submitted talk', function () {
    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $this->actingAs($talkUser)->post(route('conferences.talks.submit', [
        'conference' => $conference,
        'talk' => $talk,
    ]))->assertRedirect(route('conferences.show', $conference));

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status', [$conference, $talk]), [
            'status' => 'accepted',
        ]
    );

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'status' => 'accepted',
    ]);
});

it('cannot change status from rejected', function () {
    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $this->actingAs($talkUser)->post(route('conferences.talks.submit', [
        'conference' => $conference,
        'talk' => $talk,
    ]))->assertRedirect(route('conferences.show', $conference));

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
    ]);

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status', [$conference, $talk]), [
            'status' => 'rejected',
        ]
    );

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'status' => 'rejected',
    ]);

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status', [$conference, $talk]), [
            'status' => 'accepted',
        ]
    )->assertStatus(302);

});

it('creating a talk creates a first revision', function () {
    $user = makeUser();

    $this->actingAs($user)->post(route('talks.store'), [
        'title' => 'My Talk',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
        'abstract' => 'First abstract',
    ]);

    $talk = Talk::first();

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'First abstract',
    ]);

    expect($talk->revisions)->toHaveCount(1);
});

it('updating abstract creates a new revision', function () {
    $user = makeUser();

    $talk = Talk::factory()->create(['user_id' => $user->id]);

    $talk->revisions()->create([
        'abstract' => 'Original abstract',
    ]);

    $this->actingAs($user)->put(route('talks.update', $talk), [
        'title' => $talk->title,
        'length' => $talk->length,
        'type' => TalkType::STANDARD->value,
        'abstract' => 'Updated abstract',
    ])->assertRedirect(route('talks.index'));

    expect($talk->fresh()->revisions)->toHaveCount(2);

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'Original abstract',
    ]);

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'Updated abstract',
    ]);
});

it('submitting a talk stores the current revision on the pivot', function () {
    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $oldRevision = $talk->revisions()->create([
        'abstract' => 'Old version',
    ]);

    $latestRevision = $talk->revisions()->create([
        'abstract' => 'Newest version',
    ]);

    $this->actingAs($talkUser)->post(route('conferences.talks.submit', [
        'conference' => $conference,
        'talk' => $talk,
    ]));

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'talk_revision_id' => $latestRevision->id,
    ]);
});

it('talk with no abstract has no revision', function () {
    $user = makeUser();

    $this->actingAs($user)->post(route('talks.store'), [
        'title' => 'No Abstract Talk',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ]);

    $talk = $user->talks()->first();

    $this->assertDatabaseMissing('talk_revisions', [
        'talk_id' => $talk->id,
    ]);

    expect($talk->revisions)->toHaveCount(0);
});

it('restoring a revision creates a new row with the same abstract', function () {
    $user = makeUser();
    $talk = Talk::factory()->create(['user_id' => $user->id]);

    $oldRevision = $talk->revisions()->create([
        'abstract' => 'Old version',
    ]);

    $talk->revisions()->create([
        'abstract' => 'Newest version',
    ]);

    $this->actingAs($user)->post(route('talks.revisions.restore', [$talk, $oldRevision]))
        ->assertRedirect(route('talks.revisions.index', $talk));

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'Old version',
    ]);

    expect($talk->fresh()->currentRevision->abstract)->toBe('Old version');
});

it('old revisions are preserved when restoring', function () {
    $user = makeUser();
    $talk = Talk::factory()->create(['user_id' => $user->id]);

    $oldRevision = $talk->revisions()->create([
        'abstract' => 'Old version',
    ]);

    $talk->revisions()->create([
        'abstract' => 'Newest version',
    ]);

    expect($talk->fresh()->revisions)->toHaveCount(2);

    $this->actingAs($user)->post(route('talks.revisions.restore', [$talk, $oldRevision]));

    expect($talk->fresh()->revisions)->toHaveCount(3);

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'Newest version',
    ]);

    $this->assertDatabaseHas('talk_revisions', [
        'talk_id' => $talk->id,
        'abstract' => 'Old version',
    ]);
});

it('non-owner cannot restore a revision', function () {
    $user = makeUser();
    $otherUser = makeUser();
    $talk = Talk::factory()->create(['user_id' => $otherUser->id]);

    $oldRevision = $talk->revisions()->create([
        'abstract' => 'Old version',
    ]);

    $talk->revisions()->create([
        'abstract' => 'Newest version',
    ]);

    $this->actingAs($user)->post(route('talks.revisions.restore', [$talk, $oldRevision]))
        ->assertForbidden();

    expect($talk->fresh()->revisions)->toHaveCount(2);
});

it('dispatches TalkWasSubmitted event when a talk is submitted', function () {
    Event::fake([TalkWasSubmitted::class]);

    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $this->actingAs($talkUser)
        ->post(route('conferences.talks.submit', [
            'conference' => $conference,
            'talk' => $talk,
        ]));

    Event::assertDispatched(
        TalkWasSubmitted::class,
        function (TalkWasSubmitted $event) use ($conference, $talk) {
            return $event->conference->is($conference)
                && $event->talk->is($talk);
        }
    );
});

it('dispatches SubmissionStatusChanged event when status changes', function () {
    Event::fake([SubmissionStatusChanged::class]);

    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $this->actingAs($talkUser)->post(
        route('conferences.talks.submit', [
            'conference' => $conference,
            'talk' => $talk,
        ])
    );

    $this->actingAs($conferenceUser)
        ->patch(
            route('conferences.talks.status', [$conference, $talk]),
            ['status' => 'accepted']
        );

    Event::assertDispatched(
        SubmissionStatusChanged::class,
        function (SubmissionStatusChanged $event) use ($conference, $talk) {
            return $event->conference->is($conference)
                && $event->talk->is($talk)
                && $event->status === TalkSubmissionStatus::ACCEPTED;
        }
    );
});

it('rate limits revision restores', function () {
    $user = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $revision = $talk->revisions()->create([
        'abstract' => 'Old version',
    ]);

    $request = fn () => $this->actingAs($user)
        ->post(route('talks.revisions.restore', [
            'talk' => $talk,
            'revision' => $revision,
        ]));

    foreach (range(1, 5) as $i) {
        $request()->assertRedirect();
    }

    $request()->assertStatus(429);
});

it('rate limits talk submissions', function () {
    $talkUser = makeUser();
    $conferenceUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $conferenceUser->id,
    ]);

    $talk = Talk::factory()->create([
        'user_id' => $talkUser->id,
    ]);

    $request = fn () => $this->actingAs($talkUser)
        ->post(route('conferences.talks.submit', [
            'conference' => $conference,
            'talk' => $talk,
        ]));

    foreach (range(1, 5) as $i) {
        $request()->assertRedirect();
    }

    $request()->assertStatus(429);
});

it('speaker can attach tags to a talk on create', function () {
    $user = makeUser();
    $tags = Tag::factory()->count(3)->create();

    $this->actingAs($user)
        ->post(route('talks.store'), [
            'title' => 'Tagged Talk',
            'length' => 30,
            'type' => TalkType::STANDARD->value,
            'tags' => $tags->pluck('id')->all(),
        ])
        ->assertRedirect(route('talks.index'));

    $talk = Talk::where('title', 'Tagged Talk')->firstOrFail();

    expect($talk->tags()->pluck('tags.id')->all())
        ->toEqualCanonicalizing($tags->pluck('id')->all());

    foreach ($tags as $tag) {
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $talk->id,
            'taggable_type' => Talk::class,
        ]);
    }
});

it('max 5 tags are enforced when creating a talk', function () {
    $user = makeUser();
    $tags = Tag::factory()->count(6)->create();

    $this->actingAs($user)
        ->post(route('talks.store'), [
            'title' => 'Too Many Tags',
            'length' => 30,
            'type' => TalkType::STANDARD->value,
            'tags' => $tags->pluck('id')->all(),
        ])
        ->assertSessionHasErrors('tags');

    $this->assertDatabaseMissing('talks', [
        'title' => 'Too Many Tags',
    ]);
});

it('non-existent tag id is rejected when creating a talk', function () {
    $user = makeUser();

    $nonExistentTagId = 999999;

    $this->actingAs($user)
        ->post(route('talks.store'), [
            'title' => 'Invalid Tag Talk',
            'length' => 30,
            'type' => TalkType::STANDARD->value,
            'tags' => [$nonExistentTagId],
        ])
        ->assertSessionHasErrors('tags.0');

    $this->assertDatabaseMissing('talks', [
        'title' => 'Invalid Tag Talk',
    ]);
});

it('talks index can be filtered by tag slug', function () {
    $user = makeUser();

    $php = Tag::factory()->create([
        'name' => 'PHP',
        'slug' => 'php',
    ]);

    $laravel = Tag::factory()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $matchingTalk = Talk::factory()->create([
        'user_id' => $user->id,
        'title' => 'PHP Internals',
    ]);

    $nonMatchingTalk = Talk::factory()->create([
        'user_id' => $user->id,
        'title' => 'Laravel Testing',
    ]);

    $matchingTalk->tags()->attach($php);
    $nonMatchingTalk->tags()->attach($laravel);

    $this->actingAs($user)
        ->get(route('talks.index', ['tag' => $php->slug]))
        ->assertOk()
        ->assertSee($matchingTalk->title)
        ->assertDontSee($nonMatchingTalk->title);
});
