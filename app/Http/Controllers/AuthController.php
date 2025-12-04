<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use App\Services\ProfileResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador responsable del flujo "login por placa".
 * Paso a paso:
 * 1. Sanitiza la placa enviada por el cliente.
 * 2. Busca la combinación (placa, perfil) en la API externa.
 * 3. Si existe, genera un token Sanctum y retorna la sesión.
 * 4. Si no existe, crea el vehículo automáticamente y repite el flujo.
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly ExternalApiService $externalApi,
        private readonly ProfileResolverService $profileResolver
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'placa' => ['required', 'regex:/^[A-Z]{3}-?\d{3}$/i'],
            'perfil_id' => ['nullable', 'uuid']
        ]);

        $placa = strtoupper(str_replace('-', '', $data['placa']));
        $perfilId = $this->profileResolver->resolve($data['perfil_id'] ?? null);

        $vehiculo = $this->externalApi->findVehiculoByPlaca($placa, $perfilId);

        if (!$vehiculo) {
            $vehiculo = $this->externalApi->registerVehiculo([
                'placa' => $placa,
                'perfil_id' => $perfilId,
                'activo' => true
            ]);
        }

        $user = \App\Models\User::firstOrCreate(
            ['placa' => $vehiculo['placa']],
            [
                'perfil_id' => $vehiculo['perfil_id'],
                'name' => $vehiculo['placa'],
                'password' => Hash::make($vehiculo['placa'])
            ]
        );

        $token = $user->createToken('driver-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'vehiculo' => $vehiculo,
            'perfil_id' => $perfilId
        ]);
    }
}
