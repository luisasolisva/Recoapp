<?php

namespace App\Http\Controllers;

use App\Services\TrackingQueueService;
use Illuminate\Http\JsonResponse;

/**
 * Endpoints administrativos para consultar y disparar manualmente la cola de posiciones en espera.
 */
class TrackingQueueController extends Controller
{
    public function __construct(private readonly TrackingQueueService $queueService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->queueService->pending());
    }

    public function flush(): JsonResponse
    {
        $resultado = $this->queueService->flush();
        return response()->json($resultado);
    }
}
