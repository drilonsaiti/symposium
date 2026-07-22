<?php

namespace App\Http\Controllers;

use App\Enum\TalkSubmissionStatus;
use App\Models\Bio;
use App\Models\Talk;
use App\Models\User;
use Illuminate\Http\Request;

class SpeakerProfileController extends Controller
{
    //

    public function show(User $user)
    {
        $acceptedTalks = Talk::where('user_id', $user->id)
            ->whereHas('conferences', fn ($q) =>
            $q->where('conference_talk.status', TalkSubmissionStatus::ACCEPTED->value)
            )
            ->with(['conferences' => fn ($q) =>
            $q->wherePivot('status', TalkSubmissionStatus::ACCEPTED)
            ])
            ->get();

        $bioIds = $acceptedTalks
            ->flatMap(fn ($talk) => $talk->conferences)
            ->pluck('pivot.bio_id')
            ->filter()
            ->unique();

        $bios = Bio::whereIn('id', $bioIds)->get()->keyBy('id');

        $acceptedTalks->each(function ($talk) use ($bios) {
            $talk->conferences->each(function ($conference) use ($bios) {
                $conference->pivot->setRelation(
                    'bio',
                    $bios->get($conference->pivot->bio_id)
                );
            });
        });

        $availableTalks = Talk::where('user_id', $user->id)
            ->doesntHave('conferences')
            ->with('currentRevision')
            ->get();

        $stats = [
            'accepted_talks' => $acceptedTalks->count(),
            'conferences_spoken_at' => $acceptedTalks
                ->flatMap(fn ($talk) => $talk->conferences)
                ->unique('id')
                ->count(),
        ];

        return view('speakers.show', [
            'speaker' => $user,
            'acceptedTalks' => $acceptedTalks,
            'availableTalks' => $availableTalks,
            'stats' => $stats,
        ]);
    }
}
