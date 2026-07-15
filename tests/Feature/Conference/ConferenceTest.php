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
