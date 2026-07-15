<?php

namespace App\Providers;

use App\Models\Bio;
use App\Models\Conference;
use App\Models\Talk;
use App\Policies\BioPolicy;
use App\Policies\ConferencePolicy;
use App\Policies\TalkPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
