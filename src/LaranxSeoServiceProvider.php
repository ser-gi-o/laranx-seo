<?php

namespace Srg\LaranxSeo;

use Illuminate\Support\ServiceProvider;

class LaranxSeoServiceProvider extends ServiceProvider
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
        //migrations
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
