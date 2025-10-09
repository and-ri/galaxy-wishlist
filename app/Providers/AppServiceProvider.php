<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

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
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        
        $socialite->extend('authentik', function ($app) use ($socialite) {
            $config = $app['config']['services.authentik'];
            return $socialite->buildProvider(AuthentikProvider::class, $config);
        });
    }
}
