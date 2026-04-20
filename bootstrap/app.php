<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Résolution du tenant en tout premier, avant tous les autres middlewares
        $middleware->prepend(\App\Http\Middleware\ResolveTenant::class);

        // Alias des middlewares de l'application
        $middleware->alias([
            'super_admin'   => \App\Http\Middleware\EnsureSuperAdmin::class,
            'tenant_access' => \App\Http\Middleware\EnsureTenantAccess::class,
        ]);

        // Stripe webhook must bypass CSRF verification
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
