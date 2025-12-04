<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controlador base de Laravel. Incluimos traits para:
 * - AuthorizesRequests: validar políticas de autorización.
 * - DispatchesJobs: lanzar jobs a la cola, útil para sincronizar tracking.
 * - ValidatesRequests: reutilizar validaciones en cualquier endpoint.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
