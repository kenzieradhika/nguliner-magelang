<?php

namespace App\Providers;

use App\Security\NativeGuard;
use Illuminate\Support\ServiceProvider;

class NativeSecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NativeGuard::class, fn () => new NativeGuard(config('security.dll')));
    }
}
