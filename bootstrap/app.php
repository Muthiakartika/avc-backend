<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    //adding auto ngok (temp)
    ->withProviders([
        \App\Providers\NgrokCorsServiceProvider::class,
    ])

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'isAdmin' => \App\Http\Middleware\AdminMiddleware::class,
            'isHr' => \App\Http\Middleware\HRMiddleware::class,
            'isEmployee' => \App\Http\Middleware\EmployeeMiddleware::class,

        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
