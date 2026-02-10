<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
    Vite::prefetch(concurrency: 3);

    // Force HTTPS in production
    if (app()->environment('production')) {
        // Force Laravel to generate HTTPS URLs
        URL::forceScheme('https');
        
        // Tell Laravel the request is already secure (Railway proxy)
        $this->app['request']->server->set('HTTPS', 'on');
        
        // Optional: Also set port for good measure
        $this->app['request']->server->set('SERVER_PORT', 443);
    }
}
}
