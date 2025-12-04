<?php

return [
    /**
     * URL base de la API externa proporcionada.
     */
    'base_url' => env('EXTERNAL_API_BASE_URL', 'http://apirecoleccion.gonzaloandreslucio.com/api'),

    /**
     * Timeout en segundos para cortar peticiones lentas.
     */
    'timeout' => (float) env('EXTERNAL_API_TIMEOUT', 10),

    /**
     * Perfil global utilizado para endpoints ciudadanos cuando no se envía uno específico.
     */
    'global_perfil_id' => env('GLOBAL_PERFIL_ID'),
];
