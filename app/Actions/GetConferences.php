<?php

namespace App\Actions;

use App\Filters\ConferenceFilter;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class GetConferences
{
    public function handle(Request $request)
    {
        $user = $request->user();

        $query = Conference::query();

        if ($user) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => fn (Builder $q) =>
                $q->where('user_id', $user->id),

                'dismissedByUsers as is_dismissed' => fn (Builder $q) =>
                $q->where('user_id', $user->id),
            ]);
        }

        $query = ConferenceFilter::apply($request,$query);

        if ($user){
            return $query
                ->paginate(12)
                ->withQueryString();
        }


        $cacheKey = $this->cacheKey($request);

        return Cache::tags(['conferences'])->remember(
            $cacheKey,
            now()->addMinutes(10),
            fn () => $query
                ->paginate(12)
                ->withQueryString()
        );

    }

    private function cacheKey(Request $request): string
    {
        return 'conferences.index.'
            .'term:'.($request->input('term') ?? 'none').'.'
            .'conference_date:'.($request->input('conference_date') ?? 'none').'.'
            .'cfp_status:'.($request->input('cfp_status') ?? 'none').'.'
            .'page:'.($request->input('page', 1));
    }
}
