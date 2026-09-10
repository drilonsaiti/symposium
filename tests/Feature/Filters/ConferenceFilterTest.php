<?php

use App\Enum\ConferenceUserStatus;
use App\Filters\ConferenceFilter;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('filters upcoming conferences', function () {
    $upcoming = Conference::factory()->create([
        'starts_at' => now()->addWeek(),
    ]);

    $past = Conference::factory()->create([
        'starts_at' => now()->subWeek(),
    ]);

    $request = Request::create('/', 'GET', [
        'conference_date' => 'upcoming',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($upcoming->id)
        ->not->toContain($past->id);
});

it('filters past conferences', function () {
    $past = Conference::factory()->create([
        'starts_at' => now()->subWeek(),
        'ends_at' => now()->subDay(),
    ]);

    $upcoming = Conference::factory()->create([
        'starts_at' => now()->addWeek(),
    ]);

    $request = Request::create('/', 'GET', [
        'conference_date' => 'past',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($past->id)
        ->not->toContain($upcoming->id);
});

it('filters conferences with open cfp', function () {
    $open = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeek(),
        'cfp_ends_at' => now()->addWeek(),
    ]);

    $upcoming = Conference::factory()->create([
        'cfp_starts_at' => now()->addWeek(),
        'cfp_ends_at' => now()->addWeeks(2),
    ]);

    $closed = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeeks(2),
        'cfp_ends_at' => now()->subWeek(),
    ]);

    $request = Request::create('/', 'GET', [
        'cfp_status' => 'open',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($open->id)
        ->not->toContain($upcoming->id)
        ->not->toContain($closed->id);
});

it('filters conferences with upcoming cfp', function () {
    $upcoming = Conference::factory()->create([
        'cfp_starts_at' => now()->addWeek(),
        'cfp_ends_at' => now()->addWeeks(2),
    ]);

    $open = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeek(),
        'cfp_ends_at' => now()->addWeek(),
    ]);

    $closed = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeeks(2),
        'cfp_ends_at' => now()->subWeek(),
    ]);

    $request = Request::create('/', 'GET', [
        'cfp_status' => 'upcoming',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($upcoming->id)
        ->not->toContain($open->id)
        ->not->toContain($closed->id);
});

it('filters conferences with closed cfp', function () {
    $closed = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeeks(2),
        'cfp_ends_at' => now()->subWeek(),
    ]);

    $open = Conference::factory()->create([
        'cfp_starts_at' => now()->subWeek(),
        'cfp_ends_at' => now()->addWeek(),
    ]);

    $upcoming = Conference::factory()->create([
        'cfp_starts_at' => now()->addWeek(),
        'cfp_ends_at' => now()->addWeeks(2),
    ]);

    $request = Request::create('/', 'GET', [
        'cfp_status' => 'closed',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($closed->id)
        ->not->toContain($open->id)
        ->not->toContain($upcoming->id);
});

it('filters conferences by search term', function () {
    $matching = Conference::factory()->create([
        'title' => 'Laravel Conference',
    ]);

    $notMatching = Conference::factory()->create([
        'title' => 'Symfony Conference',
    ]);

    $request = Request::create('/', 'GET', [
        'term' => 'Laravel',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($matching->id)
        ->not->toContain($notMatching->id);
});

it('filters saved favorited conferences', function () {
    $user = makeUser();

    $favorited = Conference::factory()->create();
    $notFavorited = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $favorited->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::FAVORITED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'saved' => 'favorited',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($favorited->id)
        ->not->toContain($notFavorited->id);
});

it('filters saved dismissed conferences', function () {
    $user = makeUser();

    $dismissed = Conference::factory()->create();
    $notDismissed = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $dismissed->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::DISMISSED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'saved' => 'dismissed',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($dismissed->id)
        ->not->toContain($notDismissed->id);
});

it('filters conferences owned by the authenticated user', function () {
    $user = makeUser();
    $otherUser = makeUser();

    $mine = Conference::factory()->create([
        'user_id' => $user->id,
    ]);

    $other = Conference::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'view' => 'mine',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($mine->id)
        ->not->toContain($other->id);
});

it('filters favorited conferences using the view filter', function () {
    $user = makeUser();

    $favorited = Conference::factory()->create();
    $notFavorited = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $favorited->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::FAVORITED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'view' => 'favorited',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($favorited->id)
        ->not->toContain($notFavorited->id);
});

it('filters dismissed conferences using the view filter', function () {
    $user = makeUser();

    $dismissed = Conference::factory()->create();
    $notDismissed = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $dismissed->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::DISMISSED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'view' => 'dismissed',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($dismissed->id)
        ->not->toContain($notDismissed->id);
});

it('filters conferences where the authenticated user has submitted a talk', function () {
    $user = makeUser();

    $submittedConference = Conference::factory()->create();
    $otherConference = Conference::factory()->create();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $submittedConference->talks()->attach($talk->id);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'view' => 'submitted',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($submittedConference->id)
        ->not->toContain($otherConference->id);
});

it('hides dismissed conferences from authenticated users by default', function () {
    $user = makeUser();

    $visible = Conference::factory()->create();
    $dismissed = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $dismissed->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::DISMISSED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET');

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($visible->id)
        ->not->toContain($dismissed->id);
});

it('shows only owned conferences by default for authenticated conference view', function () {
    $user = makeUser();
    $otherUser = makeUser();

    $mine = Conference::factory()->create([
        'user_id' => $user->id,
    ]);

    $other = Conference::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET');

    $results = ConferenceFilter::apply(
        $request,
        Conference::query(),
        'authenticated'
    )->get();

    expect($results->pluck('id'))
        ->toContain($mine->id)
        ->not->toContain($other->id);
});

it('does not hide dismissed conferences when dismissed is explicitly requested', function () {
    $user = makeUser();

    $dismissed = Conference::factory()->create();

    DB::table('conference_user')->insert([
        'conference_id' => $dismissed->id,
        'user_id' => $user->id,
        'status' => ConferenceUserStatus::DISMISSED,
    ]);

    $this->actingAs($user);

    $request = Request::create('/', 'GET', [
        'view' => 'dismissed',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id'))
        ->toContain($dismissed->id);
});

it('returns conferences ordered by latest start date', function () {
    $older = Conference::factory()->create([
        'starts_at' => now()->addWeek(),
    ]);

    $newer = Conference::factory()->create([
        'starts_at' => now()->addWeeks(2),
    ]);

    $request = Request::create('/', 'GET');

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results->pluck('id')->values()->all())
        ->toBe([
            $newer->id,
            $older->id,
        ]);
});

it('returns no favorited conferences for a guest', function () {
    $conference = Conference::factory()->create();

    $request = Request::create('/', 'GET', [
        'view' => 'favorited',
    ]);

    $results = ConferenceFilter::apply(
        $request,
        Conference::query()
    )->get();

    expect($results)->toBeEmpty();
});
