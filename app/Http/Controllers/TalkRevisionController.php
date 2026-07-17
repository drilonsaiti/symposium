<?php

namespace App\Http\Controllers;

use App\Models\Talk;
use App\Models\TalkRevision;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TalkRevisionController extends Controller
{
    use AuthorizesRequests;

    public function index(Talk $talk)
    {
        $this->authorize('update', $talk);

        $revisions = $talk->revisions()->latest()->paginate(10);
        $talk->load('currentRevision');

        return view('talks.revisions.index', compact('talk', 'revisions'));
    }

    public function restore(Talk $talk, TalkRevision $revision)
    {
        $this->authorize('update', $talk);

        abort_unless($revision->talk_id === $talk->id, 404);

        $talk->revisions()->create([
            'abstract' => $revision->abstract,
        ]);

        return redirect()
            ->route('talks.revisions.index', $talk)
            ->with('status', 'Revision restored.');
    }
}
