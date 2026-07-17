<?php

namespace Talk;

use App\Enum\TalkType;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
