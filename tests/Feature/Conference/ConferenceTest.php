<?php

use App\Models\Conference;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    $this->actingAs($user)->post(route('conferences.favorite', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::FAVORITED
    ]);


    $this->actingAs($user)->post(route('conferences.dismissed', $conference));
    $this->assertDatabaseHas('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
        'status' => \App\Enum\ConferenceUserStatus::DISMISSED
    ]);

    $this->assertDatabaseMissing('conference_user', [
        'user_id' => $user->id,
        'conference_id' => $conference->id,
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
