<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Talk;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();

        $talks = $user->talks()
            ->with('currentRevision')
            ->latest()
            ->limit(4)
            ->get();

        $bios = $user->bios()
            ->latest()
            ->limit(10)
            ->get();

        $submissions = Talk::query()
            ->where('user_id', $user->id)
            ->whereHas('conferences')
            ->with([
                'conferences' => fn($query) => $query->latest('conference_talk.created_at'),
                'currentRevision'
            ])
            ->latest()
            ->get();


        $ownedConferences = $user->conferences()
            ->latest()
            ->limit(5)
            ->get();

        $upcomingConferences = Conference::query()
            ->cfpOpen()
            ->notDismissedBy($user)
            ->where('user_id', '!=', $user->id)
            ->orderBy('cfp_ends_at')
            ->limit(3)
            ->get();

        $getSubmissionStatus = function ($talk) {
            $status = $talk->conferences->first()?->pivot?->status;

            return $status?->value ?? $status;
        };

        $pendingSubmissions = $submissions->filter(
            fn ($talk) => $getSubmissionStatus($talk) === 'pending'
        );

        $acceptedSubmissions = $submissions->filter(
            fn ($talk) => $getSubmissionStatus($talk) === 'accepted'
        );

        $rejectedSubmissions = $submissions->filter(
            fn ($talk) => $getSubmissionStatus($talk) === 'rejected'
        );

        return view('dashboard', compact(
            'talks',
            'bios',
            'submissions',
            'ownedConferences',
            'upcomingConferences',
            'pendingSubmissions',
            'acceptedSubmissions',
            'rejectedSubmissions',
        ));
    }
}
