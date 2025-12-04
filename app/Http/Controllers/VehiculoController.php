<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use App\Services\ProfileResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador REST para vehículos.
 * Cada acción delega en ExternalApiService, que encapsula el cliente HTTP externo.
 */
class VehiculoController extends Controller
{
    public function __construct(
        private readonly ExternalApiService $externalApi,
        private readonly ProfileResolverService $profileResolver
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $vehiculos = $this->externalApi->listVehiculos($perfilId);
        return response()->json($vehiculos);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'placa' => ['required', 'regex:/^[A-Z]{3}-?\d{3}$/i'],
            'marca' => ['nullable', 'string'],
            'modelo' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
            'perfil_id' => ['nullable', 'uuid']
        ]);
        $payload['perfil_id'] = $this->profileResolver->resolve($payload['perfil_id'] ?? null);
        $vehiculo = $this->externalApi->registerVehiculo($payload);
        return response()->json($vehiculo, 201);
    }

    public function show(string $vehiculoId, Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $vehiculo = $this->externalApi->getVehiculo($vehiculoId, $perfilId);
        return response()->json($vehiculo);
    }

    public function update(string $vehiculoId, Request $request): JsonResponse
    {
        $payload = $request->validate([
            'marca' => ['nullable', 'string'],
            'modelo' => ['nullable', 'string'],
            'activo' => ['nullable', 'boolean']
        ]);
        $vehiculo = $this->externalApi->updateVehiculo($vehiculoId, $payload);
        return response()->json($vehiculo);
    }

    public function destroy(string $vehiculoId, Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $this->externalApi->deleteVehiculo($vehiculoId, $perfilId);
        return response()->json(status: 204);
    }
}
