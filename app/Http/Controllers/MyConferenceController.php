<?php

namespace App\Http\Controllers;

use App\Actions\GetConferenceDTO;
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

        $conferences = ConferenceFilter::apply($request, $query, 'authenticated')
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('conferences.partials.list', compact('conferences'));
        }

        return view('conferences.index', compact('conferences'));
    }

    public function show(Conference $conference, GetConferenceDTO $action)
    {
        $user = auth()->user();
        $isOwner = $user?->is($conference->user) ?? false;
        $canViewSubmissions = $user?->can('viewSubmissions', $conference) ?? false;
        $data = $action->handle($conference, $user,$isOwner,$canViewSubmissions);

        return view('conferences.show', $data->toArray());
    }

}
