<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * Handler global de excepciones: aquí podemos mapear errores de la API externa a respuestas 4xx/5xx.
 */
class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = ['password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Podemos añadir notificaciones (Slack, email) si la API externa deja de responder.
        });
    }
}
