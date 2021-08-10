<?php

namespace App\Providers;

use App\Models;
use App\Observers;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Models\Instance::observe(Observers\InstanceObserver::class);
        Models\Project::observe(Observers\ProjectObserver::class);
        Models\User::observe(Observers\UserObserver::class);
    }
}
