<?php

namespace App\Http\Controllers;

use App\Enum\TalkType;
use App\Http\Requests\StoreTalkRequest;
use App\Http\Requests\UpdateTalkRequest;
use App\Models\Talk;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TalkController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $talks = auth()
            ->user()
            ->talks()
            ->with([
                'conferences' => fn($query) => $query->orderBy('starts_at'),
            ])
            ->withCount('conferences')
            ->latest('updated_at')
            ->paginate(10);
        return view('talks.index', compact('talks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $talkTypes = TalkType::cases();
        return view('talks.create', compact('talkTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTalkRequest $request)
    {
        //
        $validated = $request->validated();
        Talk::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('talks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Talk $talk)
    {
        //
        $this->authorize('view', $talk);
        $talk->load(['conferences' => fn($query) => $query->orderBy('starts_at')])
            ->loadCount('conferences')
            ->latest('updated_at');

        return view('talks.show', compact('talk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Talk $talk)
    {
        //
        $this->authorize('update', $talk);
        $talkTypes = TalkType::cases();
        return view('talks.edit', compact('talk', 'talkTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTalkRequest $request, Talk $talk)
    {
        //
        $this->authorize('update', $talk);

        $validated = $request->validated();
        $talk->update($validated);
        return redirect()->route('talks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Talk $talk)
    {
        //
        $this->authorize('delete', $talk);
        $talk->delete();
        return redirect()->route('talks.index');
    }
}
