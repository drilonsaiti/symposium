<?php

namespace App\Http\Controllers;

use App\Models\Bio;
use App\Models\Talk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function live(Request $request)
    {
        $validated = $request->validate([
            'term' => ['nullable', 'string', 'max:100'],
        ]);

        $term = trim($validated['term'] ?? '');

        if (mb_strlen($term) < 2) {
            return response()->json(['talks' => [], 'bios' => []]);
        }

        $talks = Talk::search($term)
            ->where('user_id', auth()->id())
            ->take(8)
            ->get()
            ->map(fn (Talk $talk) => [
                'id' => $talk->id,
                'title' => $talk->title,
                'snippet' => Str::limit($talk->currentRevision?->abstract, 120),
                'url' => route('talks.show', $talk),
            ]);

        $bios = Bio::search($term)
            ->where('user_id', auth()->id())
            ->take(8)
            ->get()
            ->map(fn (Bio $bio) => [
                'id' => $bio->id,
                'nickname' => $bio->nickname,
                'snippet' => Str::limit($bio->bio, 120),
                'url' => route('bios.show', $bio),
            ]);

        return response()->json([
            'talks' => $talks,
            'bios' => $bios,
        ]);
    }
}
