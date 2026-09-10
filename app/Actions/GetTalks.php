<?php

namespace App\Actions;

use Illuminate\Http\Request;

final class GetTalks
{
    public function handle(Request $request)
    {
        $talks = auth()
            ->user()
            ->talks()
            ->with([
                'tags',
                'conferences' => fn($query) => $query->orderBy('starts_at'),
                'currentRevision'
            ]);

        if ($request->filled('tag')) {
            $talks->whereHas('tags', fn($query) => $query->where('slug', $request->string('tag')));
        }

        return $talks->withCount('conferences')
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();
    }
}
