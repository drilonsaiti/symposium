<?php

namespace App\Http\Controllers;

use App\Enum\TalkSubmissionStatus;
use App\Filters\ConferenceFilter;
use App\Models\Bio;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MyConferenceController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Conference::query();

        if ($user = auth()->user()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => fn(Builder $q) => $q->where('user_id', $user->id),
                'dismissedByUsers as is_dismissed' => fn(Builder $q) => $q->where('user_id', $user->id),
            ]);
        }

        $conferences = ConferenceFilter::apply($request, $query)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('conferences.partials.list', compact('conferences'));
        }

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
        $submissionBios = Bio::whereIn('id', $biosIds)
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
