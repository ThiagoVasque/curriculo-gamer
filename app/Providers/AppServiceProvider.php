<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

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
    // Força HTTPS se estiver em produção (Railway)
    if (App::environment('production') || env('RAILWAY_ENVIRONMENT')) {
        URL::forceScheme('https');
    }
}
}
