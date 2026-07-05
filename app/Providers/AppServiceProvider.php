<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // In production behind HTTPS (e.g. https://demo.ordr.my.id/laundry/public),
        // force generated URLs/assets to https to avoid mixed-content / scheme 404s.
        // The sub-folder path itself is handled automatically: Laravel & Livewire 4
        // build URLs from the request root (scheme+host+base path), so as long as the
        // web server's document root is the app's `public/` folder, the
        // "/laundry/public" prefix is included in every generated URL.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
