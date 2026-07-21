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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

            $latestNotifications = $user
                ? $user->notifications()->latest()->limit(5)->get()
                : collect();

            $view->with([
                'unreadCount' => $latestNotifications->whereNull('read_at')->count(),
                'latestNotifications' => $latestNotifications,
            ]);
        });

        RateLimiter::for('restore',function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->id);
        });

        RateLimiter::for('talk-submission',function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->id);
        });

        RateLimiter::for('status-change',function (Request $request) {
            return Limit::perMinute(20)
                ->by($request->user()->id);
        });
    }
}
