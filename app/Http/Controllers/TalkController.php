<?php

namespace App\Http\Controllers;

use App\Actions\GetTalks;
use App\Enum\TalkType;
use App\Http\Requests\StoreTalkRequest;
use App\Http\Requests\UpdateTalkRequest;
use App\Models\ConferenceTalk;
use App\Models\Tag;
use App\Models\Talk;
use App\Models\TalkRevision;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalkController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetTalks $action)
    {
        //
        $talks = $action->handle($request);
        $tags = Tag::orderBy('name')->get();
        return view('talks.index', compact('talks', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('talks.create', [
            'talkTypes' => TalkType::cases(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTalkRequest $request)
    {
        //
        $validated = $request->validated();
        $abstract = $validated['abstract'] ?? null;
        $tags = $validated['tags'] ?? [];
        unset($validated['abstract'], $validated['tags']);

        DB::transaction(function () use ($validated, $abstract, $tags) {
            $talk = Talk::create([
                ...$validated,
                'user_id' => auth()->id(),
            ]);

            if ($abstract) {
                TalkRevision::create([
                    'talk_id' => $talk->id,
                    'abstract' => $abstract,
                ]);
            }

            $talk->tags()->sync($tags);
        });

        return redirect()->route('talks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Talk $talk)
    {
        $this->authorize('view', $talk);

        $user = auth()->user();

        $isAuthor = $talk->user_id === $user->id;

        $conferenceAccess = function ($query) use ($user) {
            $query->where(function ($query) use ($user) {
                $query
                    ->where('conferences.user_id', $user->id)
                    ->orWhereHas('reviewers', fn($query) => $query->where('users.id', $user->id)
                    );
            });
        };

        $talk->load([
            'conferences' => function ($query) use ($isAuthor, $conferenceAccess) {
                if (!$isAuthor) {
                    $conferenceAccess($query);
                }

                $query->orderBy('starts_at');
            },
            'currentRevision',
            'tags',
        ]);

        $talk->loadCount([
            'conferences' => function ($query) use ($isAuthor, $conferenceAccess) {
                if (!$isAuthor) {
                    $conferenceAccess($query);
                }
            },
        ]);

        $conferenceSubmissions = ConferenceTalk::query()
            ->where('talk_id', $talk->id)
            ->when(!$isAuthor, fn($query) => $query->whereHas('conference', $conferenceAccess)
            )
            ->with([
                'conference',
                'answers.question',
            ])
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('talks.show', compact(
            'talk',
            'conferenceSubmissions',
            'isAuthor'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Talk $talk)
    {
        $this->authorize('update', $talk);

        $talk->load('tags');

        return view('talks.edit', [
            'talk' => $talk,
            'talkTypes' => TalkType::cases(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTalkRequest $request, Talk $talk)
    {
        //
        $this->authorize('update', $talk);

        $validated = $request->validated();
        $abstract = $validated['abstract'] ?? null;
        $tags = $validated['tags'] ?? [];
        unset($validated['abstract'], $validated['tags']);


        DB::transaction(function () use ($talk, $validated, $abstract, $tags) {
            $talk->update($validated);

            if ($abstract) {
                TalkRevision::create([
                    'talk_id' => $talk->id,
                    'abstract' => $abstract,
                ]);
            }

            $talk->tags()->sync($tags);
        });

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
