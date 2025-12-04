<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

/**
 * Proveedor central para enlazar servicios de la aplicación.
 * Aquí registramos singletons (ExternalApiService) y configuraciones globales (timeout).
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Configura cliente HTTP global para la API externa.
        Http::macro('externalApi', function () {
            return Http::baseUrl(config('external-api.base_url'))
                ->timeout(config('external-api.timeout'))
                ->acceptJson();
        });
    }

    public function boot(): void
    {
        // Podemos añadir hooks (por ejemplo, validar que GLOBAL_PERFIL_ID esté definido)
        if (!config('external-api.global_perfil_id')) {
            logger()->warning('GLOBAL_PERFIL_ID no está configurado, el login ciudadano fallará.');
        }
    }
}
