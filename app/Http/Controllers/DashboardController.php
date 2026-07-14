<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();

        $talks = $user->talks()
            ->latest()
            ->get();

        $bios = $user->bios()
            ->latest()
            ->get();

        $submissions = Talk::query()
            ->where('user_id', $user->id)
            ->whereHas('conferences')
            ->with([
                'conferences' => fn ($query) => $query->latest('conference_talk.created_at'),
            ])
            ->latest()
            ->get();

        $ownedConferences = $user->conferences()
            ->latest()
            ->get();

        $upcomingConferences = Conference::query()
            ->whereDate('cfp_starts_at', '<=', today())
            ->whereDate('cfp_ends_at', '>=', today())
            ->where('user_id', '!=', $user->id)
            ->orderBy('cfp_ends_at')
            ->get();

        return view('dashboard', compact(
            'talks',
            'bios',
            'submissions',
            'ownedConferences',
            'upcomingConferences',
        ));
    }
}
