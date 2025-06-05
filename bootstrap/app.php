<?php

use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\EnsureFeatureAccess;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => EnsureAdminRole::class,
            'active_subscription' => EnsureActiveSubscription::class,
            'feature_access' => EnsureFeatureAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
