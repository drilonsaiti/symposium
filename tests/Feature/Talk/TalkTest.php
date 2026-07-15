<?php

namespace Talk;

use App\Enum\TalkType;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticated user sees only their own talks',function(){

    $user = makeUser();

    $talk = Talk::factory()->create(['user_id' => $user->id]);
    $otherTalk = Talk::factory()->create();

    $this->actingAs($user)->get(route('talks.index'))->assertSee($talk->title);
    $this->actingAs($user)->get(route('talks.index'))->assertDontSee($otherTalk->title);
});

it('guest redirected to login page',function(){
    $this->get(route('talks.index'))->assertRedirect(route('login'));
});

it('authenticated user can create talk',function(){
    $user = makeUser();
    $this->actingAs($user)->get(route('talks.create'))->assertOk();
    $this->actingAs($user)->post(route('talks.store'),[
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));
    $this->assertDatabaseHas('talks',[
        'title' => 'Test Title',
    ]);
});

it('validation fails with missing title',function(){
    $user = makeUser();
    $this->actingAs($user)->post(route('talks.store'),[
        'title' => '',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertSessionHasErrors();

    $this->assertDatabaseMissing('talks',[
        'title' => '',
    ]);
});

it('user_id is set from auth,not from request input',function(){
    $user = makeUser();
    $this->actingAs($user)->post(route('talks.store'),[
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));
    $this->assertDatabaseHas('talks',[
        'user_id' => $user->id,
    ]);
});

it('owner can edit their talk',function(){
    $user = makeUser();
    $talk = Talk::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user)->get(route('talks.edit',$talk))->assertOk();

    $this->actingAs($user)->put(route('talks.update',$talk),[
        'title' => 'Test Title',
        'length' => 30,
        'type' => TalkType::STANDARD->value,
    ])->assertRedirect(route('talks.index'));

    $this->assertDatabaseHas('talks',[
        'title' => 'Test Title',
    ]);
});

it('another authenticated user cannot edit someone else talk',function(){
    $user = makeUser();
    $otherUser = makeUser();
    $talk = Talk::factory()->create(['user_id' => $otherUser->id]);
    $this->actingAs($user)->get(route('talks.edit',$talk))->assertForbidden();
    $this->actingAs($user)->put(route('talks.update',$talk),[
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

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status',[$conference,$talk]),[
        'status' => 'accepted',
        ]
    );

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'status' => 'accepted',
    ]);
});

it('cannot change status from rejected',function (){
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

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status',[$conference,$talk]),[
            'status' => 'rejected',
        ]
    );

    $this->assertDatabaseHas('conference_talk', [
        'conference_id' => $conference->id,
        'talk_id' => $talk->id,
        'status' => 'rejected',
    ]);

    $this->actingAs($conferenceUser)->patch(route('conferences.talks.status',[$conference,$talk]),[
        'status' => 'accepted',
        ]
    )->assertStatus(302);

});
