<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ConferenceFilter
{

    public static function apply(Request $request, Builder $query)
    {
        $conference_date = $request->input('conference_date');
        $cfp_status = $request->input('cfp_status');
        $term = $request->input('term');
        $saved = $request->input('saved');
        $view = $request->input('view');

        match ($conference_date) {
            'upcoming' => $query->upcoming(),
            'past' => $query->past(),
            default => null,
        };

        match ($cfp_status) {
            'open' => $query->cfpOpen(),
            'upcoming' => $query->cfpUpcoming(),
            'closed' => $query->cfpClosed(),
            default => null,
        };

        match ($saved) {
            'favorited' => $query->whereNot('user_id',auth()->id())->whereHas('favoritedByUsers',
                fn($q) => $q->where('user_id', auth()->id())),
            'dismissed' => $query->whereNot('user_id',auth()->id())->whereHas('dismissedByUsers',
                fn($q) => $q->where('user_id', auth()->id())),
            default => null,
        };

        match ($view) {
            'mine' => $query->where('user_id', auth()->id()),

            'favorited' => $query->whereNot('user_id',auth()->id())->whereHas('favoritedByUsers',
                fn($q) => $q->where('user_id', auth()->id())

            ),

            'dismissed' => $query->whereNot('user_id',auth()->id())->whereHas('dismissedByUsers',
                fn($q) => $q->where('user_id', auth()->id())
            ),

            default => null,
        };

        if (filled($term)) {
            $query->search($term);
        }

        \Log::info($view);
        if ($saved !== 'dismissed' && $view !== 'dismissed') {
            return $query->latest('starts_at')->notDismissedBy(auth()->user());
        }


        return $query->latest('starts_at');
    }
}
