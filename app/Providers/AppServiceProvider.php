<?php

namespace App\Providers;

use App\Listeners\SecurityEventSubscriber;
use App\View\Components\AppIcon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
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
        Event::subscribe(SecurityEventSubscriber::class);
        Event::subscribe(\App\Listeners\AdminActivitySubscriber::class);

        // BladeUI\Icons ikut terpasang bersama Filament dan mendaftarkan komponen
        // class `<x-icon>` yang menimpa komponen kustom app (sprite ng-*).
        // Daftarkan ulang pada event `booted` (pasti setelah semua package provider)
        // agar `<x-icon>` kembali memakai sprite kustom, lalu `php artisan view:clear`.
        $this->app->booted(function (): void {
            Blade::component(AppIcon::class, 'icon');
        });
    }
}
