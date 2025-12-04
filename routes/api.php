<?php
use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\RecorridoController;
use App\Http\Controllers\TrackingQueueController;

Route::any('lucio-proxy/{path?}', [ExternalApiController::class, 'proxy'])
    ->where('path', '.*');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vehiculos', [VehiculoController::class, 'index']);
    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show']);
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update']);
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy']);

    Route::get('/rutas', [RutaController::class, 'index']);
    Route::post('/rutas', [RutaController::class, 'store']);
    Route::get('/rutas/{ruta}', [RutaController::class, 'show']);
    Route::put('/rutas/{ruta}', [RutaController::class, 'update']);
    Route::delete('/rutas/{ruta}', [RutaController::class, 'destroy']);

    Route::get('/recorridos', [RecorridoController::class, 'index']);
    Route::post('/recorridos/iniciar', [RecorridoController::class, 'start']);
    Route::post('/recorridos/{recorrido}/finalizar', [RecorridoController::class, 'finish']);
    Route::post('/recorridos/{recorrido}/posiciones', [RecorridoController::class, 'storePosition']);
    Route::get('/recorridos/{recorrido}/posiciones', [RecorridoController::class, 'positions']);

    Route::get('/tracking/pending', [TrackingQueueController::class, 'index']);
    Route::post('/tracking/flush', [TrackingQueueController::class, 'flush']);
});
