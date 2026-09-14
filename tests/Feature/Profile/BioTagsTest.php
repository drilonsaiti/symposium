<?php

namespace Bio;

use App\Enum\TalkSubmissionStatus;
use App\Models\Bio;
use App\Models\Conference;
use App\Models\Tag;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('speaker can attach tags to a bio on create', function () {
    $user = makeUser();
    $tags = Tag::factory()->count(3)->create();

    $this->actingAs($user)
        ->post(route('bios.store'), [
            'nickname' => 'John Doe',
            'bio' => 'Laravel developer and conference speaker.',
            'tags' => $tags->pluck('id')->all(),
        ])
        ->assertSessionHasNoErrors();

    $bio = Bio::where('user_id', $user->id)->firstOrFail();

    expect($bio->tags()->pluck('tags.id')->all())
        ->toEqualCanonicalizing($tags->pluck('id')->all());

    foreach ($tags as $tag) {
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $bio->id,
            'taggable_type' => Bio::class,
        ]);
    }
});

it('max 8 tags are enforced when creating a bio', function () {
    $user = makeUser();
    $tags = Tag::factory()->count(9)->create();

    $this->actingAs($user)
        ->post(route('bios.store'), [
            'nickname' => 'John Doe',
            'bio' => 'Laravel developer and conference speaker.',
            'tags' => $tags->pluck('id')->all(),
        ])
        ->assertSessionHasErrors('tags');

    $this->assertDatabaseMissing('bios', [
        'user_id' => $user->id,
    ]);
});

it('non-existent tag id is rejected when creating a bio', function () {
    $user = makeUser();

    $this->actingAs($user)
        ->post(route('bios.store'), [
            'nickname' => 'John Doe',
            'bio' => 'Laravel developer and conference speaker.',
            'tags' => [999999],
        ])
        ->assertSessionHasErrors('tags.0');

    $this->assertDatabaseMissing('bios', [
        'user_id' => $user->id,
    ]);
});

it('existing bio tags are checked on the edit form', function () {
    $user = makeUser();

    $bio = Bio::factory()->create([
        'user_id' => $user->id,
    ]);

    $tags = Tag::factory()->count(3)->create();

    $bio->tags()->attach($tags);

    $response = $this->actingAs($user)
        ->get(route('bios.edit', $bio))
        ->assertOk();

    foreach ($tags as $tag) {
        expect($response->getContent())
            ->toMatch(
                '/<input(?=[^>]*value="' . $tag->id . '")(?=[^>]*\bchecked\b)[^>]*>/'
            );
    }
});

it('editing a bio with the same tags keeps them attached', function () {
    $user = makeUser();

    $bio = Bio::factory()->create([
        'user_id' => $user->id,
    ]);

    $tags = Tag::factory()->count(3)->create();

    $bio->tags()->attach($tags);

    $this->actingAs($user)
        ->put(route('bios.update', $bio), [
            'nickname' => $bio->nickname,
            'bio' => $bio->bio,
            'tags' => $tags->pluck('id')->all(),
        ])
        ->assertSessionHasNoErrors();

    expect($bio->fresh()->tags()->pluck('tags.id')->all())
        ->toEqualCanonicalizing($tags->pluck('id')->all());

    foreach ($tags as $tag) {
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $bio->id,
            'taggable_type' => Bio::class,
        ]);
    }
});

it('bio tags appear on speaker profile for an accepted talk', function () {
    $user = makeUser();

    $talk = Talk::factory()->create([
        'user_id' => $user->id,
    ]);

    $conference = Conference::factory()->create();

    $bio = Bio::factory()->create([
        'user_id' => $user->id,
    ]);

    $tag = Tag::factory()->create([
        'name' => 'Laravel',
    ]);

    $bio->tags()->attach($tag);

    $conference->talks()->attach($talk->id, [
        'status' => TalkSubmissionStatus::ACCEPTED->value,
        'bio_id' => $bio->id,
    ]);

    $this->get(route('speakers.show', $user))
        ->assertOk()
        ->assertSee($tag->name);
});
