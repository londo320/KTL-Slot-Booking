<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |---------------------------------------------------------------------------
        | Global middleware  (run on every request)
        |---------------------------------------------------------------------------
        |
        | $middleware->append([
        |     \App\Http\Middleware\TrustProxies::class,
        | ]);
        |
        */

        /*
        |---------------------------------------------------------------------------
        | Route-middleware aliases
        |---------------------------------------------------------------------------
        | Usage example →  Route::middleware('admin')->group(...)
        */
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
