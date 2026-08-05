<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'superadmin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'ng-hardening' => \App\Http\Middleware\NativeSessionGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request): void {
            $isCsrf = $e->getStatusCode() === 419
                && $e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException;

            if (! $isCsrf) {
                return;
            }

            try {
                app(\App\Services\SecurityEventService::class)->record(
                    'csrf_mismatch',
                    'Token CSRF tidak cocok (form kadaluarsa atau payload mencurigakan).',
                    ['url' => $request->fullUrl()],
                    $request,
                    'low',
                );
            } catch (\Throwable $ignored) {
                // jangan sampai perekaman error menggagalkan response
            }
        });
    })->create();
