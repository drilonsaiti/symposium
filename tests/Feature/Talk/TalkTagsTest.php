<?php

namespace Talk;

use App\Enum\TalkType;
use App\Models\Tag;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('restore');
    RateLimiter::clear('talk-submission');
    RateLimiter::clear('status-change');
});

uses(RefreshDatabase::class);

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


it('shows tags on the talk details page', function () {
    $user = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $tags = Tag::factory()->count(3)->create();

    $talk->tags()->attach($tags);

    $response = $this->actingAs($user)
        ->get(route('talks.show', $talk))
        ->assertOk();

    foreach ($tags as $tag) {
        $response->assertSee($tag->name);
    }
});
