<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use App\Services\ProfileResolverService;
use App\Services\TrackingQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para gestionar recorridos y posiciones GPS.
 * Se apoya en TrackingQueueService para almacenar posiciones cuando falla la API externa.
 */
class RecorridoController extends Controller
{
    public function __construct(
        private readonly ExternalApiService $externalApi,
        private readonly ProfileResolverService $profileResolver,
        private readonly TrackingQueueService $queueService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        return response()->json($this->externalApi->listRecorridos($perfilId));
    }

    public function start(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'ruta_id' => ['required', 'uuid'],
            'vehiculo_id' => ['required', 'uuid'],
            'perfil_id' => ['nullable', 'uuid']
        ]);
        $payload['perfil_id'] = $this->profileResolver->resolve($payload['perfil_id'] ?? null);
        $recorrido = $this->externalApi->startRecorrido($payload);
        return response()->json($recorrido, 201);
    }

    public function finish(string $recorridoId): JsonResponse
    {
        $this->externalApi->finishRecorrido($recorridoId);
        return response()->json(status: 204);
    }

    public function storePosition(string $recorridoId, Request $request): JsonResponse
    {
        $payload = $request->validate([
            'lat' => ['required', 'numeric'],
            'lon' => ['required', 'numeric'],
            'perfil_id' => ['nullable', 'uuid'],
            'capturado_ts' => ['nullable', 'date']
        ]);
        $payload['perfil_id'] = $this->profileResolver->resolve($payload['perfil_id'] ?? null);

        $response = $this->externalApi->storePosition($recorridoId, $payload);

        if (!$response) {
            $this->queueService->enqueuePosition($recorridoId, $payload);
            return response()->json([
                'queued' => true,
                'message' => 'Posición almacenada localmente para reintento'
            ], 202);
        }

        return response()->json($response, 201);
    }

    public function positions(string $recorridoId, Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $posiciones = $this->externalApi->getPositions($recorridoId, $perfilId);
        return response()->json($posiciones);
    }
}
