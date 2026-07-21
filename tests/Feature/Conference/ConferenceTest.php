<?php

use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');
});

uses(RefreshDatabase::class);

it('search filter returns matching conferences', function () {
    Conference::factory()->create(['title' => 'LaravelConf']);
    Conference::factory()->create(['title' => 'SymfonyConf']);

    $this->get(route('public.conferences.index', ['term' => 'Laravel'],
        ['X-Requested-With' => 'XMLHttpRequest',]))
        ->assertSee('LaravelConf')
        ->assertDontSee('SymfonyConf');
});

it('filters by starts date(upcoming and past) returning wrong conferences',function(){
    Conference::factory()->create([
        'title' => 'UpcomingConf',
        'starts_at' => now()->addWeek(),
    ]);

    Conference::factory()->create([
        'title' => 'PastConf',
        'starts_at' => now()->subWeek(),
    ]);

    $this->get(route('public.conferences.index', [
        'conference_date' => 'upcoming',
    ],
        ['X-Requested-With' => 'XMLHttpRequest',]))
        ->assertSee('UpcomingConf')
        ->assertDontSee('PastConf');
});

it('filters by cfp starts date(upcoming,open,closed) returning wrong conferences',function(){
    Conference::factory()->create([
        'title' => 'UpcomingConf',
        'cfp_starts_at' => now()->addWeek(),
        'cfp_ends_at' => now()->addWeeks(3),
    ]);

    Conference::factory()->create([
        'title' => 'OpenConf',
        'cfp_starts_at' => now()->subWeek(),
        'cfp_ends_at' => now()->addWeek(),
    ]);

    Conference::factory()->create([
        'title' => 'ClosedConf',
        'cfp_starts_at' => now()->subMonths(2),
        'cfp_ends_at' => now()->subWeek(),
    ]);

    // Upcoming
    $this->get(route('public.conferences.index', [
        'cfp_status' => 'upcoming',
    ],
        ['X-Requested-With' => 'XMLHttpRequest',]))
        ->assertSee('UpcomingConf')
        ->assertDontSee(['OpenConf', 'ClosedConf']);

    $this->get(route('public.conferences.index', [
        'cfp_status' => 'open',
    ],
        ['X-Requested-With' => 'XMLHttpRequest',]))
        ->assertSee('OpenConf')
        ->assertDontSee(['UpcomingConf', 'ClosedConf']);

    $this->get(route('public.conferences.index', [
        'cfp_status' => 'closed',
    ],
        ['X-Requested-With' => 'XMLHttpRequest',]))
        ->assertSee('ClosedConf')
        ->assertDontSee(['UpcomingConf', 'OpenConf']);
});

it('add conference to favorites', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();
    $this->actingAs($user)->post(route('conferences.favorite', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::FAVORITED
    ]);
});

it('remove conference from favorites', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();
    $this->actingAs($user)->post(route('conferences.favorite', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::FAVORITED
    ]);

    $this->actingAs($user)->delete(route('conferences.favorite', $conference));
    $this->assertDatabaseMissing('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
    ]);
});

it('add conference to dismissed', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();
    $this->actingAs($user)->post(route('conferences.dismissed', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::DISMISSED
    ]);
});

it('remove conference from dismissed', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();
    $this->actingAs($user)->post(route('conferences.dismissed', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::DISMISSED
    ]);

    $this->actingAs($user)->delete(route('conferences.dismissed', $conference));
    $this->assertDatabaseMissing('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
    ]);
});

it('change conference from favorite to dismissed', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();

    $this->actingAs($user)
        ->post(route('conferences.favorite', $conference));

    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::FAVORITED
    ]);

    $this->actingAs($user)
        ->post(route('conferences.dismissed', $conference));

    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::DISMISSED
    ]);

    $this->assertDatabaseMissing('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::FAVORITED
    ]);
});

it('guest cannot favorite conference', function () {
    $conference = Conference::factory()->create();
    $this->post(route('conferences.favorite', $conference))->assertRedirect(route('login'));
});

