<?php

namespace App\Actions;

use App\DTOs\ConferenceDTO;
use App\Enum\TalkSubmissionStatus;
use App\Models\Bio;
use App\Models\Conference;
use App\Models\User;

final class GetConferenceDTO
{

    public function handle(Conference $conference,?User $user,bool $isOwner,bool $canViewSubmissions): ConferenceDTO
    {
        $conference->load('user');

        $allTalks = $conference->talks()
            ->with('author')
            ->get();

        $acceptedTalks = $allTalks->filter(fn($t) => $t->pivot->status === TalkSubmissionStatus::ACCEPTED);

        $submissions = $canViewSubmissions ? $allTalks : collect();

        $talkSubmissionStatuses = $canViewSubmissions
            ? TalkSubmissionStatus::cases()
            : [];

        $mySubmissions = collect();
        $availableTalks = collect();
        $bios = collect();
        $biosIds = $allTalks
            ->pluck('pivot.bio_id')
            ->filter()
            ->unique();
        $submissionBios = Bio::whereIn('id', $biosIds)
            ->get()->keyBy('id');


        if ($user) {
            $bios = $user->bios()->latest()->get();

            $mySubmissions = $allTalks->filter(fn($t) => $t->user_id === $user?->id);

            $availableTalks = $user->talks()
                ->whereNotIn('talks.id', $mySubmissions->pluck('id'))
                ->latest()
                ->get();

        }
        $cfpIsOpen = $conference->cfpIsOpen();

        $cfpQuestions = collect();

        if ($isOwner || ($user && $cfpIsOpen)) {
            $cfpQuestions = $conference->cfpQuestions()
                ->active()
                ->orderBy('position')
                ->get();
        }

        return new ConferenceDTO(
            conference: $conference,
            isOwner: $isOwner,
            canViewSubmissions: $canViewSubmissions,
            cfpIsOpen: $cfpIsOpen,
            acceptedTalks: $acceptedTalks,
            submissions: $submissions,
            talkSubmissionStatuses: $talkSubmissionStatuses,
            mySubmissions: $mySubmissions,
            availableTalks: $availableTalks,
            bios: $bios,
            submissionBios: $submissionBios,
            cfpQuestions: $cfpQuestions
        );
    }
}
