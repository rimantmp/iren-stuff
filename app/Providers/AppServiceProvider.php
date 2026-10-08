<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if (
            request()->header('X-Forwarded-Proto') === 'https'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || str_contains(request()->getHost(), 'trycloudflare.com')
        ) {
            URL::forceScheme('https');
        }
    }
}
