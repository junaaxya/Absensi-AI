<?php

namespace App\Providers;

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
        // Tambahkan ini agar semua URL yang digenerate Laravel otomatis pakai HTTPS dan prefix yang benar
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
            // Jika butuh paksa root folder ke /absensi
            // \URL::forceRootUrl(config('app.url')); 
        }
    }
}
