<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use App\Services\ProfileResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador de rutas para CRUD y visualización de geometrías.
 */
class RutaController extends Controller
{
    public function __construct(
        private readonly ExternalApiService $externalApi,
        private readonly ProfileResolverService $profileResolver
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        return response()->json($this->externalApi->listRutas($perfilId));
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'nombre_ruta' => ['required', 'string'],
            'perfil_id' => ['nullable', 'uuid'],
            'shape' => ['nullable', 'json'],
            'calles_ids' => ['nullable', 'array'],
            'calles_ids.*' => ['uuid'],
            'color_hex' => ['nullable', 'regex:/^#?[0-9A-F]{6}$/i']
        ]);
        $payload['perfil_id'] = $this->profileResolver->resolve($payload['perfil_id'] ?? null);
        $ruta = $this->externalApi->createRuta($payload);
        return response()->json($ruta, 201);
    }

    public function show(string $rutaId, Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $ruta = $this->externalApi->getRuta($rutaId, $perfilId);
        return response()->json($ruta);
    }

    public function update(string $rutaId, Request $request): JsonResponse
    {
        $payload = $request->validate([
            'nombre_ruta' => ['sometimes', 'string'],
            'shape' => ['nullable', 'json'],
            'calles_ids' => ['nullable', 'array'],
            'calles_ids.*' => ['uuid'],
            'color_hex' => ['nullable', 'regex:/^#?[0-9A-F]{6}$/i']
        ]);
        $ruta = $this->externalApi->updateRuta($rutaId, $payload);
        return response()->json($ruta);
    }

    public function destroy(string $rutaId, Request $request): JsonResponse
    {
        $perfilId = $this->profileResolver->resolve($request->query('perfil_id'));
        $this->externalApi->deleteRuta($rutaId, $perfilId);
        return response()->json(status: 204);
    }
}
