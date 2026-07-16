<?php

namespace App\Http\Controllers;

use App\Enum\TalkSubmissionStatus;
use App\Filters\ConferenceFilter;
use App\Http\Requests\StoreConferenceRequest;
use App\Http\Requests\UpdateConferenceRequest;
use App\Models\Bio;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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
            return view('conferences.public.partials.list', compact('conferences'));
        }

        return view('conferences.public.index', compact('conferences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('conferences.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConferenceRequest $request)
    {
        //
        $validated = $request->validated();
        $conference = Conference::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('conferences.show', $conference);
    }

    /**
     * Display the specified resource.
     */
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


        if ($user && !$isOwner) {
            $bios = $user->bios()->latest()->get();

            $mySubmissions = $allTalks->filter(fn($t) => $t->user_id === $user?->id);

            $availableTalks = $user->talks()
                ->whereNotIn('talks.id', $mySubmissions->pluck('id'))
                ->latest()
                ->get();

        }

        $today = now()->startOfDay();
        $cfpStartsAt = $conference->cfp_starts_at->copy()->startOfDay();
        $cfpEndsAt = $conference->cfp_ends_at->copy()->startOfDay();

        $cfpIsOpen = $today->gte($cfpStartsAt)
            && $today->lte($cfpEndsAt);

        return view('conferences.public.show', compact(
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conference $conference)
    {
        //
        $this->authorize('update', $conference);
        return view('conferences.edit', compact('conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConferenceRequest $request, Conference $conference)
    {
        //
        $this->authorize('update', $conference);
        $validated = $request->validated();
        $conference->update($validated);

        return redirect()->route('conferences.show', $conference);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conference $conference)
    {
        //
        $this->authorize('delete', $conference);
        $conference->delete();
        return redirect()->route('conferences.index');
    }
}
