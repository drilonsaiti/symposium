<?php

namespace App\Http\Controllers;

use App\Enum\TalkSubmissionStatus;
use App\Models\Bio;
use App\Models\Conference;
use Illuminate\Http\Request;

class MyConferenceController extends Controller
{
    //
    public function index()
    {

        $conferences = auth()->user()->conferences()->latest('starts_at')->paginate(12);
        return view('conferences.index', compact('conferences'));
    }

    public function show(Conference $conference)
    {
        $conference->load('user');

        $user = auth()->user();
        $isOwner = $user?->is($conference->user) ?? false;
        $canViewSubmissions = $user?->can('viewSubmissions', $conference) ?? false;
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
        $submissionBios = Bio::whereIn('id',$biosIds)
            ->get()->keyBy('id');


        $today = now()->startOfDay();
        $cfpStartsAt = $conference->cfp_starts_at->copy()->startOfDay();
        $cfpEndsAt = $conference->cfp_ends_at->copy()->startOfDay();

        $cfpIsOpen = $today->gte($cfpStartsAt)
            && $today->lte($cfpEndsAt);

        return view('conferences.show', compact(
            'conference',
            'acceptedTalks',
            'submissions',
            'talkSubmissionStatuses',
            'mySubmissions',
            'availableTalks',
            'bios',
            'submissionBios',
            'isOwner',
            'cfpIsOpen',
        ));
    }

}
