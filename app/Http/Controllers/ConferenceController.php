<?php

namespace App\Http\Controllers;

use App\Actions\GetConferences;
use App\Actions\GetConferenceDTO;
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
    public function index(Request $request, GetConferences $getConferences)
    {
        $conferences = $getConferences->handle($request);

        if ($request->ajax()) {
            return view(
                'conferences.public.partials.list',
                compact('conferences')
            );
        }

        return view(
            'conferences.public.index',
            compact('conferences')
        );
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
    public function show(Conference $conference, GetConferenceDTO $action)
    {
        $user = auth()->user();
        $isOwner = $user?->is($conference->user) ?? false;
        $canViewSubmissions = $user?->can('viewSubmissions', $conference) ?? false;
        $data = $action->handle($conference, $user,$isOwner,$canViewSubmissions);

        return view('conferences.public.show', $data->toArray());
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
