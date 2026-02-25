<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// quando for usar a url para mostrar
// use Illuminate\Support\Facades\URL;


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
    public function boot()
{
    // if (app()->environment('local')) {
    //     URL::forceScheme('https');
    // }
}
}
