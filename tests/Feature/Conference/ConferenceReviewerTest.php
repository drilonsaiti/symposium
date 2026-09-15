<?php

namespace Conference;

use App\Enum\ConferenceReviewerStatus;
use App\Models\Conference;
use App\Notifications\ReviewerInvitedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('owner can invite an existing user by username', function () {
    Notification::fake();

    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->post(route('conferences.reviewers.store', $conference), [
            'reviewer' => $reviewer->username,
        ])
        ->assertSessionHas('status', 'Reviewer invited successfully.');

    $this->assertDatabaseHas('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
        'status' => ConferenceReviewerStatus::PENDING->value,
    ]);

    Notification::assertSentTo(
        $reviewer,
        ReviewerInvitedNotification::class
    );
});

it('owner can invite an existing user by email', function () {
    Notification::fake();

    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->post(route('conferences.reviewers.store', $conference), [
            'reviewer' => $reviewer->email,
        ])
        ->assertSessionHas('status', 'Reviewer invited successfully.');

    $this->assertDatabaseHas('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
        'status' => ConferenceReviewerStatus::PENDING->value,
    ]);

    Notification::assertSentTo(
        $reviewer,
        ReviewerInvitedNotification::class
    );
});

it('non-existent username or email is rejected', function () {
    $owner = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->post(route('conferences.reviewers.store', $conference), [
            'reviewer' => 'missing-user@example.com',
        ])
        ->assertSessionHasErrors('reviewer');

    $this->assertDatabaseCount('conference_reviewers', 0);
});

it('non-owner gets forbidden before reviewer validation runs', function () {
    $owner = makeUser();
    $nonOwner = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this->actingAs($nonOwner)
        ->post(route('conferences.reviewers.store', $conference), [
            'reviewer' => 'definitely-does-not-exist',
        ]);

    $response->assertForbidden();
    $response->assertSessionDoesntHaveErrors('reviewer');

    $this->assertDatabaseCount('conference_reviewers', 0);
});

it('duplicate reviewer invite returns a friendly message', function () {
    Notification::fake();

    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::PENDING,
    ]);

    $this->actingAs($owner)
        ->post(route('conferences.reviewers.store', $conference), [
            'reviewer' => $reviewer->username,
        ])
        ->assertSessionHas('status', 'Reviewer was already invited.');

    expect(
        $conference->reviewerInvitations()
            ->whereKey($reviewer->id)
            ->count()
    )->toBe(1);
});

it('reviewer can accept a pending invitation', function () {
    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::PENDING,
    ]);

    $this->actingAs($reviewer)
        ->patch(route('reviewing.accept', $conference))
        ->assertRedirect(route('conferences.show',$conference));

    $this->assertDatabaseHas('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
        'status' => ConferenceReviewerStatus::ACCEPTED->value,
    ]);

    expect(
        $conference->fresh()
            ->reviewers()
            ->whereKey($reviewer->id)
            ->exists()
    )->toBeTrue();

    expect($reviewer->can('viewSubmissions', $conference))->toBeTrue();
    expect($reviewer->can('manageSubmissions', $conference))->toBeTrue();
});

it('accepted reviewer cannot accept the same invitation again', function () {
    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::ACCEPTED,
    ]);

    $this->actingAs($reviewer)
        ->patch(route('reviewing.accept', $conference))
        ->assertStatus(409);
});

it('reviewer can decline a pending invitation', function () {
    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::PENDING,
    ]);

    $this->actingAs($reviewer)
        ->delete(route('reviewing.decline', $conference))
        ->assertRedirect(route('reviewing.index'));

    $this->assertDatabaseMissing('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
    ]);

    expect($reviewer->can('viewSubmissions', $conference))->toBeFalse();
    expect($reviewer->can('manageSubmissions', $conference))->toBeFalse();
});

it('user cannot decline another users invitation', function () {
    $owner = makeUser();
    $reviewer = makeUser();
    $otherUser = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::PENDING,
    ]);

    $this->actingAs($otherUser)
        ->delete(route('reviewing.decline', $conference))
        ->assertNotFound();

    $this->assertDatabaseHas('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
        'status' => ConferenceReviewerStatus::PENDING->value,
    ]);
});

it('owner can remove an accepted reviewer', function () {
    $owner = makeUser();
    $reviewer = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::ACCEPTED,
    ]);

    $this->actingAs($owner)
        ->delete(route('conferences.reviewers.destroy', [$conference, $reviewer]))
        ->assertSessionHas('status', 'Reviewer removed successfully.');

    $this->assertDatabaseMissing('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
    ]);

    expect($reviewer->can('viewSubmissions', $conference))->toBeFalse();
    expect($reviewer->can('manageSubmissions', $conference))->toBeFalse();
});

it('non-owner cannot remove a reviewer', function () {
    $owner = makeUser();
    $reviewer = makeUser();
    $nonOwner = makeUser();

    $conference = Conference::factory()->create([
        'user_id' => $owner->id,
    ]);

    $conference->reviewerInvitations()->attach($reviewer->id, [
        'status' => ConferenceReviewerStatus::ACCEPTED,
    ]);

    $this->actingAs($nonOwner)
        ->delete(route('conferences.reviewers.destroy', [$conference, $reviewer]))
        ->assertForbidden();

    $this->assertDatabaseHas('conference_reviewers', [
        'conference_id' => $conference->id,
        'user_id' => $reviewer->id,
        'status' => ConferenceReviewerStatus::ACCEPTED->value,
    ]);
});
