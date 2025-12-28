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
        // Added for Azure Web App deployment https://chatgpt.com/c/6941ba71-d628-8321-a010-e2ec69b400c1
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
