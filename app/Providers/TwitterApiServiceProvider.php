<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TwitterApiServiceProvider extends ServiceProvider
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
            'apihandle',
            'App\Http\Components\ApiHandle'
        );
    }
}
