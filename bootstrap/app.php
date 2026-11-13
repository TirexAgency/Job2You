<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enregistrement des alias de middleware personnalisés
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'plan.feature' => \App\Http\Middleware\EnsurePlanFeature::class,
            'subscription.current' => \App\Http\Middleware\EnsureSubscriptionIsCurrent::class,
        ]);

        // Remplacer le middleware CSRF par notre version avec exceptions
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class, \App\Http\Middleware\PreventRequestForgery::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
