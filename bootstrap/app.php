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
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'puede.consultar' => \App\Http\Middleware\PuedeConsultarMiddleware::class,
        'puede.editar' => \App\Http\Middleware\PuedeEditarMiddleware::class,
        'verificar.password.temporal' => \App\Http\Middleware\VerificarPasswordTemporal::class,
    ]);

    
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