it('guest cannot dismiss conference', function () {
    $conference = Conference::factory()->create();
    $this->post(route('conferences.dismissed', $conference))
        ->assertRedirect(route('login'));
});

it('dismissed conference cannot be seen by user', function () {
    $conference = Conference::factory()->create();
    $user = makeUser();
    $this->actingAs($user)->post(route('conferences.dismissed', $conference));
    $this->get(route('conferences.index', $conference))
    ->assertDontSee($conference->title);
});

it('rate limits status changes', function () {
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

    $request = fn () => $this->actingAs($conferenceUser)
        ->patch(
            route('conferences.talks.status', [
                'conference' => $conference,
                'talk' => $talk,
            ]),
            [
                'status' => 'accepted',
            ]
        );

    foreach (range(1, 20) as $i) {
        DB::table('conference_talk')->update(['status' => 'pending']);
        $request()->assertRedirect();
    }

    $request()->assertStatus(429);
});

it('uses the cached conference list', function () {
    Cache::flush();

    $conference = Conference::factory()->create([
        'title' => 'Original Conference',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('Original Conference');

    $conference->updateQuietly([
        'title' => 'Updated Conference',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('Original Conference')
        ->assertDontSee('Updated Conference');
});


it('invalidates the conference cache when a conference is created', function () {
    Cache::flush();

    $this->get(route('public.conferences.index'))
        ->assertDontSee('New Conference');

    Conference::factory()->create([
        'title' => 'New Conference',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('New Conference');
});

it('invalidates the conference cache when a conference is updated', function () {
    Cache::flush();

    $conference = Conference::factory()->create([
        'title' => 'Old Conference Title',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('Old Conference Title');

    $conference->update([
        'title' => 'New Conference Title',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('New Conference Title')
        ->assertDontSee('Old Conference Title');
});

it('invalidates the conference cache when a conference is deleted', function () {
    Cache::flush();

    $conference = Conference::factory()->create([
        'title' => 'Conference To Delete',
    ]);

    $this->get(route('public.conferences.index'))
        ->assertSee('Conference To Delete');

    $conference->delete();

    $this->get(route('public.conferences.index'))
        ->assertDontSee('Conference To Delete');
});

it('uses different cache entries for different filters', function () {

    $this->withoutExceptionHandling();

    Cache::flush();

    Conference::factory()->create([
        'title' => 'Upcoming Conference',
        'starts_at' => now()->addWeek(),
    ]);

    Conference::factory()->create([
        'title' => 'Past Conference',
        'starts_at' => now()->subWeeks(2),
        'ends_at' => now()->subWeeks(1),
    ]);

    $this->get(route('public.conferences.index', [
        'conference_date' => 'upcoming',
    ]))
        ->assertSee('Upcoming Conference')
        ->assertDontSee('Past Conference');

    $this->get(route('public.conferences.index', [
        'conference_date' => 'past',
    ]))
        ->assertSee('Past Conference')
        ->assertDontSee('Upcoming Conference');
});


it('uses different cache entries for different pages', function () {
    Cache::flush();

    Conference::factory()
        ->count(24)
        ->create();

    $pageOne = $this->get(route('public.conferences.index', [
        'page' => 1,
    ]));

    $pageTwo = $this->get(route('public.conferences.index', [
        'page' => 2,
    ]));

    expect($pageOne->getOriginalContent())
        ->not->toBe($pageTwo->getOriginalContent());
});

it('does not cache personalized conference results for authenticated users', function () {
    Cache::spy();

    $user = makeUser();

    $this->actingAs($user)
        ->get(route('public.conferences.index'));

    Cache::shouldNotHaveReceived('remember');
    Cache::shouldNotHaveReceived('flexible');
});

it('does not use the conference cache for authenticated users', function () {
    Cache::spy();

    $user = makeUser();

    $this->actingAs($user)
        ->get(route('public.conferences.index'));

    Cache::shouldNotHaveReceived('tags');
});
