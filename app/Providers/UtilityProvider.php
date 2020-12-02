<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UtilityProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            'utility',
            'App\Http\Components\Utility'
        );
    }
}
