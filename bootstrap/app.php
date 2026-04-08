<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\TrialExpiradoMiddleware;
use App\Http\Middleware\GodMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'            => RoleMiddleware::class,
        'trial.expirado'  => TrialExpiradoMiddleware::class,
        'god'             => GodMiddleware::class,
    ]);
    $middleware->validateCsrfTokens(except: [
        'webhooks/mercadopago',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

 