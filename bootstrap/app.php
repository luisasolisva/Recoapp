<?php

use Illuminate\Foundation\Application;

/**
 * Bootstrap estándar para Laravel 11.
 * Crea la instancia principal y registra rutas y middleware.
 */

$app = new Application(dirname(__DIR__));

// Registrar el kernel HTTP y de consola
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
