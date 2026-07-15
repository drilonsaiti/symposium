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

        if (filled($term)) {
            $query->search($term);
        }

        return $query->latest('starts_at');
    }
}
