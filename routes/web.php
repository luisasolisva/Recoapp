<?php

use Illuminate\Support\Facades\Route;

/**
 * Este archivo aloja rutas web tradicionales.
 * Para este proyecto la UI se sirve desde Ionic, por lo que dejamos una
 * única ruta informativa.
 */
Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'version' => '0.1.0',
        'docs' => url('/docs'),
    ]);
});
