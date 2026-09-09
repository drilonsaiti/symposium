<?php

namespace App\DTOs;

use App\Models\Conference;
use App\Enum\TalkSubmissionStatus;
use Illuminate\Support\Collection;

final class ConferenceDTO
{
    public function __construct(
        public readonly Conference $conference,
        public readonly bool $isOwner,
        public readonly bool $canViewSubmissions,
        public readonly bool $cfpIsOpen,
        public readonly Collection $acceptedTalks,
        public readonly Collection $submissions,
        public readonly array $talkSubmissionStatuses,
        public readonly Collection $mySubmissions,
        public readonly Collection $availableTalks,
        public readonly Collection $bios,
        public readonly Collection $submissionBios,
    ){}

    public function toArray(): array
    {
        return [
            'conference' => $this->conference,
            'isOwner' => $this->isOwner,
            'canViewSubmissions' => $this->canViewSubmissions,
            'cfpIsOpen' => $this->cfpIsOpen,
            'acceptedTalks' => $this->acceptedTalks,
            'submissions' => $this->submissions,
            'talkSubmissionStatuses' => $this->talkSubmissionStatuses,
            'mySubmissions' => $this->mySubmissions,
            'availableTalks' => $this->availableTalks,
            'bios' => $this->bios,
            'submissionBios' => $this->submissionBios,
        ];
    }
}
