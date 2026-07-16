<?php

namespace App\Http\Controllers;

use App\Enum\ConferenceUserStatus;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConferenceFavoriteController extends Controller
{
    //

    public function store(Conference $conference): JsonResponse
    {
        auth()->user()->conferencesStates()->syncWithoutDetaching([
            $conference->id => ['status' => ConferenceUserStatus::FAVORITED]
        ]);
        return response()->json([
            'status' => ConferenceUserStatus::FAVORITED,
        ]);
    }

    public function destroy(Conference $conference): JsonResponse
    {
        auth()->user()->conferencesStates()->detach($conference->id);

        return response()->json([
            'status' => null,
        ]);
    }
}
