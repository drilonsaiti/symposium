<?php

namespace Profile;

use App\Enum\TalkSubmissionStatus;
use App\Models\Bio;
use App\Models\Conference;
use App\Models\Talk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');
});

uses(RefreshDatabase::class);

it('allows public to view speaker profile without auth', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertSee($speaker->name);
});

it('does not show rejected or pending talks on speaker profile', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');
    $conference = Conference::factory()->create();

    $rejectedTalk = Talk::factory()->create([
        'user_id' => $speaker->id,
        'title' => 'Rejected Talk Title',
    ]);

    $pendingTalk = Talk::factory()->create([
        'user_id' => $speaker->id,
        'title' => 'Pending Talk Title',
    ]);

    $rejectedTalk->conferences()->attach($conference->id, [
        'status' => TalkSubmissionStatus::REJECTED,
    ]);

    $pendingTalk->conferences()->attach($conference->id, [
        'status' => TalkSubmissionStatus::PENDING,
    ]);

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertDontSee('Rejected Talk Title')
        ->assertDontSee('Pending Talk Title');
});

it('shows never-submitted talks on speaker profile', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');

    $availableTalk = Talk::factory()->create([
        'user_id' => $speaker->id,
        'title' => 'Never Submitted Talk',
    ]);

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertSee('Never Submitted Talk');
});

it('shows accepted talks with their correct conference', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');
    $conference = Conference::factory()->create(['title' => 'LaraconEurope']);

    $acceptedTalk = Talk::factory()->create([
        'user_id' => $speaker->id,
        'title' => 'Accepted Talk Title',
    ]);

    $acceptedTalk->conferences()->attach($conference->id, [
        'status' => TalkSubmissionStatus::ACCEPTED,
    ]);

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertSee('Accepted Talk Title')
        ->assertSee('LaraconEurope');
});

it('shows the submission bio attached to an accepted talk', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');
    $conference = Conference::factory()->create();
    $bio = Bio::create([
        'user_id' => $speaker->id,
        'bio' => 'A short bio about Jane.',
        'nickname' => 'Short bio'
    ]);

    $acceptedTalk = Talk::factory()->create([
        'user_id' => $speaker->id,
        'title' => 'Talk With Bio',
    ]);

    $acceptedTalk->conferences()->attach($conference->id, [
        'status' => TalkSubmissionStatus::ACCEPTED,
        'bio_id' => $bio->id,
    ]);

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertSee('A short bio about Jane.');
});

it('resolves speaker profile by username', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');

    $this->get('/speakers/janedoe')
        ->assertOk()
        ->assertSee($speaker->name);
});

it('resolves speaker profile by id', function () {
    $this->withoutExceptionHandling();

    $speaker = makeUser('janedoe');

    $this->get('/speakers/' . $speaker->id)
        ->assertOk()
        ->assertSee($speaker->name);
});

it('returns 404 for a nonexistent speaker', function () {
    $this->get('/speakers/does-not-exist')
        ->assertNotFound();
});

it('shows correct speaker stats', function () {
    $speaker = makeUser('janedoe');
    $conference1 = Conference::factory()->create();
    $conference2 = Conference::factory()->create();

    $talk1 = Talk::factory()->create(['user_id' => $speaker->id]);
    $talk2 = Talk::factory()->create(['user_id' => $speaker->id]);

    $talk1->conferences()->attach($conference1->id, [
        'status' => TalkSubmissionStatus::ACCEPTED,
    ]);
    $talk2->conferences()->attach($conference2->id, [
        'status' => TalkSubmissionStatus::ACCEPTED,
    ]);

    $this->get(route('speakers.show', $speaker))
        ->assertOk()
        ->assertSee('2')
        ->assertSee('2');
});
