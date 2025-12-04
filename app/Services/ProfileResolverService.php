<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Servicio pequeño que decide qué perfil utilizar para las peticiones.
 * Si el cliente envía un perfil explícito se valida que sea UUID; de lo contrario
 * se usa un perfil global definido en .env (útil para modo ciudadano).
 */
class ProfileResolverService
{
    public function resolve(?string $perfilId): string
    {
        if ($perfilId && Str::isUuid($perfilId)) {
            return $perfilId;
        }

        $global = Config::get('external-api.global_perfil_id');
        if (!$global) {
            throw new \RuntimeException('Debes configurar GLOBAL_PERFIL_ID en el backend.');
        }

        return $global;
    }
}
