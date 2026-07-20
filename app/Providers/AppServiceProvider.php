<?php

namespace App\Providers;

use App\Events\SubmissionStatusChanged;
use App\Events\TalkWasSubmitted;
use App\Listeners\NotifyConferenceOwnerOfSubmission;
use App\Listeners\NotifySubmitterOfStatusChange;
use App\Models\Bio;
use App\Models\Conference;
use App\Models\Talk;
use App\Policies\BioPolicy;
use App\Policies\ConferencePolicy;
use App\Policies\TalkPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::policy(Talk::class, TalkPolicy::class);
        Gate::policy(Conference::class, ConferencePolicy::class);
        Gate::policy(Bio::class, BioPolicy::class);

        Event::listen(
            TalkWasSubmitted::class,
            NotifyConferenceOwnerOfSubmission::class
        );

        Event::listen(
            SubmissionStatusChanged::class,
            NotifySubmitterOfStatusChange::class,
        );

        View::composer(['layouts.app','layouts.public.app'], function ($view) {
            $user = auth()->user();

            $view->with([
                'unreadCount' => $user
                    ? $user->unreadNotifications()->count()
                    : 0,

                'latestNotifications' => $user
                    ? $user->notifications()->latest()->limit(5)->get()
                    : collect(),
            ]);
        });
    }
}
